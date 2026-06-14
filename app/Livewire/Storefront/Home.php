<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Lunar\Models\Collection;
use Lunar\Models\Product;

class Home extends Component
{
    public function addToCart($variantId)
    {
        $cart = \Lunar\Facades\CartSession::manager();

        $variant = \Lunar\Models\ProductVariant::with(['prices.currency', 'product'])->find($variantId);
        
        if ($variant) {
            $cart->add($variant, 1);
            $this->dispatch('cart-updated');
            session()->flash('message', 'Product added to cart!');

            // Trigger add_to_cart event for frontend
            $priceValue = $variant->prices->first()?->price?->value;
            $factor = 10 ** (\Lunar\Models\Currency::getDefault()?->decimal_places ?? 2);
            $priceFloat = $priceValue ? (float) ($priceValue / $factor) : 0.0;
            $eventId = 'cart_' . $variant->id . '_' . time();

            $this->dispatch('track-ecommerce-event', [
                'eventName' => 'add_to_cart',
                'eventId' => $eventId,
                'ecommerceData' => [
                    'currency' => 'PKR',
                    'value' => $priceFloat,
                    'items' => [[
                        'item_id' => $variant->sku,
                        'item_name' => $variant->product?->attr('name') ?? 'Product',
                        'price' => $priceFloat,
                        'quantity' => 1
                    ]]
                ]
            ]);
        }
    }

    public function render()
    {
        $collections = Collection::take(4)->get();
        
        $products = Product::with(['variants.prices.currency', 'thumbnail', 'urls'])
            ->latest()
            ->take(8)
            ->get();

        $posts = collect();
        if (class_exists(\LaraZeus\Sky\Models\Post::class)) {
            $posts = \LaraZeus\Sky\Models\Post::published()
                ->latest()
                ->take(3)
                ->get();
        }

        return view('livewire.storefront.home', [
            'collections' => $collections,
            'products' => $products,
            'posts' => $posts,
        ])->layout('layouts.storefront');
    }
}
