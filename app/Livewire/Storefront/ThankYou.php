<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Lunar\Models\Order;

class ThankYou extends Component
{
    public $order;
    public $purchaseEventData = null;


    public function mount($id)
    {
        $this->order = Order::with([
            'lines', 
            'shippingAddress', 
            'billingAddress', 
            'transactions'
        ])->find($id);

        if (!$this->order) {
            return redirect()->to('/');
        }

        // Eager load purchasable.product only for non-shipping lines
        $productLines = $this->order->lines->filter(fn($line) => $line->type !== 'shipping');
        $productLines->load('purchasable.product');

        $eventId = 'pur_' . $this->order->id;
        $currencyModel = \Lunar\Models\Currency::where('code', $this->order->currency_code)->first() ?? \Lunar\Models\Currency::getDefault();
        $decimalPlaces = $currencyModel ? $currencyModel->decimal_places : 2;
        $factor = 10 ** $decimalPlaces;

        // Build enriched items array
        $items = [];
        foreach ($this->order->lines as $line) {
            if ($line->type === 'shipping') {
                continue;
            }
            $variant = $line->purchasable;
            if ($variant) {
                $category    = $variant->product?->collections()->first()?->attr('name') ?? 'Skincare';
                $variantName = $variant->attr('name') ?? $variant->sku;

                $items[] = [
                    'item_id'       => $variant->sku,
                    'item_name'     => $variant->product?->attr('name') ?? 'Product',
                    'item_brand'    => 'KELVS',
                    'item_category' => $category,
                    'item_variant'  => $variantName,
                    'price'         => (float) ($line->unit_price->value / $factor),
                    'quantity'      => (int) $line->quantity,
                ];
            }
        }

        // Build hashed user_data for Enhanced Conversions
        $addr     = $this->order->shippingAddress;
        $userData = [];

        if ($addr?->contact_email) {
            $userData['email_address'] = hash('sha256', strtolower(trim($addr->contact_email)));
        }

        if ($addr?->contact_phone) {
            // Normalize to E.164 for Pakistan (+92...)
            $phone = preg_replace('/\D/', '', $addr->contact_phone);
            if (!str_starts_with($phone, '92')) {
                $phone = '92' . ltrim($phone, '0');
            }
            $userData['phone_number'] = hash('sha256', '+' . $phone);
        }

        // external_id: hashed order reference — ties conversion to a specific order
        $userData['external_id'] = hash('sha256', (string) $this->order->reference);

        if ($addr?->first_name) {
            $userData['address']['first_name'] = hash('sha256', strtolower(trim($addr->first_name)));
        }
        if ($addr?->city) {
            $userData['address']['city'] = hash('sha256', strtolower(trim($addr->city)));
        }
        // Country is NOT hashed — pass ISO 2-letter code as-is
        $userData['address']['country'] = 'PK';

        $this->purchaseEventData = [
            'eventName' => 'purchase',
            'eventId'   => $eventId,
            'userData'  => $userData,
            'ecommerceData' => [
                'transaction_id' => $this->order->reference,
                'affiliation'    => 'KELVS Store',
                'value'          => (float) ($this->order->total->value / $factor),
                'tax'            => (float) ($this->order->tax_total->value / $factor),
                'shipping'       => (float) ($this->order->shipping_total->value / $factor),
                'currency'       => $this->order->currency_code ?? 'PKR',
                'items'          => $items,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.storefront.thankyou', [
            'completedOrder' => $this->order,
        ])->layout('layouts.storefront');
    }
}
