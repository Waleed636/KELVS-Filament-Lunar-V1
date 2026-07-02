<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Lunar\Facades\CartSession;
use Lunar\Models\Country;
use Lunar\Models\Order;
use Lunar\DataTypes\ShippingOption;
use Lunar\Models\TaxClass;
use App\Models\PartialOrder;

class Checkout extends Component
{
    public $shippingAddress = [
        'first_name' => '',
        'last_name' => '',
        'company_name' => '',
        'line_one' => '',
        'line_two' => '',
        'city' => '',
        'state' => '',
        'postcode' => '',
        'country_id' => '',
        'contact_email' => '',
        'contact_phone' => '',
    ];

    public $billingAddress = [
        'first_name' => '',
        'last_name' => '',
        'company_name' => '',
        'line_one' => '',
        'line_two' => '',
        'city' => '',
        'state' => '',
        'postcode' => '',
        'country_id' => '',
        'contact_email' => '',
        'contact_phone' => '',
    ];

    public $sameAsShipping = true;
    public $shippingOptionHandle = '';
    public $paymentMethod = 'cod';
    public $orderCompleted = false;
    public $completedOrder = null;
    public $checkoutEventData = null;
    public $couponCode = '';
    public $appliedCoupon = '';

    protected $validationAttributes = [
        'couponCode' => 'coupon code',
    ];


    protected $messages = [
        'shippingAddress.first_name.required' => 'Please enter your full name.',
        'shippingAddress.line_one.required' => 'Please enter your shipping address.',
        'shippingAddress.city.required' => 'Please enter your city.',
        'shippingAddress.contact_phone.required_without' => 'Please enter either a phone number or email address.',
        'shippingAddress.contact_email.required_without' => 'Please enter either an email address or phone number.',
    ];

    protected function rules()
    {
        $rules = [
            'shippingAddress.first_name' => 'required|string|max:255',
            'shippingAddress.line_one' => 'required|string|max:255',
            'shippingAddress.city' => 'required|string|max:255',
            'shippingAddress.state' => 'nullable|string|max:255',
            'shippingAddress.postcode' => 'nullable|string|max:20',
            'shippingAddress.country_id' => 'required|exists:lunar_countries,id',
            'shippingAddress.contact_email' => 'required_without:shippingAddress.contact_phone|nullable|email|max:255',
            'shippingAddress.contact_phone' => 'required_without:shippingAddress.contact_email|nullable|string|max:50',
            'shippingOptionHandle' => 'required|string',
            'paymentMethod' => 'required|in:cod,card',
        ];

        if (!$this->sameAsShipping) {
            $rules = array_merge($rules, [
                'billingAddress.first_name' => 'required|string|max:255',
                'billingAddress.line_one' => 'required|string|max:255',
                'billingAddress.city' => 'required|string|max:255',
                'billingAddress.state' => 'nullable|string|max:255',
                'billingAddress.postcode' => 'nullable|string|max:20',
                'billingAddress.country_id' => 'required|exists:lunar_countries,id',
            ]);
        }

        return $rules;
    }

