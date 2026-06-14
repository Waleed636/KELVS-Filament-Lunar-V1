<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Lunar\Models\Order;

class ThankYou extends Component
{
    public $order;

    public function mount($id)
    {
        $this->order = Order::with([
            'lines.purchasable.product', 
            'shippingAddress', 
            'billingAddress', 
            'transactions'
        ])->find($id);

        if (!$this->order) {
            return redirect()->to('/');
        }

        $eventId = 'pur_' . $this->order->id;
        $currencyModel = \Lunar\Models\Currency::where('code', $this->order->currency_code)->first() ?? \Lunar\Models\Currency::getDefault();
        $decimalPlaces = $currencyModel ? $currencyModel->decimal_places : 2;
        $factor = 10 ** $decimalPlaces;

        $items = [];
        foreach ($this->order->lines as $line) {
            $variant = $line->purchasable;
            if ($variant) {
                $items[] = [
                    'item_id' => $variant->sku,
                    'item_name' => $variant->product?->attr('name') ?? 'Product',
                    'price' => (float) ($line->unit_price->value / $factor),
                    'quantity' => (int) $line->quantity
                ];
            }
        }

        $this->dispatch('track-ecommerce-event', [
            'eventName' => 'purchase',
            'eventId' => $eventId,
            'ecommerceData' => [
                'transaction_id' => $this->order->reference,
                'value' => (float) ($this->order->total->value / $factor),
                'tax' => (float) ($this->order->tax_total->value / $factor),
                'shipping' => (float) ($this->order->shipping_total->value / $factor),
                'currency' => $this->order->currency_code ?? 'PKR',
                'items' => $items
            ]
        ]);
    }

    public function render()
    {
        return view('livewire.storefront.thankyou', [
            'completedOrder' => $this->order,
        ])->layout('layouts.storefront');
    }
}
