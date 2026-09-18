<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Lunar\Models\Order;
use App\Mail\OrderShippedMail;
use App\Mail\OrderDeliveredMail;

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

    /**
     * Synchronize shipment status for a single order and trigger automated notifications.
     */
    public function syncOrderShipment(Order $order): array
    {
        $meta = (array) ($order->meta ?? []);
        $trackingNumber = $meta['postex_tracking_number'] ?? null;
        $oldStatus = $meta['postex_status'] ?? 'UnBooked';

        if (!$trackingNumber || strtoupper((string) $trackingNumber) === 'NULL') {
            return ['status' => 'skipped', 'message' => 'No tracking number found'];
        }

        $response = $this->trackOrder($trackingNumber);

        if (($response['statusCode'] ?? null) == '200' && isset($response['dist'])) {
            $dist = $response['dist'];
            $newStatus = $dist['transactionStatus'] ?? null;

            if ($newStatus) {
                $statusChanged = ($newStatus !== $oldStatus);
                $meta['postex_status'] = $newStatus;

                $updateData = ['meta' => $meta];

                if ($newStatus === 'Delivered') {
                    $updateData['status'] = 'payment-received';
                } elseif ($newStatus === 'Returned') {
                    $updateData['status'] = 'returned';
                }

                // Resolve customer recipient email safely
                $shippingAddress = $order->shippingAddress ?: $order->addresses()->where('type', 'shipping')->first();
                $billingAddress = $order->billingAddress ?: $order->addresses()->where('type', 'billing')->first();
                $recipientEmail = $billingAddress?->contact_email ?? $shippingAddress?->contact_email;

                // 1. Trigger OrderShippedMail if parcel is dispatched/active and email not yet sent
                // Note: 'Booked' is excluded as it means registered but not yet picked up/dispatched.
                $activeShippingStatuses = ['In-Transit', 'Arrived at Station', 'Out for Delivery', 'Dispatched'];
                $trackingSentAt = $meta['tracking_email_sent_at'] ?? null;

                if (empty($trackingSentAt) && in_array($newStatus, $activeShippingStatuses)) {
                    if ($recipientEmail && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($recipientEmail)->queue(new OrderShippedMail($order, (string) $trackingNumber));
                            $meta['tracking_email_sent_at'] = now()->toIso8601String();
                            $updateData['meta'] = $meta;
                            Log::info("PostEx Status Sync: Queued OrderShippedMail for Order #{$order->id} to {$recipientEmail}");
                        } catch (\Throwable $e) {
                            Log::error("PostEx Status Sync: Failed to queue OrderShippedMail for Order #{$order->id}: " . $e->getMessage(), [
                                'exception' => $e,
                            ]);
                        }
                    }
                }

                // 2. Trigger OrderDeliveredMail if delivered and email not yet sent
                $deliveredSentAt = $meta['delivered_email_sent_at'] ?? null;
                if (empty($deliveredSentAt) && $newStatus === 'Delivered') {
                    if ($recipientEmail && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($recipientEmail)->queue(new OrderDeliveredMail($order));
                            $meta['delivered_email_sent_at'] = now()->toIso8601String();
                            $updateData['meta'] = $meta;
                            Log::info("PostEx Status Sync: Queued OrderDeliveredMail for Order #{$order->id} to {$recipientEmail}");
                        } catch (\Throwable $e) {
                            Log::error("PostEx Status Sync: Failed to queue OrderDeliveredMail for Order #{$order->id}: " . $e->getMessage(), [
                                'exception' => $e,
                            ]);
                        }
                    }
                }

                $order->update($updateData);

                if ($statusChanged) {
                    Log::info("PostEx Status Sync: Order #{$order->id} status updated from '{$oldStatus}' to '{$newStatus}'");
                    return ['status' => 'updated', 'old' => $oldStatus, 'new' => $newStatus];
                }

                return ['status' => 'synced', 'current' => $newStatus];
            }
        }

        return ['status' => 'failed', 'message' => $response['statusMessage'] ?? 'Failed to fetch tracking data'];
    }

    /**
     * Batch synchronize all active shipments from PostEx.
     */
    public function syncAllActiveShipments(): array
    {
        $orders = Order::all()->filter(function ($order) {
            $meta = (array) ($order->meta ?? []);
            $tracking = $meta['postex_tracking_number'] ?? null;
            $status = $meta['postex_status'] ?? '';

            return !empty($tracking) &&
                   strtoupper((string) $tracking) !== 'NULL' &&
                   !in_array($status, ['Delivered', 'Returned', 'Cancelled']);
        });

        $results = [
            'total' => $orders->count(),
            'updated' => 0,
            'synced' => 0,
            'failed' => 0,
        ];

        foreach ($orders as $order) {
            try {
                $res = $this->syncOrderShipment($order);
                if ($res['status'] === 'updated') {
                    $results['updated']++;
                } elseif ($res['status'] === 'synced') {
                    $results['synced']++;
                } else {
                    $results['failed']++;
                }
            } catch (\Throwable $e) {
                $results['failed']++;
                Log::error("PostEx Batch Sync Exception on Order #{$order->id}: " . $e->getMessage(), [
                    'exception' => $e,
                ]);
            }
        }

        return $results;
    }
}
