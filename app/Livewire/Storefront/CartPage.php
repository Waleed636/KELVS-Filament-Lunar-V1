<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Lunar\Facades\CartSession;
use Lunar\Models\ProductVariant;

class CartPage extends Component
{
    public $dataLayerPayload = null;

    #[Computed]
    public function cart()
    {
        $cart = CartSession::current();
        if ($cart) {
            return $cart->calculate();
        }
        return null;
    }

    public function mount()
    {
        $cart = CartSession::current();
        if (!$cart || $cart->lines->isEmpty()) return;

        // Eager load to prevent N+1
        $cart->load(['lines.purchasable.prices.currency', 'lines.purchasable.product']);

        $factor = 10 ** ($cart->currency->decimal_places ?? 0);
        $items  = [];

        foreach ($cart->lines as $line) {
            $variant = $line->purchasable;
            if (!$variant) continue;

            $priceValue = $variant->prices->first()?->price?->value;
            $priceFloat = $priceValue ? (float) ($priceValue / $factor) : 0.0;

            $category = $variant->product?->collections()->first()?->attr('name') ?? 'Skincare';

            $items[] = [
                'item_id'       => $variant->sku,
                'item_name'     => $variant->product?->attr('name') ?? 'Product',
                'item_brand'    => 'KELVS',
                'item_category' => $category,
                'price'         => $priceFloat,
                'quantity'      => (int) $line->quantity,
            ];
        }

        $cartTotal = $cart->calculate()?->total?->value ?? 0;

        $this->dataLayerPayload = [
            'eventName' => 'view_cart',
            'eventId'   => 'vc_' . $cart->id . '_' . time(),
            'ecommerceData' => [
                'currency' => $cart->currency->code ?? 'PKR',
                'value'    => (float) ($cartTotal / $factor),
                'items'    => $items,
            ],
        ];
    }

    public function updateQuantity($lineId, $quantity)
    {
        $quantity = (int) $quantity;
        if ($quantity <= 0) {
            $this->removeLine($lineId);
            return;
        }

        $cart = CartSession::current();
        if ($cart) {
            $cart->updateLine($lineId, $quantity);
            $this->dispatch('cart-updated');
        }
    }

    public function removeLine($lineId)
    {
        $cart = CartSession::current();
        if ($cart) {
            // Capture item data BEFORE removing so we can track it
            $line = $cart->lines->firstWhere('id', (int) $lineId);
            if ($line) {
                $variant = $line->purchasable;
                $factor  = 10 ** ($cart->currency->decimal_places ?? 0);
                $price   = 0.0;

                if ($variant) {
                    $priceValue = $variant->prices->first()?->price?->value;
                    $price      = $priceValue ? (float) ($priceValue / $factor) : 0.0;
                }

                $category = $variant?->product?->collections()->first()?->attr('name') ?? 'Skincare';

                $this->dispatch('track-ecommerce-event', [
                    'eventName' => 'remove_from_cart',
                    'eventId'   => 'rfc_' . $lineId . '_' . time(),
                    'ecommerceData' => [
                        'currency' => $cart->currency->code ?? 'PKR',
                        'value'    => round($price * $line->quantity, 2),
                        'items'    => [[
                            'item_id'       => $variant?->sku ?? '',
                            'item_name'     => $variant?->product?->attr('name') ?? 'Product',
                            'item_brand'    => 'KELVS',
                            'item_category' => $category,
                            'price'         => $price,
                            'quantity'      => (int) $line->quantity,
                        ]],
                    ],
                ]);
            }

            $cart->remove((int) $lineId);
            $this->dispatch('cart-updated');
        }
    }

    public function render()
    {
        return view('livewire.storefront.cart-page', [
            'cart' => $this->cart,
        ])->layout('layouts.storefront');
    }
}
