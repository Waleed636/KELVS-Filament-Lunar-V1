<?php

namespace Tests\Feature;

use App\Mail\OrderPlacedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\Order;
use Lunar\Models\OrderAddress;
use Tests\TestCase;

class OrderEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_dispatched_when_order_is_placed(): void
    {
        Mail::fake();

        // 1. Create dependencies for Order (Currency, Language)
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

        // 2. Create the Order
        $order = Order::factory()->create([
            'placed_at' => null,
            'status' => 'awaiting-payment',
            'currency_code' => $currency->code,
        ]);

        // 3. Create Shipping/Billing Address with an email
        OrderAddress::factory()->create([
            'order_id' => $order->id,
            'type' => 'shipping',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contact_email' => 'john.doe@example.com',
        ]);

        // 4. Update the order to place it
        $order->update([
            'placed_at' => now(),
            'status' => 'payment-offline',
        ]);

        // 5. Assert the mail was sent/queued
        Mail::assertQueued(OrderPlacedMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id &&
                   $mail->hasTo('john.doe@example.com');
        });
    }

    public function test_email_template_renders_successfully(): void
    {
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

        $order = Order::factory()->create([
            'placed_at' => now(),
            'status' => 'payment-offline',
            'currency_code' => $currency->code,
            'sub_total' => 1000,
            'shipping_total' => 150,
            'tax_total' => 50,
            'discount_total' => 0,
            'total' => 1200,
        ]);

        OrderAddress::factory()->create([
            'order_id' => $order->id,
            'type' => 'shipping',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contact_email' => 'john.doe@example.com',
            'line_one' => '123 Test Street',
            'city' => 'Lahore',
            'postcode' => '54000',
        ]);

        // Create an order line
        \Lunar\Models\OrderLine::factory()->create([
            'order_id' => $order->id,
            'type' => 'physical',
            'description' => 'Test Product Variant',
            'identifier' => 'TEST-SKU-123',
            'quantity' => 2,
            'unit_price' => 500,
            'sub_total' => 1000,
            'tax_total' => 50,
            'total' => 1050,
        ]);

        $mailable = new OrderPlacedMail($order);

        $mailable->assertSeeInHtml('KELVS');
        $mailable->assertSeeInHtml('Test Product Variant');
        $mailable->assertSeeInHtml('TEST-SKU-123');
        $mailable->assertSeeInHtml('John Doe');
    }
}
