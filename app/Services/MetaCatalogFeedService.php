<?php

namespace App\Services;

use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\Currency;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MetaCatalogFeedService
{
    /**
     * Get the live public base URL for the store.
     */
    public function getBaseUrl(): string
    {
        $host = null;
        try {
            if (app()->bound('request') && request()->getHost() && !in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
                $host = request()->getSchemeAndHttpHost();
            }
        } catch (\Throwable $e) {}

        if (!$host) {
            $envAppUrl = config('app.url');
            if ($envAppUrl && !str_contains($envAppUrl, 'localhost') && !str_contains($envAppUrl, '127.0.0.1')) {
                $host = rtrim($envAppUrl, '/');
            } else {
                $host = 'https://kelvsint.com';
            }
        }

        return rtrim($host, '/');
    }

    /**
     * Format a path or URL into a guaranteed public absolute URL (replaces localhost with live domain).
     */
    public function formatAbsoluteUrl(?string $pathOrUrl): string
    {
        if (empty($pathOrUrl)) {
            return $this->getBaseUrl();
        }

        // Replace localhost or 127.0.0.1 with the live domain
        if (str_contains($pathOrUrl, '://localhost') || str_contains($pathOrUrl, '://127.0.0.1')) {
            $path = parse_url($pathOrUrl, PHP_URL_PATH);
            $query = parse_url($pathOrUrl, PHP_URL_QUERY);
            return $this->getBaseUrl() . $path . ($query ? '?' . $query : '');
        }

        if (Str::startsWith($pathOrUrl, ['http://', 'https://'])) {
            return $pathOrUrl;
        }

        return $this->getBaseUrl() . '/' . ltrim($pathOrUrl, '/');
    }

    /**
     * Fetch all eligible products and transform them into standardized Meta catalog items.
     *
     * @return Collection
     */
    public function getCatalogItems(): Collection
    {
        $defaultCurrency = Currency::getDefault();
        $decimalPlaces = $defaultCurrency?->decimal_places ?? 0;
        $factor = 10 ** $decimalPlaces;
        $currencyCode = $defaultCurrency?->code ?? 'PKR';

        $products = Product::with([
            'variants.prices.currency',
            'urls',
            'media',
            'collections'
        ])->get();

        $items = collect();

        foreach ($products as $product) {
            $productName = (string) ($product->attr('name') ?? 'KELVS Product');
            $rawDescription = (string) ($product->attr('description') ?? '');
            
            // Clean description: strip tags, remove extra whitespaces and newlines
            $cleanDescription = Str::of(strip_tags($rawDescription))
                ->replaceMatches('/\s+/', ' ')
                ->trim()
                ->limit(5000, '...');

            if ($cleanDescription->isEmpty()) {
                $cleanDescription = Str::of($productName . ' by KELVS. Dermatologically formulated premium skincare and personal care.')
                    ->limit(5000);
            }

            // Canonical product URL
            $slug = $product->urls->first()?->slug;
            $productUrl = $slug ? $this->formatAbsoluteUrl('/products/' . $slug) : $this->formatAbsoluteUrl('/shop');

            // Category determination
            $collectionName = $product->collections->first()?->attr('name') ?? 'Skincare';
            $isHairCare = Str::contains(strtolower($collectionName . ' ' . $productName), ['hair', 'shampoo', 'masque', 'keratin', 'scalp']);
            
            $googleCategory = $isHairCare 
                ? 'Health & Beauty > Personal Care > Hair Care'
                : 'Health & Beauty > Personal Care > Cosmetics > Skin Care';

            $fbCategory = $isHairCare
                ? 'Health & Beauty > Personal Care > Hair Care'
                : 'Health & Beauty > Personal Care > Cosmetics > Skin Care';

            // Primary Image extraction
            $primaryMedia = $product->getMedia('images')->first(fn ($media) => $media->getCustomProperty('primary') === true)
                ?? $product->getMedia('images')->first();

            foreach ($product->variants as $variant) {
                $sku = trim((string) $variant->sku);
                $itemId = !empty($sku) ? $sku : 'KELVS-PROD-' . $product->id . '-' . $variant->id;

                // Title: append variant title if multiple variants exist
                $variantName = $variant->attr('name');
                $title = ($product->variants->count() > 1 && !empty($variantName) && $variantName !== $productName)
                    ? "{$productName} - {$variantName}"
                    : $productName;

                // Price calculation
                $priceRecord = $variant->prices->first();
                $priceVal = $priceRecord?->price?->value ?? 0;
                $comparePriceVal = $priceRecord?->compare_price?->value ?? null;

                $formattedPriceNum = number_format($priceVal / $factor, 2, '.', '');
                $priceString = "{$formattedPriceNum} {$currencyCode}";

                $salePriceString = '';
                if ($comparePriceVal && $comparePriceVal > $priceVal) {
                    // In Lunar, compare_price is original (higher), and price is active discounted price
                    $originalPriceNum = number_format($comparePriceVal / $factor, 2, '.', '');
                    $priceString = "{$originalPriceNum} {$currencyCode}";
                    $salePriceString = "{$formattedPriceNum} {$currencyCode}";
                }

                // Inventory & Availability
                $stock = (int) ($variant->stock ?? 100);
                $availability = ($stock > 0 || ($variant->purchasable ?? 'always') === 'always') ? 'in stock' : 'out of stock';
                $quantityToSell = max(1, $stock);

                // Image link resolution
                $rawImageUrl = null;
                try {
                    if (method_exists($variant, 'images') && $variant->relationLoaded('images')) {
                        $variantMedia = $variant->images->first();
                        if ($variantMedia && method_exists($variantMedia, 'getUrl')) {
                            $rawImageUrl = $variantMedia->getUrl();
                        }
                    }
                } catch (\Throwable $e) {
                    $rawImageUrl = null;
                }

                if (!$rawImageUrl && $primaryMedia && method_exists($primaryMedia, 'getUrl')) {
                    $rawImageUrl = $primaryMedia->getUrl();
                }

                $imageUrl = $this->resolveMetaCompliantImageUrl($rawImageUrl, $sku);

                $items->push([
                    'id'                          => $itemId,
                    'title'                       => Str::limit($title, 195),
                    'description'                 => (string) $cleanDescription,
                    'availability'                => $availability,
                    'condition'                   => 'new',
                    'link'                        => $productUrl,
                    'image_link'                  => $imageUrl,
                    'brand'                       => 'KELVS',
                    'price'                       => $priceString,
                    'google_product_category'     => $googleCategory,
                    'fb_product_category'         => $fbCategory,
                    'quantity_to_sell_on_facebook'=> $quantityToSell,
                    'sale_price'                  => $salePriceString,
                    'sale_price_effective_date'   => '',
                    'item_group_id'               => 'product_' . $product->id,
                    'gender'                      => 'unisex',
                    'color'                       => '',
                    'size'                        => $variantName ?: '',
                    'age_group'                   => 'adult',
                    'material'                    => '',
                    'pattern'                     => '',
                    'shipping'                    => 'PK::Standard:0.00 PKR',
                    'shipping_country'            => 'PK',
                    'shipping_service'            => 'Standard',
                    'shipping_price'              => '0.00 PKR',
                    'shipping_weight'             => '250 g',
                    'offer_disclaimer'            => 'Valid while supplies last.',
                    'offer_disclaimer_url'        => $this->formatAbsoluteUrl('/terms-and-conditions'),
                    'gtin'                        => $variant->gtin ?? '',
                ]);
            }
        }

        return $items;
    }

    /**
     * Generate Meta Catalog CSV matching Meta's template specification.
     *
     * @return string
     */
    public function generateCsv(): string
    {
        $items = $this->getCatalogItems();

        $headers = [
            'id',
            'title',
            'description',
            'availability',
            'condition',
            'link',
            'image_link',
            'brand',
            'price',
            'google_product_category',
            'fb_product_category',
            'quantity_to_sell_on_facebook',
            'sale_price',
            'sale_price_effective_date',
            'item_group_id',
            'gender',
            'color',
            'size',
            'age_group',
            'material',
            'pattern',
            'shipping',
            'shipping_weight',
            'offer_disclaimer',
            'offer_disclaimer_url',
            'gtin'
        ];

        $output = fopen('php://temp', 'r+');

        // Write header row
        fputcsv($output, $headers);

        // Write data rows
        foreach ($items as $item) {
            $row = [];
            foreach ($headers as $header) {
                $row[] = $item[$header] ?? '';
            }
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Generate standard Meta / Google Merchant RSS 2.0 XML Feed.
     *
     * @return string
     */
    public function generateXml(): string
    {
        $items = $this->getCatalogItems();
        $storeUrl = $this->getBaseUrl();
        $storeName = 'KELVS';

        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('  ');

        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('rss');
        $xml->writeAttribute('version', '2.0');
        $xml->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');

        $xml->startElement('channel');
        $xml->writeElement('title', $storeName . ' Product Catalog');
        $xml->writeElement('link', $storeUrl);
        $xml->writeElement('description', 'Automated Meta Catalog Feed for ' . $storeName);

        foreach ($items as $item) {
            $xml->startElement('item');
            
            $xml->writeElement('g:id', $item['id']);
            $xml->writeElement('g:title', $item['title']);
            $xml->writeElement('g:description', $item['description']);
            $xml->writeElement('g:link', $item['link']);
            $xml->writeElement('g:image_link', $item['image_link']);
            $xml->writeElement('g:brand', $item['brand']);
            $xml->writeElement('g:condition', $item['condition']);
            $xml->writeElement('g:availability', $item['availability']);
            $xml->writeElement('g:price', $item['price']);

            if (!empty($item['sale_price'])) {
                $xml->writeElement('g:sale_price', $item['sale_price']);
            }

            if (!empty($item['item_group_id'])) {
                $xml->writeElement('g:item_group_id', $item['item_group_id']);
            }

            if (!empty($item['google_product_category'])) {
                $xml->writeElement('g:google_product_category', $item['google_product_category']);
            }

            if (!empty($item['fb_product_category'])) {
                $xml->writeElement('g:fb_product_category', $item['fb_product_category']);
            }

            if (!empty($item['gender'])) {
                $xml->writeElement('g:gender', $item['gender']);
            }

            if (!empty($item['age_group'])) {
                $xml->writeElement('g:age_group', $item['age_group']);
            }

            if (!empty($item['shipping_country']) || !empty($item['shipping_price'])) {
                $xml->startElement('g:shipping');
                $xml->writeElement('g:country', $item['shipping_country'] ?? 'PK');
                $xml->writeElement('g:service', $item['shipping_service'] ?? 'Standard');
                $xml->writeElement('g:price', $item['shipping_price'] ?? '0.00 PKR');
                $xml->endElement(); // </g:shipping>
            }

            $xml->endElement(); // </item>
        }

        $xml->endElement(); // </channel>
        $xml->endElement(); // </rss>

        return $xml->outputMemory();
    }

    /**
     * Resolve image URL to guaranteed Meta-compliant format (JPG or PNG).
     * Automatically converts WebP images or selects existing JPG/PNG siblings.
     */
    protected function resolveMetaCompliantImageUrl(?string $rawUrl, string $sku): string
    {
        if (empty($rawUrl)) {
            $fallbackPath = match($sku) {
                'KELVS-CLEAN-01' => '/images/cleanser.png',
                'KELVS-NIAC-01'  => '/images/niacinamide.png',
                'KELVS-BHA-01'   => '/images/bha.png',
                'KELVS-HYA-01'   => '/images/hyaluronic.png',
                'KELVS-CER-01'   => '/images/ceramide.png',
                'KELVS-SPF-01'   => '/images/sunshield.png',
                default          => '/images/hero_lifestyle.png'
            };
            return $this->formatAbsoluteUrl($fallbackPath);
        }

        $parsedPath = parse_url($rawUrl, PHP_URL_PATH);
        $decodedPath = urldecode((string) $parsedPath);
        $extension = strtolower(pathinfo($decodedPath, PATHINFO_EXTENSION));

        // If already JPG/JPEG/PNG/GIF, return formatted absolute URL
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return $this->formatAbsoluteUrl($rawUrl);
        }

        // If it's WebP, convert or find JPG/PNG replacement
        if ($extension === 'webp') {
            $localPath = public_path(ltrim($decodedPath, '/\\'));
            if (file_exists($localPath)) {
                $dir = dirname($localPath);
                $filenameWithoutExt = pathinfo($localPath, PATHINFO_FILENAME);

                // 1. Check for existing JPG / PNG sibling in the same folder
                foreach (['jpg', 'jpeg', 'png'] as $altExt) {
                    $altPath = $dir . DIRECTORY_SEPARATOR . $filenameWithoutExt . '.' . $altExt;
                    if (file_exists($altPath)) {
                        $altRelativePath = str_replace(public_path(), '', $altPath);
                        $altRelativePath = str_replace('\\', '/', $altRelativePath);
                        // Encode URL segments properly (handling +, spaces, etc.)
                        $segments = array_map('rawurlencode', explode('/', ltrim($altRelativePath, '/')));
                        return $this->formatAbsoluteUrl(implode('/', $segments));
                    }
                }

                // 2. Convert WebP to JPG on the fly and cache in public/feeds/images
                try {
                    $cacheDir = public_path('feeds/images');
                    if (!file_exists($cacheDir)) {
                        mkdir($cacheDir, 0755, true);
                    }

                    $hashName = md5($decodedPath) . '.jpg';
                    $cachedJpgPath = $cacheDir . DIRECTORY_SEPARATOR . $hashName;

                    if (!file_exists($cachedJpgPath) && function_exists('imagecreatefromwebp')) {
                        $img = @imagecreatefromwebp($localPath);
                        if ($img) {
                            $w = imagesx($img);
                            $h = imagesy($img);
                            $bg = imagecreatetruecolor($w, $h);
                            $white = imagecolorallocate($bg, 255, 255, 255);
                            imagefilledrectangle($bg, 0, 0, $w, $h, $white);
                            imagecopy($bg, $img, 0, 0, 0, 0, $w, $h);
                            imagejpeg($bg, $cachedJpgPath, 92);
                            imagedestroy($img);
                            imagedestroy($bg);
                        }
                    }

                    if (file_exists($cachedJpgPath)) {
                        return $this->formatAbsoluteUrl('feeds/images/' . $hashName);
                    }
                } catch (\Throwable $e) {
                    // Fallback to rawUrl
                }
            }
        }

        return $this->formatAbsoluteUrl($rawUrl);
    }
}