    public function mount()
    {
        $cart = CartSession::current();
        if (!$cart || $cart->lines->isEmpty()) {
            return redirect()->to('/cart');
        }

        $this->couponCode = $cart->coupon_code;
        $this->appliedCoupon = $cart->coupon_code;

        // Set default country to Pakistan (ID: 168) if available
        $pakistan = Country::where('iso3', 'PAK')->orWhere('name', 'like', '%Pakistan%')->first();
        $defaultCountryId = $pakistan ? $pakistan->id : (Country::first()?->id ?? 168);

        $this->shippingAddress['country_id'] = $defaultCountryId;
        $this->billingAddress['country_id'] = $defaultCountryId;

        // Eager load relationships for tracking to prevent N+1 issues
        $cart->load(['lines.purchasable.prices.currency', 'lines.purchasable.product']);

        $decimalPlaces = $cart->currency->decimal_places ?? 0;
        $factor = 10 ** $decimalPlaces;

        $eventId   = 'chk_' . $cart->id . '_' . time();
        $cartTotal = $cart->calculate()?->total?->value ?? 0;

        // Build partial user_data from form state (may be empty on first mount)
        $userData = [];
        if (!empty($this->shippingAddress['contact_email'])) {
            $userData['email_address'] = hash('sha256', strtolower(trim($this->shippingAddress['contact_email'])));
        }
        if (!empty($this->shippingAddress['contact_phone'])) {
            $phone = preg_replace('/\D/', '', $this->shippingAddress['contact_phone']);
            if (!str_starts_with($phone, '92')) {
                $phone = '92' . ltrim($phone, '0');
            }
            $userData['phone_number'] = hash('sha256', '+' . $phone);
        }
        $userData['external_id'] = hash('sha256', 'cart_' . $cart->id);

        $this->checkoutEventData = [
            'eventName' => 'begin_checkout',
            'eventId'   => $eventId,
            'userData'  => $userData,
            'ecommerceData' => [
                'currency' => $cart->currency->code ?? 'PKR',
                'value'    => (float) ($cartTotal / $factor),
                'items'    => $this->buildCheckoutItems($cart, $factor),
            ],
        ];

        $this->capturePartialOrder();
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

        // Check for basic fields to update cart shipping address
        if (blank($this->shippingAddress['first_name']) ||
            blank($this->shippingAddress['line_one']) ||
            blank($this->shippingAddress['city']) ||
            blank($this->shippingAddress['country_id'])) {
            return collect();
        }

        // Split name for internal Lunar cart compatibility
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
        } catch (\Lunar\Exceptions\MissingCurrencyPriceException $e) {
            $options = collect();
        } catch (\Exception $e) {
            $options = collect();
        }

        // Fallback option if manifest has none
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

        // Set default shipping option handle if not set yet
        if (blank($this->shippingOptionHandle) && $options->isNotEmpty()) {
            $this->shippingOptionHandle = $options->first()->identifier;
        }

