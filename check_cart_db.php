<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== LUNAR CARTS ===\n";
foreach (DB::table('lunar_carts')->get() as $cart) {
    echo "ID: {$cart->id}, Session ID: " . ($cart->session_id ?? 'N/A') . ", Created: {$cart->created_at}\n";
}

echo "\n=== LUNAR CART LINES ===\n";
foreach (DB::table('lunar_cart_lines')->get() as $line) {
    echo "ID: {$line->id}, Cart ID: {$line->cart_id}, Purchasable ID: {$line->purchasable_id}, Quantity: {$line->quantity}\n";
}
