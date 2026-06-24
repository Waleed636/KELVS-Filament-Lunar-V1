<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Lunar\Models\Price;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_renders_with_schema_price()
    {
        // 1. Create dependencies
        $currency = Currency::factory()->create([
            'code' => 'PKR',
            'decimal_places' => 2,
            'default' => true,
            'exchange_rate' => 1.0,
        ]);

        Language::factory()->create([
            'code' => 'en',
            'default' => true,
        ]);

        // Create a product
        $product = Product::factory()->create();
        
        // Add URL for slug matching
        $product->urls()->create([
            'slug' => 'test-product',
            'element_type' => Product::class,
            'language_id' => Language::getDefault()->id,
        ]);

        // Use the first variant created by the factory, or create one
        $variant = $product->variants->first();
        if (!$variant) {
            $variant = ProductVariant::factory()->create([
                'product_id' => $product->id,
                'sku' => 'TEST-SKU',
            ]);
        } else {
            $variant->update(['sku' => 'TEST-SKU']);
        }

        // Create a price for the variant using morph class
        $priceObj = Price::factory()->create([
            'priceable_type' => $variant->getMorphClass(),
            'priceable_id' => $variant->id,
            'currency_id' => $currency->id,
            'price' => 125000, // representing 1250.00
        ]);

        // Retrieve the page and assert status 200
        $response = $this->get('/products/test-product');
        $response->assertStatus(200);

        // Check that the offers JSON-LD structured data contains "price": "1250.00"
        $response->assertSee('"price": "1250.00"', false);
    }
}
