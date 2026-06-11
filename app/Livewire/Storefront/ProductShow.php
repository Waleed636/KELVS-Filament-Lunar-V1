<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Facades\CartSession;

class ProductShow extends Component
{
    public $slug;
    public $variantId;
    public $quantity = 1;

    public function mount($slug)
    {
        $this->slug = $slug;
        
        $product = Product::whereHas('urls', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->firstOrFail();
        
        // Default to the first variant
        $this->variantId = $product->variants->first()?->id;
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