        return $options;
    }

    public function updatedShippingAddress()
    {
        // Force live recalculation of shipping options on address updates
        $this->reset('shippingOptionHandle');
    }

    /**
     * Build the items array from the current cart (shared by multiple events).
     */
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

    /**
     * Fires add_shipping_info when a shipping option is selected.
     */
    public function updatedShippingOptionHandle($value)
    {
        if (blank($value)) return;

        $cart = CartSession::current();
        if (!$cart || $cart->lines->isEmpty()) return;

        $cart->load(['lines.purchasable.prices.currency', 'lines.purchasable.product']);
        $factor = 10 ** ($cart->currency->decimal_places ?? 0);

        $option   = $this->shippingOptions->first(fn($opt) => $opt->identifier === $value);
        $cartTotal = $cart->calculate()?->total?->value ?? 0;

        $this->dispatch('track-ecommerce-event', [
            'eventName' => 'add_shipping_info',
            'eventId'   => 'shi_' . $cart->id . '_' . time(),
            'ecommerceData' => [
                'currency'      => $cart->currency->code ?? 'PKR',
                'value'         => (float) ($cartTotal / $factor),
                'shipping_tier' => $option?->name ?? $value,
                'items'         => $this->buildCheckoutItems($cart, $factor),
            ],
        ]);
    }

    /**
     * Fires add_payment_info when a payment method is selected.
     */
    public function updatedPaymentMethod($value)
    {
        if (blank($value)) return;

        $cart = CartSession::current();
        if (!$cart || $cart->lines->isEmpty()) return;

        $cart->load(['lines.purchasable.prices.currency', 'lines.purchasable.product']);
        $factor    = 10 ** ($cart->currency->decimal_places ?? 0);
        $cartTotal = $cart->calculate()?->total?->value ?? 0;

        $paymentLabel = match($value) {
            'cod'  => 'Cash on Delivery',
            'card' => 'Card',
            default => $value,
        };

        $this->dispatch('track-ecommerce-event', [
            'eventName' => 'add_payment_info',
            'eventId'   => 'pay_' . $cart->id . '_' . time(),
            'ecommerceData' => [
                'currency'     => $cart->currency->code ?? 'PKR',
                'value'        => (float) ($cartTotal / $factor),
                'payment_type' => $paymentLabel,
                'items'        => $this->buildCheckoutItems($cart, $factor),
            ],
        ]);
    }

    public function placeOrder()
    {
        // Populate shippingOptionHandle if empty but options exist to prevent validation failure
        if (blank($this->shippingOptionHandle)) {
            $options = $this->shippingOptions;
            if ($options && $options->isNotEmpty()) {
                $this->shippingOptionHandle = $options->first()->identifier;
            }
        }

        $this->validate();

        $cart = CartSession::current();
        if (!$cart || $cart->lines->isEmpty()) {
            return redirect()->to('/cart');
        }

        // Split name for internal Lunar cart compatibility
        $parts = explode(' ', trim($this->shippingAddress['first_name']), 2);
        $firstName = $parts[0];
        $lastName = isset($parts[1]) ? $parts[1] : '.';

        // 1. Save addresses
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

        if ($this->sameAsShipping) {
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
        } else {
            $bParts = explode(' ', trim($this->billingAddress['first_name']), 2);
            $bFirstName = $bParts[0];
            $bLastName = isset($bParts[1]) ? $bParts[1] : '.';

            $cart->setBillingAddress([
                'first_name' => $bFirstName,
                'last_name' => $bLastName,
                'company_name' => '',
                'line_one' => $this->billingAddress['line_one'],
                'line_two' => '',
                'city' => $this->billingAddress['city'],
                'state' => $this->billingAddress['state'],
                'postcode' => $this->billingAddress['postcode'],
                'country_id' => $this->billingAddress['country_id'],
                'contact_email' => $this->shippingAddress['contact_email'] ?? '', 
                'contact_phone' => $this->shippingAddress['contact_phone'] ?? '', 
            ]);
        }

        // 2. Select shipping option
        $shippingOption = $this->shippingOptions->first(fn($opt) => $opt->identifier === $this->shippingOptionHandle);
        if ($shippingOption) {
            $cart->setShippingOption($shippingOption);
        }

        // 3. Create Draft Order
        $order = $cart->createOrder();

        // 4. Record Manual Payment Transaction
        $order->transactions()->create([
            'success' => true,
            'type' => 'capture',
            'driver' => 'manual',
            'amount' => $order->total->value,
            'reference' => 'manual-' . strtoupper($this->paymentMethod) . '-' . uniqid(),
            'status' => 'success',
            'card_type' => $this->paymentMethod === 'cod' ? 'cash-on-delivery' : 'mock-card',
            'last_four' => $this->paymentMethod === 'cod' ? '0000' : '4242',
        ]);

        // 5. Complete order status and place it
        $order->update([
            'placed_at' => now(),
            'status' => $this->paymentMethod === 'cod' ? 'payment-offline' : 'payment-received',
        ]);

        // Delete partial order on successful purchase
        PartialOrder::where('session_id', session()->getId())->delete();

        // 6. Clear cart session and redirect to the thankyou page
        CartSession::forget();
        $this->dispatch('cart-updated');

        return redirect()->route('checkout.thankyou', ['id' => $order->id]);
    }

    public function updated($name, $value)
    {
        if (str_starts_with($name, 'shippingAddress') || str_starts_with($name, 'billingAddress') || $name === 'shippingOptionHandle') {
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
        return view('livewire.storefront.checkout', [
            'cart' => $this->cart,
            'countries' => $this->countries,
            'shippingOptions' => $this->shippingOptions,
        ])->layout('layouts.storefront', [
            'seoTitle'  => 'Secure Checkout | KELVS',
            'seoRobots' => 'noindex, nofollow',
        ]);
    }
}
