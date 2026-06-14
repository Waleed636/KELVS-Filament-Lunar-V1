<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Lunar\Models\Collection;
use Lunar\Models\Product;

class Home extends Component
{
    public $dataLayerPayload = null;

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
            $factor = 10 ** (\Lunar\Models\Currency::getDefault()?->decimal_places ?? 0);
            $priceFloat = $priceValue ? (float) ($priceValue / $factor) : 0.0;
            $eventId = 'cart_' . $variant->id . '_' . time();
            $category = $variant->product?->collections()->first()?->attr('name') ?? 'Skincare';

            $this->dispatch('track-ecommerce-event', [
                'eventName' => 'add_to_cart',
                'eventId'   => $eventId,
                'ecommerceData' => [
                    'currency' => 'PKR',
                    'value'    => $priceFloat,
                    'items'    => [[
                        'item_id'        => $variant->sku,
                        'item_name'      => $variant->product?->attr('name') ?? 'Product',
                        'item_brand'     => 'KELVS',
                        'item_category'  => $category,
                        'item_list_name' => 'Homepage Featured',
                        'price'          => $priceFloat,
                        'quantity'       => 1,
                    ]],
                ],
            ]);
        }
    }

    protected $productsList;

    public function mount()
    {
        $factor = 10 ** (\Lunar\Models\Currency::getDefault()?->decimal_places ?? 0);
        $this->productsList = Product::with(['variants.prices.currency', 'thumbnail', 'urls', 'collections'])
            ->latest()
            ->take(8)
            ->get();

        // Build view_item_list payload for homepage featured section
        $listItems = [];
        foreach ($this->productsList as $index => $product) {
            $variant    = $product->variants->first();
            $priceValue = $variant?->prices->first()?->price?->value;
            $priceFloat = $priceValue ? (float) ($priceValue / $factor) : 0.0;
            $category   = $product->collections->first()?->attr('name') ?? 'Skincare';

            $listItems[] = [
                'item_id'        => $variant?->sku ?? (string) $product->id,
                'item_name'      => $product->attr('name'),
                'item_brand'     => 'KELVS',
                'item_category'  => $category,
                'item_list_id'   => 'homepage_featured',
                'item_list_name' => 'Homepage Featured',
                'index'          => $index,
                'price'          => $priceFloat,
                'quantity'       => 1,
            ];
        }

        if (!empty($listItems)) {
            $this->dataLayerPayload = [
                'eventName' => 'view_item_list',
                'eventId'   => 'vil_home_' . time(),
                'ecommerceData' => [
                    'item_list_id'   => 'homepage_featured',
                    'item_list_name' => 'Homepage Featured',
                    'items'          => $listItems,
                ],
            ];
        }
    }

    public function render()
    {
        $collections = Collection::take(4)->get();
        
        if (!$this->productsList) {
            $this->productsList = Product::with(['variants.prices.currency', 'thumbnail', 'urls', 'collections'])
                ->latest()
                ->take(8)
                ->get();
        }

        $posts = collect();
        if (class_exists(\LaraZeus\Sky\Models\Post::class)) {
            $posts = \LaraZeus\Sky\Models\Post::published()
                ->latest()
                ->take(3)
                ->get();
        }

        return view('livewire.storefront.home', [
            'collections' => $collections,
            'products'    => $this->productsList,
            'posts'       => $posts,
        ])->layout('layouts.storefront');
    }
}
