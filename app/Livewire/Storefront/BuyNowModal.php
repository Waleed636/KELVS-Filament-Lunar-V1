<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Lunar\Facades\CartSession;
use Lunar\Models\Country;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\DataTypes\ShippingOption;
use App\Models\PartialOrder;

class BuyNowModal extends Component
{
    public $isOpen = false;
    public $variantId;
    public $quantity = 1;
    public $ready = false;

    public $shippingAddress = [
        'first_name' => '',
        'line_one' => '',
        'city' => '',
        'state' => '',
        'postcode' => '',
        'country_id' => '',
        'contact_email' => '',
        'contact_phone' => '',
    ];

    public $shippingOptionHandle = '';
    public $paymentMethod = 'cod';
    public $couponCode = '';
    public $appliedCoupon = '';

    protected $validationAttributes = [
        'couponCode' => 'coupon code',
    ];

    protected function rules()
    {
        return [
            'shippingAddress.first_name' => 'required|string|max:255',
            'shippingAddress.line_one' => 'required|string|max:255',
            'shippingAddress.city' => 'required|string|max:255',
            'shippingAddress.state' => 'nullable|string|max:255',
            'shippingAddress.postcode' => 'nullable|string|max:20',
            'shippingAddress.country_id' => 'required|exists:lunar_countries,id',
            'shippingAddress.contact_email' => 'required_without:shippingAddress.contact_phone|nullable|email|max:255',
            'shippingAddress.contact_phone' => 'required_without:shippingAddress.contact_email|nullable|string|max:50',
        ];
    }

    protected $messages = [
        'shippingAddress.first_name.required' => 'Please enter your full name.',
        'shippingAddress.line_one.required' => 'Please enter your shipping address.',
        'shippingAddress.city.required' => 'Please enter your city.',
        'shippingAddress.contact_phone.required_without' => 'Please enter either a phone number or email address.',
        'shippingAddress.contact_email.required_without' => 'Please enter either an email address or phone number.',
    ];

    #[On('open-buy-now')]
    public function openBuyNow($variantId, $quantity = 1)
    {
        $this->ready = false;
        $this->variantId = $variantId;
        $this->quantity = (int) $quantity;

        $this->couponCode = '';
        $this->appliedCoupon = '';

        // 1. Stash current cart session items to restore later if canceled
        $cart = CartSession::current();
        if ($cart) {
            session(['stashed_coupon_code' => $cart->coupon_code]);
            if (!$cart->lines->isEmpty()) {
                $stashed = [];
                foreach ($cart->lines as $line) {
                    $stashed[] = [
                        'purchasable_type' => $line->purchasable_type,
                        'purchasable_id' => $line->purchasable_id,
                        'quantity' => $line->quantity,
                        'meta' => $line->meta,
                    ];
                }
                session(['stashed_cart_lines' => $stashed]);
                $cart->lines()->delete();
            } else {
                session()->forget('stashed_cart_lines');
            }
            
            // Clear coupon for the Buy Now modal until they enter one
            $cart->coupon_code = null;
            $cart->save();
        } else {
            session()->forget('stashed_coupon_code');
            session()->forget('stashed_cart_lines');
        }

        // 2. Add Buy Now item to active cart
        $variant = ProductVariant::find($variantId);
        if ($variant) {
            CartSession::manager()->add($variant, $this->quantity);
        }

        // 3. Reload cart to fetch prices and attributes
        $cart = CartSession::current();
        if ($cart) {
            $cart->load(['lines.purchasable.prices.currency', 'lines.purchasable.product']);
        }

        // 4. Autofill defaults (Pakistan: 168) and auth user info
        $pakistan = Country::where('iso3', 'PAK')->orWhere('name', 'like', '%Pakistan%')->first();
        $defaultCountryId = $pakistan ? $pakistan->id : (Country::first()?->id ?? 168);
        $this->shippingAddress['country_id'] = $defaultCountryId;

        if (auth()->check()) {
            $user = auth()->user();
            $this->shippingAddress['first_name'] = $user->name;
            $this->shippingAddress['contact_email'] = $user->email;
        }

        // Reset shipping option handle for recalculation
        $this->reset('shippingOptionHandle');
        $this->isOpen = true;

        // 5. Fire DataLayer `begin_checkout` event
        if ($cart) {
            $factor = 10 ** ($cart->currency->decimal_places ?? 0);
            $cartTotal = $cart->calculate()?->total?->value ?? 0;
            $eventId = 'chk_' . $cart->id . '_' . time();

            $this->dispatch('track-ecommerce-event', [
                'eventName' => 'begin_checkout',
                'eventId'   => $eventId,
                'userData'  => [],
                'ecommerceData' => [
                    'currency' => $cart->currency->code ?? 'PKR',
                    'value'    => (float) ($cartTotal / $factor),
                    'items'    => $this->buildCheckoutItems($cart, $factor),
                ],
            ]);
        }

        $this->capturePartialOrder();
        unset($this->cart);
        $this->ready = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->ready = false;
        $this->restoreCart();
    }

