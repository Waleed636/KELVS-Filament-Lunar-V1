<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Lunar\Facades\CartSession;

class CartPage extends Component
{
    #[Computed]
    public function cart()
    {
        $cart = CartSession::current();
        if ($cart) {
            return $cart->calculate();
        }
        return null;
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
