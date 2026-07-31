<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lunar\Models\Product;
use Lunar\Models\Currency;

class GenerateMetaCatalog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meta:generate-catalog {--domain= : Custom base URL for product links}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Generate Meta (Facebook) Product Catalog CSV feed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customDomain = $this->option('domain');
        $configUrl = config('app.url');

        if (!empty($customDomain)) {
            $baseUrl = rtrim($customDomain, '/');
        } elseif (!empty($configUrl) && $configUrl !== 'http://localhost:8000' && $configUrl !== 'http://localhost') {
            $baseUrl = rtrim($configUrl, '/');
        } else {
            $baseUrl = 'https://kelvsint.com';
        }

        $this->info("Using Base URL: {$baseUrl}");

        $csvContent = self::generateCsvContent($baseUrl);

        // 1. Root CSV
        $rootPath = base_path('catalog_products.csv');
        file_put_contents($rootPath, $csvContent);
        $this->info("Updated root CSV: {$rootPath}");

        // 2. Public CSVs (for Nginx/webserver direct static file serving)
        $publicDir = public_path();
        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0755, true);
        }

        $publicMetaPath = public_path('meta-catalog.csv');
        file_put_contents($publicMetaPath, $csvContent);
        $this->info("Updated public CSV feed: {$publicMetaPath}");

        $publicCatalogPath = public_path('catalog_products.csv');
        file_put_contents($publicCatalogPath, $csvContent);
        $this->info("Updated public CSV feed: {$publicCatalogPath}");

        $this->info("Meta catalog generated successfully!");
        return 0;
    }

    /**
     * Helper method to generate the CSV string content.
     */
    public static function generateCsvContent(?string $baseUrl = null): string
    {
        if (empty($baseUrl)) {
            $configUrl = config('app.url');
            $baseUrl = (!empty($configUrl) && $configUrl !== 'http://localhost:8000' && $configUrl !== 'http://localhost')
                ? rtrim($configUrl, '/')
                : 'https://kelvsint.com';
        }

        $currency = Currency::getDefault();
        $factor = 10 ** ($currency?->decimal_places ?? 0);
        $currencyCode = strtoupper($currency?->code ?? 'PKR');

        $products = Product::with([
            'variants.prices.currency',
            'urls',
            'media',
            'collections',
            'brand'
        ])->where('status', 'published')->get();

        $stream = fopen('php://temp', 'r+');

        // Comment header from standard Meta catalog specification
        $commentHeader = "# Required | A unique content ID for the item. Use the item's SKU if you can. Each content ID must appear only once in your catalog. To run dynamic ads this ID must exactly match the content ID for the same item in your Meta Pixel code. Character limit: 100,# Required | A specific and relevant title for the item. See title specifications: https://www.facebook.com/business/help/2104231189874655 Character limit: 200,# Required | A short and relevant description of the item. Include specific or unique product features like material or color. Use plain text and don't enter text in all capital letters. See description specifications: https://www.facebook.com/business/help/2302017289821154 Character limit: 9999,# Required | The current availability of the item. | Supported values: in stock; out of stock,# Required | The current condition of the item. | Supported values: new; used,# Required | The price of the item. Format the price as a number followed by the 3-letter currency code (ISO 4217 standards). Use a period (.) as the decimal point; don't use a comma.,# Required | The URL of the specific product page where people can buy the item.,# Required | The URL for the main image of your item. Images must be in a supported format (JPG/GIF/PNG) and at least 500 x 500 pixels.,# Required | The brand name of the item. Character limit: 100.,# Optional | The Google product category for the item. Learn more about product categories: https://www.facebook.com/business/help/526764014610932.,# Optional | The Facebook product category for the item. Learn more about product categories: https://www.facebook.com/business/help/526764014610932.,# Optional | The quantity of this item you have to sell on Facebook and Instagram with checkout. Must be 1 or higher or the item won't be buyable,# Optional | The discounted price of the item if it's on sale. Format the price as a number followed by the 3-letter currency code (ISO 4217 standards). Use a period (.) as the decimal point; don't use a comma. A sale price is required if you want to use an overlay for discounted prices.,# Optional | The time range for your sale period. Includes the date and time/time zone when your sale starts and ends. If this field is blank any items with a sale_price remain on sale until you remove the sale price. Use this format: YYYY-MM-DDT23:59+00:00/YYYY-MM-DDT23:59+00:00. Enter the start date as YYYY-MM-DD. Enter a 'T'. Enter the start time in 24-hour format (00:00 to 23:59) followed by the UTC time zone (-12:00 to +14:00). Enter '/' and then repeat the same format for your end date and time. The example row below uses PST time zone (-08:00).,# Optional | Use this field to create variants of the same item. Enter the same group ID for all variants within a group. Learn more about variants: https://www.facebook.com/business/help/2256580051262113 Character limit: 100.,# Optional | The gender of a person that the item is targeted towards. | Supported values: female; male; unisex,# Optional | The color of the item. Use one or more words to describe the color. Don't use a hex code. Character limit: 200.,# Optional | The size of the item written as a word or abbreviation or number. For example: small; XL; 12. Character limit: 200.,# Optional | The age group that the item is targeted towards. | Supported values: adult; all ages; infant; kids; newborn; teen; toddler,# Optional | The material the item is made from; such as cotton; denim or leather. Character limit: 200.,# Optional | The pattern or graphic print on the item. Character limit: 100.,# Optional | Shipping details for the item. Format as Country:Region:Service:Price. Include the 3-letter ISO 4217 currency code in the price. Enter the price as 0.0 to use the free shipping overlay in your ads. Use a semi-colon ';' or a comma \";\" to separate multiple shipping details for different regions or countries. Only people in the specified region or country will see shipping details for that region or country. You can leave out the region (keep the double '::') if your shipping details are the same for an entire country.,# Optional | The shipping weight of the item. Include the unit of measurement (lb/oz/g/kg).,# Optional | Legal disclaimer text for product offers. This text provides important legal or regulatory information that must be displayed with the product offer. For example: \"Valid while supplies last. Terms and conditions apply.\",# Optional | URL linking to the full disclaimer text. This provides a link to a page containing the complete disclaimer information for the product offer. For example: \"https://example.com/terms-and-conditions\",# Optional | The URL for a video of your product. Link should be a videos file on a file hosting website; not a video player. Videos must be in a supported format (.3g2; .3gp; .3gpp; .asf; .avi; .dat; .divx; .dv; .f4v; .flv; .gif; .m2ts; .m4v; .mkv; .mod; .mov; .mp4; .mpe; .mpeg; .mpeg4; .mpg; .mts; .nsv; .ogm; .ogv; .qt; .tod; .ts; .vob or .wmv).,# Optional | The URL for a video of your product. Link should be a videos file on a file hosting website; not a video player. Videos must be in a supported format (.3g2; .3gp; .3gpp; .asf; .avi; .dat; .divx; .dv; .f4v; .flv; .gif; .m2ts; .m4v; .mkv; .mod; .mov; .mp4; .mpe; .mpeg; .mpeg4; .mpg; .mts; .nsv; .ogm; .ogv; .qt; .tod; .ts; .vob or .wmv).,# Optional | The item’s Global Trade Item Number (GTIN). Recommended to help classify the item. May appear on the barcode; packaging or book cover. Only provide GTIN if you’re sure it’s correct. GTIN types include UPC (12 digits); EAN (13 digits); JAN (8 or 13 digits); ISBN (13 digits) or ITF-14 (14 digits),# Optional | Add labels to products to help filter them into product sets. Max characters: 110 per label; 5000 labels per product,# Optional | Add labels to products to help filter them into product sets. Max characters: 110 per label; 5000 labels per product,# Optional | Describe the fashion style of this item.\n";

        fwrite($stream, $commentHeader);

        // Meta Catalog CSV Header Row
        $headers = [
            'id',
            'title',
            'description',
            'availability',
            'condition',
            'price',
            'link',
            'image_link',
            'brand',
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
            'video[0].url',
            'video[0].tag[0]',
            'gtin',
            'product_tags[0]',
            'product_tags[1]',
            'style[0]'
        ];

        fputcsv($stream, $headers);

        foreach ($products as $product) {
            $slug = $product->urls->first(fn($u) => $u->is_default)?->slug ?? $product->urls->first()?->slug;
            if (!$slug) {
                continue;
            }

            $productLink = $baseUrl . '/products/' . $slug;

            // Main Media / Image (Ensure JPEG format for Meta Catalog compatibility)
            $mainMedia = $product->media->first();
            $imageLink = '';
            if ($mainMedia) {
                self::ensureJpgImageExists($mainMedia);
                $imageLink = $mainMedia->getUrl();
                if (preg_match('/\.webp$/i', $imageLink)) {
                    $imageLink = preg_replace('/\.webp$/i', '.jpg', $imageLink);
                }
            }

            if (str_starts_with($imageLink, 'http://localhost:8000') || str_starts_with($imageLink, 'http://localhost')) {
                $imageLink = preg_replace('/^http:\/\/localhost(:8000)?/', $baseUrl, $imageLink);
            } elseif (str_starts_with($imageLink, '/')) {
                $imageLink = $baseUrl . $imageLink;
            }

            $brand = $product->brand?->name ?? 'KELVS';
            $googleCategory = 'Health & Beauty > Personal Care > Skincare';
            $fbCategory = 'Health & Beauty > Personal Care > Skincare';

            $rawDescription = $product->attr('description') ?? '';
            $cleanDescription = trim(preg_replace('/\s+/', ' ', strip_tags($rawDescription)));
            if (empty($cleanDescription)) {
                $cleanDescription = (string) $product->attr('name');
            }

            foreach ($product->variants as $variant) {
                $priceObj = $variant->prices->first();
                $priceVal = $priceObj ? ($priceObj->price->value / $factor) : 0;
                $compareVal = ($priceObj && $priceObj->compare_price) ? ($priceObj->compare_price->value / $factor) : null;

                $hasDiscount = $compareVal && ($compareVal > $priceVal);

                if ($hasDiscount) {
                    $priceStr = sprintf('%.2f %s', $compareVal, $currencyCode);
                    $salePriceStr = sprintf('%.2f %s', $priceVal, $currencyCode);
                } else {
                    $priceStr = sprintf('%.2f %s', $priceVal, $currencyCode);
                    $salePriceStr = '';
                }

                $availability = ($variant->stock > 0 || $variant->purchasable === 'always') ? 'in stock' : 'out of stock';
                $quantity = $variant->stock > 0 ? $variant->stock : 100;
                $sku = $variant->sku ?: ('KELVS-VAR-' . $variant->id);
                
                $variantName = $variant->attr('name');
                $title = (string) $product->attr('name');
                if (!empty($variantName) && $variantName !== 'N/A') {
                    $title .= ' - ' . $variantName;
                }

                $row = [
                    $sku,                               // id
                    $title,                             // title
                    $cleanDescription,                  // description
                    $availability,                      // availability
                    'new',                              // condition
                    $priceStr,                          // price
                    $productLink,                       // link
                    $imageLink,                         // image_link
                    $brand,                             // brand
                    $googleCategory,                    // google_product_category
                    $fbCategory,                        // fb_product_category
                    $quantity,                          // quantity_to_sell_on_facebook
                    $salePriceStr,                      // sale_price
                    '',                                 // sale_price_effective_date
                    (string) $product->id,              // item_group_id
                    'unisex',                           // gender
                    '',                                 // color
                    '',                                 // size
                    'adult',                            // age_group
                    '',                                 // material
                    '',                                 // pattern
                    '',                                 // shipping
                    '',                                 // shipping_weight
                    '',                                 // offer_disclaimer
                    '',                                 // offer_disclaimer_url
                    '',                                 // video[0].url
                    '',                                 // video[0].tag[0]
                    '',                                 // gtin
                    'skincare',                         // product_tags[0]
                    'kelvs',                            // product_tags[1]
                    ''                                  // style[0]
                ];

                fputcsv($stream, $row);
            }
        }

        rewind($stream);
        $csvContent = stream_get_contents($stream);
        fclose($stream);

        return $csvContent;
    }

    /**
     * Helper to ensure a JPEG version of the Spatie Media item exists on disk.
     */
    protected static function ensureJpgImageExists($media): void
    {
        try {
            $path = method_exists($media, 'getPath') ? $media->getPath() : null;
            if (!$path || !file_exists($path)) {
                return;
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext === 'webp') {
                $jpgPath = preg_replace('/\.webp$/i', '.jpg', $path);
                if (!file_exists($jpgPath)) {
                    @ini_set('memory_limit', '512M');
                    $img = @imagecreatefromwebp($path);
                    if ($img) {
                        $width = imagesx($img);
                        $height = imagesy($img);
                        $bg = imagecreatetruecolor($width, $height);
                        $white = imagecolorallocate($bg, 255, 255, 255);
                        imagefill($bg, 0, 0, $white);
                        imagecopy($bg, $img, 0, 0, 0, 0, $width, $height);

                        imagejpeg($bg, $jpgPath, 92);
                        imagedestroy($img);
                        imagedestroy($bg);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore image conversion failures gracefully
        }
    }
}
