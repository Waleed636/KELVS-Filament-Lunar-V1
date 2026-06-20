<?php

namespace App\Shipping;

use Closure;
use Lunar\Models\Contracts\Cart;
use Lunar\DataTypes\ShippingOption;
use Lunar\Models\TaxClass;
use Lunar\Base\ShippingManifestInterface;

class FallbackShippingModifier
{
    public function handle(Cart $cart, Closure $next)
    {
        $cart = $next($cart);

        $manifest = app(ShippingManifestInterface::class);

        // If no shipping options were calculated (e.g., because postcode is empty),
        // we add the Standard Delivery fallback so that the checkout does not crash.
        if ($manifest->options->isEmpty()) {
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

            $manifest->addOption($option);
        }

        return $cart;
    }
}
