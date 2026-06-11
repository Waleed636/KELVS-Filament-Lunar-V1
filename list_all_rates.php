<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Lunar\Shipping\Models\ShippingRate;
use Lunar\Models\Price;

echo "SHIPPING RATES:\n";
foreach (ShippingRate::all() as $rate) {
    echo "Rate ID: {$rate->id}, Name: {$rate->name}\n";
    $prices = Price::where('priceable_type', get_class($rate))
        ->where('priceable_id', $rate->id)
        ->get();
    foreach ($prices as $price) {
        echo "  Price ID: {$price->id}, Currency ID: {$price->currency_id}, Price: {$price->price}\n";
    }
}
