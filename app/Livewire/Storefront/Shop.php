<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Lunar\Models\Product;

class Shop extends Component
{
    public function addToCart($variantId)
    {
        $cart = \Lunar\Facades\CartSession::manager();

        $variant = \Lunar\Models\ProductVariant::find($variantId);
        
        if ($variant) {
            $cart->add($variant, 1);
            $this->dispatch('cart-updated');
            session()->flash('message', 'Product added to cart!');
        }
    }

    public function render()
    {
        $products = Product::with(['variants.prices.currency', 'thumbnail', 'urls'])
            ->latest()
            ->get();

        return view('livewire.storefront.shop', [
            'products' => $products,
        ])->layout('layouts.storefront');
    }
}
