<?php

namespace Tests\Feature;

use App\Services\PostExService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PostExIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['postex.api_token' => 'test-api-token']);
        config(['postex.base_url' => 'https://api.postex.pk']);
        Cache::flush();
    }

    public function test_get_operational_cities_fetches_and_caches_results(): void
    {
        Http::fake([
            'https://api.postex.pk/services/integration/api/order/v2/get-operational-city*' => Http::response([
                'statusCode' => '200',
                'statusMessage' => 'SUCCESSFULLY OPERATED',
                'dist' => [
                    [
                        'operationalCityName' => 'Lahore',
                        'countryName' => 'Pakistan',
                        'isPickupCity' => 'true',
                        'isDeliveryCity' => 'true',
                    ],
                    [
                        'operationalCityName' => 'Karachi',
                        'countryName' => 'Pakistan',
                        'isPickupCity' => 'true',
                        'isDeliveryCity' => 'true',
                    ]
                ]
            ], 200),
        ]);

        $service = new PostExService();
        $cities = $service->getOperationalCities('delivery');

        $this->assertCount(2, $cities);
        $this->assertEquals('Lahore', $cities[0]['operationalCityName']);
        $this->assertEquals('Karachi', $cities[1]['operationalCityName']);

        // Test caching: if cache works, Http should not receive a second call when fetching again.
        Http::fake([
            'https://api.postex.pk/services/integration/api/order/v2/get-operational-city*' => Http::response([], 500),
        ]);

        $citiesFromCache = $service->getOperationalCities('delivery');
        $this->assertCount(2, $citiesFromCache);
        $this->assertEquals('Lahore', $citiesFromCache[0]['operationalCityName']);
    }

    public function test_get_pickup_addresses_fetches_successfully(): void
    {
        Http::fake([
            'https://api.postex.pk/services/integration/api/order/v1/get-merchant-address*' => Http::response([
                'statusCode' => '200',
                'statusMessage' => 'SUCCESSFULLY OPERATED',
                'dist' => [
                    [
                        'phone1' => '03001234567',
                        'phone2' => '',
                        'contactPersonName' => 'Waleed',
                        'cityName' => 'Lahore',
                        'address' => 'Warehouse A',
                        'addressCode' => 'WH-A-123',
                    ]
                ]
            ], 200),
        ]);

        $service = new PostExService();
        $pickups = $service->getPickupAddresses();

        $this->assertCount(1, $pickups);
        $this->assertEquals('WH-A-123', $pickups[0]['addressCode']);
        $this->assertEquals('Warehouse A', $pickups[0]['address']);
    }

    public function test_create_order_succeeds(): void
    {
        Http::fake([
            'https://api.postex.pk/services/integration/api/order/v3/create-order' => Http::response([
                'statusCode' => '200',
                'statusMessage' => 'ORDER HAS BEEN CREATED',
                'dist' => [
                    'trackingNumber' => 'CX-123456789',
                    'orderStatus' => 'UnBooked',
                    'orderDate' => '2026-06-22 01:23:45',
                ]
            ], 200),
        ]);

        $service = new PostExService();
        $response = $service->createOrder([
            'cityName' => 'Lahore',
            'customerName' => 'John Doe',
            'customerPhone' => '03123456789',
            'deliveryAddress' => '123 Street A',
            'invoicePayment' => 1500,
            'items' => 2,
            'orderRefNumber' => 'ORD-1001',
            'orderType' => 'Normal',
        ]);

        $this->assertEquals('200', $response['statusCode']);
        $this->assertEquals('CX-123456789', $response['dist']['trackingNumber']);
        $this->assertEquals('UnBooked', $response['dist']['orderStatus']);
    }

    public function test_cancel_order_succeeds(): void
    {
        Http::fake([
            'https://api.postex.pk/services/integration/api/order/v1/cancel-order' => Http::response([
                'statusCode' => '200',
                'statusMessage' => 'SUCCESSFULLY CANCELLED',
            ], 200),
        ]);

        $service = new PostExService();
        $response = $service->cancelOrder('CX-123456789');

        $this->assertEquals('200', $response['statusCode']);
    }

    public function test_get_airway_bill_returns_binary_data(): void
    {
        Http::fake([
            'https://api.postex.pk/services/integration/api/order/v1/get-invoice*' => Http::response('%PDF-1.4 mock binary pdf data', 200),
        ]);

        $service = new PostExService();
        $pdf = $service->getAirwayBill('CX-123456789');

        $this->assertNotNull($pdf);
        $this->assertStringContainsString('%PDF-1.4', $pdf);
    }
}