    protected function restoreCart()
    {
        $this->couponCode = '';
        $this->appliedCoupon = '';

        $cart = CartSession::current();
        if ($cart) {
            $cart->lines()->delete();
            $cart->coupon_code = session()->pull('stashed_coupon_code');
            $cart->save();
        } else {
            session()->forget('stashed_coupon_code');
        }

        $stashed = session()->pull('stashed_cart_lines', []);
        if (!empty($stashed)) {
            foreach ($stashed as $item) {
                $purchasableModel = $item['purchasable_type']::find($item['purchasable_id']);
                if ($purchasableModel) {
                    CartSession::manager()->add(
                        purchasable: $purchasableModel,
                        quantity: $item['quantity'],
                        meta: $item['meta']
                    );
                }
            }
        }

        unset($this->cart);
        $this->dispatch('cart-updated');
    }

    #[Computed]
    public function cart()
    {
        $cart = CartSession::current();
        if ($cart) {
            return $cart->calculate();
        }
        return null;
    }

    #[Computed]
    public function countries()
    {
        return Country::all();
    }

    #[Computed]
    public function shippingOptions()
    {
        $cart = CartSession::current();
        if (!$cart) {
            return collect();
        }

        if (blank($this->shippingAddress['first_name']) ||
            blank($this->shippingAddress['line_one']) ||
            blank($this->shippingAddress['city']) ||
            blank($this->shippingAddress['country_id'])) {
            return collect();
        }

        $parts = explode(' ', trim($this->shippingAddress['first_name']), 2);
        $firstName = $parts[0];
        $lastName = isset($parts[1]) ? $parts[1] : '.';

        $cart->setShippingAddress([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'company_name' => '',
            'line_one' => $this->shippingAddress['line_one'],
            'line_two' => '',
            'city' => $this->shippingAddress['city'],
            'state' => $this->shippingAddress['state'],
            'postcode' => $this->shippingAddress['postcode'],
            'country_id' => $this->shippingAddress['country_id'],
            'contact_email' => $this->shippingAddress['contact_email'] ?? '',
            'contact_phone' => $this->shippingAddress['contact_phone'] ?? '',
        ]);

        try {
            $options = \Lunar\Facades\ShippingManifest::getOptions($cart);
        } catch (\Exception $e) {
            $options = collect();
        }

        if ($options->isEmpty()) {
            $taxClass = TaxClass::first() ?? TaxClass::create([
                'name' => 'Default Tax Class',
                'default' => true
            ]);

            $option = new ShippingOption(
                name: 'Standard Delivery',
                description: 'Standard delivery via local courier',
                identifier: 'standard-delivery',
                price: new \Lunar\DataTypes\Price(0, $cart->currency, 1),
                taxClass: $taxClass
            );
            $options = collect([$option]);
        }

        if (blank($this->shippingOptionHandle) && $options->isNotEmpty()) {
            $this->shippingOptionHandle = $options->first()->identifier;
        }

        return $options;
    }

    protected function buildCheckoutItems($cart, float $factor): array
    {
        $items = [];
        foreach ($cart->lines as $line) {
            $variant = $line->purchasable;
            if (!$variant) continue;

            $priceValue = $variant->prices->first()?->price?->value;
            $priceFloat = $priceValue ? (float) ($priceValue / $factor) : 0.0;
            $category   = $variant->product?->collections()->first()?->attr('name') ?? 'Skincare';

            $items[] = [
                'item_id'       => $variant->sku,
                'item_name'     => $variant->product?->attr('name') ?? 'Product',
                'item_brand'    => 'KELVS',
                'item_category' => $category,
                'price'         => $priceFloat,
                'quantity'      => (int) $line->quantity,
            ];
        }
        return $items;
    }

    public function updated($name, $value)
    {
        if (str_starts_with($name, 'shippingAddress') || $name === 'shippingOptionHandle') {
            $this->capturePartialOrder();
        }
    }

