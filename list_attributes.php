<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ATTRIBUTES ===\n";
foreach (\Lunar\Models\Attribute::all() as $attr) {
    echo "Handle: {$attr->handle}, AttributeType: {$attr->attribute_type}, Type: {$attr->type}, Name: " . json_encode($attr->name) . "\n";
}
