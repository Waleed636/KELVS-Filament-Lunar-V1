<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Lunar\Models\Order;

class ThankYou extends Component
{
    public $order;

    public function mount($id)
    {
        $this->order = Order::with(['lines', 'shippingAddress', 'billingAddress', 'transactions'])->find($id);

        if (!$this->order) {
            return redirect()->to('/');
        }
    }

    public function render()
    {
        return view('livewire.storefront.thankyou', [
            'completedOrder' => $this->order,
        ])->layout('layouts.storefront');
    }
}
