<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Facades\CartSession;
use App\Models\Review;

class ProductShow extends Component
{
    public $dataLayerPayload = null;

    public $slug;
    public $variantId;
    public $quantity = 1;

    // Review Form State
    public $newRating = 5;
    public $newName = '';
    public $newTitle = '';
    public $newComment = '';

    public function mount($slug)
    {
        $this->slug = $slug;
        
        $product = Product::whereHas('urls', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->firstOrFail();
        
        // Default to the first variant
        $this->variantId = $product->variants->first()?->id;

        // Autofill name if authenticated
        if (auth()->check()) {
            $this->newName = auth()->user()->name;
        }

        // Trigger view_item ecommerce event
        $activeVariant = $product->variants->first();
        if ($activeVariant) {
            $priceValue = $activeVariant->prices->first()?->price?->value;
            $factor = 10 ** (\Lunar\Models\Currency::getDefault()?->decimal_places ?? 0);
            $priceFloat = $priceValue ? (float) ($priceValue / $factor) : 0.0;
            $eventId = 'view_' . $activeVariant->id . '_' . time();
            $category = $product->collections()->first()?->attr('name') ?? 'Skincare';
            $variantName = $activeVariant->attr('name') ?? $activeVariant->sku;
            
            $this->dataLayerPayload = [
                'eventName' => 'view_item',
                'eventId' => $eventId,
                'ecommerceData' => [
                    'currency' => 'PKR',
                    'value' => $priceFloat,
                    'items' => [[
                        'item_id'        => $activeVariant->sku,
                        'item_name'      => $product->attr('name'),
                        'item_brand'     => 'KELVS',
                        'item_category'  => $category,
                        'item_variant'   => $variantName,
                        'item_list_name' => 'Product Page',
                        'price'          => $priceFloat,
                        'quantity'       => 1,
                    ]]
                ]
            ];
        }
    }

    #[Computed]
    public function product()
    {
        return Product::whereHas('urls', function ($query) {
            $query->where('slug', $this->slug);
        })->firstOrFail();
    }

    #[Computed]
    public function activeVariant()
    {
        return ProductVariant::with(['prices.currency', 'images'])->find($this->variantId);
    }

    #[Computed]
    public function reviews()
    {
        return Review::where('product_id', $this->product->id)
            ->where('is_approved', true)
            ->latest()
            ->get();
    }

    #[Computed]
    public function averageRating()
    {
        $avg = Review::where('product_id', $this->product->id)
            ->where('is_approved', true)
            ->avg('rating');

        return $avg ? round($avg, 1) : 0;
    }

    #[Computed]
    public function ratingDistribution()
    {
        $total = $this->reviews->count();
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        if ($total > 0) {
            $counts = Review::where('product_id', $this->product->id)
                ->where('is_approved', true)
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating');

            foreach ($distribution as $rating => &$percentage) {
                $count = $counts->get($rating, 0);
                $percentage = round(($count / $total) * 100);
            }
        }

        return $distribution;
    }

    public function submitReview()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newRating' => 'required|integer|min:1|max:5',
            'newTitle' => 'nullable|string|max:255',
            'newComment' => 'required|string|min:5|max:1000',
        ], [
            'newName.required' => 'Please enter your name.',
            'newRating.required' => 'Please select a star rating.',
            'newComment.required' => 'Please write a review comment.',
            'newComment.min' => 'Your review must be at least 5 characters.',
        ]);

        Review::create([
            'product_id' => $this->product->id,
            'customer_name' => $this->newName,
            'rating' => $this->newRating,
            'title' => $this->newTitle,
            'comment' => $this->newComment,
            'is_approved' => false, // Require admin approval
        ]);

        session()->flash('review_message', 'Thank you! Your review has been submitted and is pending moderation.');

        $this->reset(['newRating', 'newTitle', 'newComment']);
        $this->newRating = 5;
        
        if (auth()->check()) {
            $this->newName = auth()->user()->name;
        }
    }

    public function addToCart()
    {
        $this->validate([
            'quantity' => 'required|integer|min:1',
            'variantId' => 'required|exists:lunar_product_variants,id',
        ]);

        $cart = CartSession::manager();
        
        $variant = ProductVariant::find($this->variantId);
        
        if ($variant) {
            $cart->add($variant, $this->quantity);
            session()->flash('message', 'Product successfully added to your cart!');

            // Flash dataLayer event to session to survive the redirect
            $priceValue = $variant->prices->first()?->price?->value;
            $factor = 10 ** (\Lunar\Models\Currency::getDefault()?->decimal_places ?? 0);
            $priceFloat = $priceValue ? (float) ($priceValue / $factor) : 0.0;
            $eventId = 'cart_' . $variant->id . '_' . time();
            $category = $variant->product?->collections()->first()?->attr('name') ?? 'Skincare';
            $variantName = $variant->attr('name') ?? $variant->sku;

            session()->flash('dataLayerEvent', [
                'eventName' => 'add_to_cart',
                'eventId'   => $eventId,
                'userData'  => [],
                'ecommerceData' => [
                    'currency' => 'PKR',
                    'value'    => $priceFloat * $this->quantity,
                    'items'    => [[
                        'item_id'        => $variant->sku,
                        'item_name'      => $this->product->attr('name'),
                        'item_brand'     => 'KELVS',
                        'item_category'  => $category,
                        'item_variant'   => $variantName,
                        'item_list_name' => 'Product Page',
                        'price'          => $priceFloat,
                        'quantity'       => (int) $this->quantity,
                    ]],
                ],
            ]);
        } else {
            session()->flash('error', 'Selected variant not found.');
        }
        
        return redirect()->to('/cart');
    }

    public function render()
    {
        $product = $this->product;

        // ── Resolve each SEO field with smart fallbacks ─────────────────────
        $productName = $product->attr('name') ?? '';

        $seoTitle = $product->attr('seo_title')
            ?? ($productName ? "{$productName} | KELVS Skin" : config('app.name', 'KELVS Skin'));

        $seoDescription = $product->attr('seo_description')
            ?? strip_tags((string) ($product->attr('description') ?? ''));
        $seoDescription = mb_strimwidth($seoDescription, 0, 160, '…');

        $seoKeywords = $product->attr('seo_keywords') ?? '';

        // Prefer an explicitly set canonical URL; fall back to the current URL
        $canonicalUrl = $product->attr('canonical_url')
            ?? url('/products/' . $this->slug);

        // Product URL for Open Graph
        $productUrl = url('/products/' . $this->slug);

        return view('livewire.storefront.product-show', [
            'product'        => $product,
            'activeVariant'  => $this->activeVariant,
            'seoTitle'       => $seoTitle,
            'seoDescription' => $seoDescription,
            'seoKeywords'    => $seoKeywords,
            'canonicalUrl'   => $canonicalUrl,
            'productUrl'     => $productUrl,
        ])->layout('layouts.storefront', [
            'seoTitle'       => $seoTitle,
            'seoDescription' => $seoDescription,
            'seoKeywords'    => $seoKeywords,
            'canonicalUrl'   => $canonicalUrl,
            'productUrl'     => $productUrl,
            'productName'    => $productName,
        ]);
    }
}

