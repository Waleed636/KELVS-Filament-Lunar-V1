<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Lunar\Models\Product;

class Shop extends Component
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
                        'item_list_name' => 'Shop Page',
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
            ->get();

        // Build view_item_list payload
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
                'item_list_id'   => 'shop_page',
                'item_list_name' => 'Shop',
                'index'          => $index,
                'price'          => $priceFloat,
                'quantity'       => 1,
            ];
        }

        if (!empty($listItems)) {
            $this->dataLayerPayload = [
                'eventName' => 'view_item_list',
                'eventId'   => 'vil_shop_' . time(),
                'ecommerceData' => [
                    'item_list_id'   => 'shop_page',
                    'item_list_name' => 'Shop',
                    'items'          => $listItems,
                ],
            ];
        }
    }

    public function render()
    {
        if (!$this->productsList) {
            $this->productsList = Product::with(['variants.prices.currency', 'thumbnail', 'urls', 'collections'])
                ->latest()
                ->get();
        }

        return view('livewire.storefront.shop', [
            'products' => $this->productsList,
        ])->layout('layouts.storefront');
    }
}
