<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\On;
use Lunar\Facades\CartSession;

class CartBadge extends Component
{
    #[On('cart-updated')]
    public function refresh()
    {
        // This triggers a re-render
    }

    public function render()
    {
        $cartCount = CartSession::current()?->lines->sum('quantity') ?? 0;

        return view('livewire.storefront.cart-badge', [
            'cartCount' => $cartCount,
        ]);
    }
}
