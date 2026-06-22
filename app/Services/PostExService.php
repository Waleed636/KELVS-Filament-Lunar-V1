<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PostExService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('postex.base_url', 'https://api.postex.pk'), '/');
        $this->token = config('postex.api_token') ?? '';
    }

    /**
     * Helper to get common headers.
     */
    protected function headers(): array
    {
        return [
            'token' => $this->token,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Helper to get initialized Http client.
     */
    protected function client(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::withHeaders($this->headers());

        if (!config('postex.verify_ssl', true)) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    /**
     * Get operational cities.
     * Caches response for 24 hours.
     */
    public function getOperationalCities(?string $type = null): array
    {
        $cacheKey = 'postex_cities_' . ($type ?? 'all');

        return Cache::remember($cacheKey, now()->addDay(), function () use ($type) {
            try {
                $response = $this->client()
                    ->get("{$this->baseUrl}/services/integration/api/order/v2/get-operational-city", array_filter([
                        'operationalCityType' => $type,
                    ]));

                if ($response->successful()) {
                    $data = $response->json();
                    if (($data['statusCode'] ?? null) == '200' && isset($data['dist'])) {
                        return $data['dist'];
                    }
                }

                Log::error('PostEx: Failed to fetch operational cities', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            } catch (\Exception $e) {
                Log::error('PostEx Exception: Failed to fetch operational cities', [
                    'message' => $e->getMessage(),
                ]);
            }

            return [];
        });
    }

    /**
     * Get registered pickup addresses.
     */
    public function getPickupAddresses(?string $cityName = null): array
    {
        try {
            $response = $this->client()
                ->get("{$this->baseUrl}/services/integration/api/order/v1/get-merchant-address", array_filter([
                    'cityName' => $cityName,
                ]));

            if ($response->successful()) {
                $data = $response->json();
                if (($data['statusCode'] ?? null) == '200' && isset($data['dist'])) {
                    return $data['dist'];
                }
            }

            Log::error('PostEx: Failed to fetch pickup addresses', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('PostEx Exception: Failed to fetch pickup addresses', [
                'message' => $e->getMessage(),
            ]);
        }

        return [];
    }

    /**
     * Create a new pickup address.
     */
    public function createPickupAddress(array $data): array
    {
        try {
            $response = $this->client()
                ->post("{$this->baseUrl}/services/integration/api/order/v2/create-merchant-address", [
                    'address' => $data['address'],
                    'addressTypeId' => $data['addressTypeId'] ?? 2, // 2 = Pickup
                    'cityName' => $data['cityName'],
                    'contactPersonName' => $data['contactPersonName'],
                    'phone1' => $data['phone1'],
                    'phone2' => $data['phone2'] ?? '',
                    'phone3' => $data['phone3'] ?? '',
                    'wareHouseManagerName' => $data['wareHouseManagerName'] ?? '',
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PostEx: Failed to create pickup address', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('PostEx Exception: Failed to create pickup address', [
                'message' => $e->getMessage(),
            ]);
        }

        return ['statusCode' => '500', 'statusMessage' => 'Request Failed'];
    }

    /**
     * Fetch order types.
     */
    public function getOrderTypes(): array
    {
        try {
            $response = $this->client()
                ->get("{$this->baseUrl}/services/integration/api/order/v1/get-order-types");

            if ($response->successful()) {
                $data = $response->json();
                if (($data['statusCode'] ?? null) == '200' && isset($data['dist'])) {
                    return $data['dist'];
                }
            }
        } catch (\Exception $e) {
            Log::error('PostEx Exception: Failed to fetch order types', [
                'message' => $e->getMessage(),
            ]);
        }

        return ['Normal', 'Reversed', 'Replacement'];
    }

    /**
     * Book order on PostEx.
     */
    public function createOrder(array $data): array
    {
        try {
            $payload = [
                'cityName' => $data['cityName'],
                'customerName' => $data['customerName'],
                'customerPhone' => $data['customerPhone'],
                'deliveryAddress' => $data['deliveryAddress'],
                'invoiceDivision' => (int) ($data['invoiceDivision'] ?? 1),
                'invoicePayment' => (int) $data['invoicePayment'],
                'items' => (int) ($data['items'] ?? 1),
                'orderDetail' => $data['orderDetail'] ?? '',
                'orderRefNumber' => (string) $data['orderRefNumber'],
                'orderType' => $data['orderType'] ?? 'Normal',
                'transactionNotes' => $data['transactionNotes'] ?? '',
                'pickupAddressCode' => $data['pickupAddressCode'] ?? config('postex.default_pickup_address_code'),
            ];

            $response = $this->client()
                ->post("{$this->baseUrl}/services/integration/api/order/v3/create-order", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PostEx: Failed to create order', [
                'status' => $response->status(),
                'payload' => $payload,
                'response' => $response->body(),
            ]);

            return [
                'statusCode' => (string) $response->status(),
                'statusMessage' => $response->json('statusMessage') ?? 'Failed to connect to PostEx',
            ];
        } catch (\Exception $e) {
            Log::error('PostEx Exception: Failed to create order', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
        }

        return ['statusCode' => '500', 'statusMessage' => 'Internal Service Exception'];
    }

    /**
     * Track Order status and history.
     */
    public function trackOrder(string $trackingNumber): array
    {
        try {
            $response = $this->client()
                ->get("{$this->baseUrl}/services/integration/api/order/v1/track-order/{$trackingNumber}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PostEx: Failed to track order', [
                'trackingNumber' => $trackingNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('PostEx Exception: Failed to track order', [
                'trackingNumber' => $trackingNumber,
                'message' => $e->getMessage(),
            ]);
        }

        return ['statusCode' => '500', 'statusMessage' => 'Request Failed'];
    }

    /**
     * Download Airway Bill PDF.
     */
    public function getAirwayBill(string $trackingNumber)
    {
        try {
            // Note: The endpoint uses a hyphen: get-invoice
            $response = $this->client()
                ->get("{$this->baseUrl}/services/integration/api/order/v1/get-invoice", [
                    'trackingNumbers' => $trackingNumber,
                ]);

            if ($response->successful()) {
                return $response->body();
            }

            Log::error('PostEx: Failed to fetch airway bill PDF', [
                'trackingNumber' => $trackingNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('PostEx Exception: Failed to fetch airway bill PDF', [
                'trackingNumber' => $trackingNumber,
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Cancel a booked order.
     */
    public function cancelOrder(string $trackingNumber): array
    {
        try {
            // The cancel API uses PUT. Let's send the trackingNumber as a query string parameter AND request body to ensure compatibility.
            $response = $this->client()
                ->put("{$this->baseUrl}/services/integration/api/order/v1/cancel-order", [
                    'trackingNumber' => $trackingNumber,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PostEx: Failed to cancel order', [
                'trackingNumber' => $trackingNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'statusCode' => (string) $response->status(),
                'statusMessage' => $response->json('statusMessage') ?? 'Failed to cancel order on PostEx',
            ];
        } catch (\Exception $e) {
            Log::error('PostEx Exception: Failed to cancel order', [
                'trackingNumber' => $trackingNumber,
                'message' => $e->getMessage(),
            ]);
        }

        return ['statusCode' => '500', 'statusMessage' => 'Request Failed'];
    }
}
