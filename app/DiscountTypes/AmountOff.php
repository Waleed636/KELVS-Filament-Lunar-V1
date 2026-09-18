<?php

namespace App\DiscountTypes;

use Filament\Forms;
use Lunar\Admin\Base\LunarPanelDiscountInterface;
use Lunar\Models\Contracts\Cart as CartContract;
use Lunar\Models\Currency;

class AmountOff extends \Lunar\DiscountTypes\AmountOff implements LunarPanelDiscountInterface
{
    /**
     * Return the schema to use in the Lunar admin panel
     */
    public function lunarPanelSchema(): array
    {
        $currencies = Currency::get();

        $currencyInputs = [];

        foreach ($currencies as $currency) {
            $currencyInputs[] = Forms\Components\TextInput::make(
                'data.fixed_values.'.$currency->code
            )->label(
                $currency->code
            )->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                $currencyCode = last(explode('.', $component->getStatePath()));
                $currency = Currency::whereCode($currencyCode)->first();

                if ($currency) {
                    $component->state($state / $currency->factor);
                }
            });
        }

        return [
            Forms\Components\Toggle::make('data.fixed_value')
                ->label(__('lunarpanel::discount.form.fixed_value.label'))
                ->live(),
            Forms\Components\TextInput::make('data.percentage')
                ->label(__('lunarpanel::discount.form.percentage.label'))
                ->visible(
                    fn (Forms\Get $get) => ! $get('data.fixed_value')
                )->numeric(),
            Forms\Components\Group::make(
                $currencyInputs
            )->visible(
                fn (Forms\Get $get) => (bool) $get('data.fixed_value')
            )->columns(3),
        ];
    }

    public function lunarPanelOnFill(array $data): array
    {
        return $data;
    }

    public function lunarPanelOnSave(array $data): array
    {
        return $data;
    }

    public function lunarPanelRelationManagers(): array
    {
        return [];
    }
    /**
     * Check if discount's conditions met.
     */
    protected function checkDiscountConditions(CartContract $cart): bool
    {
        /** @var \Lunar\Models\Cart $cart */
        $data = $this->discount->data;

        $customerIds = $this->discount->customers->pluck('id');

        if ((! $customerIds->isEmpty() && ! $cart->customer) || (! $customerIds->isEmpty() && ! $customerIds->contains($cart->customer_id))) {
            return false;
        }

        $cartCoupon = strtoupper($cart->coupon_code ?? '');
        $conditionCoupon = strtoupper($this->discount->coupon ?? '');

        $validCoupon = filled($conditionCoupon) ? ($cartCoupon === $conditionCoupon) : true;

        $minSpend = (int) ($data['min_prices'][$cart->currency->code] ?? 0) / (int) $cart->currency->factor;
        $minSpend = (int) bcmul($minSpend, $cart->currency->factor);

        $lines = $this->getEligibleLines($cart);
        $validMinSpend = $minSpend ? $minSpend < $lines->sum('subTotal.value') : true;

        $validMaxUses = $this->discount->max_uses ? $this->discount->uses < $this->discount->max_uses : true;

        if ($validMaxUses && $this->discount->max_uses_per_user) {
            if ($cart->user) {
                $validMaxUses = $this->usesByUser($cart->user) < $this->discount->max_uses_per_user;
            } else {
                // If guest, track by email address in order addresses!
                $email = $cart->shippingAddress?->contact_email ?? $cart->billingAddress?->contact_email;
                if ($email) {
                    $orders = \Lunar\Models\Order::whereHas('addresses', function ($query) use ($email) {
                        $query->where('contact_email', $email);
                    })->get();

                    $uses = $orders->filter(function ($order) {
                        $breakdown = $order->discount_breakdown;
                        if (is_array($breakdown) || is_object($breakdown)) {
                            foreach ($breakdown as $item) {
                                $itemId = is_object($item) ? ($item->discount_id ?? null) : ($item['discount_id'] ?? null);
                                if ($itemId == $this->discount->id) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    })->count();
                    
                    $validMaxUses = $uses < $this->discount->max_uses_per_user;
                } else {
                    // Allow coupon to show before email is filled
                    $validMaxUses = true;
                }
            }
        }

        return $validCoupon && $validMinSpend && $validMaxUses;
    }
}