    public function capturePartialOrder()
    {
        $cart = CartSession::current();
        if (!$cart || $cart->lines->isEmpty()) {
            return;
        }

        $decimalPlaces = $cart->currency->decimal_places ?? 0;
        $factor = 10 ** $decimalPlaces;

        $items = [];
        foreach ($cart->lines as $line) {
            $variant = $line->purchasable;
            if ($variant) {
                $priceValue = $variant->prices->first()?->price?->value;
                $priceFloat = $priceValue ? (float) ($priceValue / $factor) : 0.0;
                $items[] = [
                    'name' => $variant->product?->attr('name') ?? 'Product',
                    'quantity' => (int) $line->quantity,
                    'price' => $priceFloat,
                ];
            }
        }

        $cartTotal = $cart->calculate()?->total?->value;
        $totalFloat = $cartTotal ? (float) ($cartTotal / $factor) : 0.0;

        PartialOrder::updateOrCreate(
            ['session_id' => session()->getId()],
            [
                'name' => $this->shippingAddress['first_name'] ?: null,
                'phone' => $this->shippingAddress['contact_phone'] ?: null,
                'email' => $this->shippingAddress['contact_email'] ?: null,
                'address' => $this->shippingAddress['line_one'] ?: null,
                'city' => $this->shippingAddress['city'] ?: null,
                'province' => $this->shippingAddress['state'] ?: null,
                'postalcode' => $this->shippingAddress['postcode'] ?: null,
                'cart_contents' => $items,
                'cart_total' => $totalFloat,
            ]
        );
    }

    public function placeOrder()
    {
        if (blank($this->shippingOptionHandle)) {
            $options = $this->shippingOptions;
            if ($options && $options->isNotEmpty()) {
                $this->shippingOptionHandle = $options->first()->identifier;
            }
        }

        $this->validate();

        $cart = CartSession::current();
        if (!$cart || $cart->lines->isEmpty()) {
            $this->isOpen = false;
            return;
        }

        $parts = explode(' ', trim($this->shippingAddress['first_name']), 2);
        $firstName = $parts[0];
        $lastName = isset($parts[1]) ? $parts[1] : '.';

        $cart->setShippingAddress([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'company_name' => '',
            'line_one' => $this->shippingAddress['line_one'],
            'line_two' => '',
            'city' => $this->shippingAddress['city'],
            'state' => $this->shippingAddress['state'],
            'postcode' => $this->shippingAddress['postcode'],
            'country_id' => $this->shippingAddress['country_id'],
            'contact_email' => $this->shippingAddress['contact_email'] ?? '',
            'contact_phone' => $this->shippingAddress['contact_phone'] ?? '',
        ]);

        $cart->setBillingAddress([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'company_name' => '',
            'line_one' => $this->shippingAddress['line_one'],
            'line_two' => '',
            'city' => $this->shippingAddress['city'],
            'state' => $this->shippingAddress['state'],
            'postcode' => $this->shippingAddress['postcode'],
            'country_id' => $this->shippingAddress['country_id'],
            'contact_email' => $this->shippingAddress['contact_email'] ?? '',
            'contact_phone' => $this->shippingAddress['contact_phone'] ?? '',
        ]);

        $shippingOption = $this->shippingOptions->first(fn($opt) => $opt->identifier === $this->shippingOptionHandle);
        if ($shippingOption) {
            $cart->setShippingOption($shippingOption);
        }

        $order = $cart->createOrder();

        $order->transactions()->create([
            'success' => true,
            'type' => 'capture',
            'driver' => 'manual',
            'amount' => $order->total->value,
            'reference' => 'manual-cod-' . uniqid(),
            'status' => 'success',
            'card_type' => 'cash-on-delivery',
            'last_four' => '0000',
        ]);

        $order->update([
            'placed_at' => now(),
            'status' => 'payment-offline',
        ]);

        PartialOrder::where('session_id', session()->getId())->delete();
        session()->forget('stashed_cart_lines');
        session()->forget('stashed_coupon_code');

        CartSession::forget();
        $this->dispatch('cart-updated');

        $this->isOpen = false;
        $this->ready = false;

        return redirect()->route('checkout.thankyou', ['id' => $order->id]);
    }

    public function applyCoupon()
    {
        $this->validate([
            'couponCode' => ['required', 'string', new \Lunar\Rules\ValidCoupon],
        ], [
            'couponCode.required' => 'Please enter a coupon code.',
        ]);

        $cart = CartSession::current();
        if ($cart) {
            $cart->coupon_code = $this->couponCode;
            $cart->save();
            $cart->recalculate();
            $this->appliedCoupon = $cart->coupon_code;
            unset($this->cart);
            $this->dispatch('cart-updated');
            $this->capturePartialOrder();
        }
    }

    public function removeCoupon()
    {
        $cart = CartSession::current();
        if ($cart) {
            $cart->coupon_code = null;
            $cart->save();
            $cart->recalculate();
            $this->couponCode = '';
            $this->appliedCoupon = '';
            unset($this->cart);
            $this->dispatch('cart-updated');
            $this->capturePartialOrder();
        }
    }

    public function render()
    {
        return view('livewire.storefront.buy-now-modal', [
            'cart' => $this->cart,
            'countries' => $this->countries,
            'shippingOptions' => $this->shippingOptions,
        ]);
    }
}
