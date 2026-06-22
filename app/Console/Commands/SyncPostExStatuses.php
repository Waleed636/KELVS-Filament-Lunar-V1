<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lunar\Models\Order;
use App\Services\PostExService;
use Illuminate\Support\Facades\Log;

class SyncPostExStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postex:sync-statuses';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Sync logistics shipment statuses of booked orders from PostEx';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting PostEx Status Sync...');

        $postExService = app(PostExService::class);

        // Fetch all orders with a PostEx tracking number
        // We filter out orders in terminal states to save API requests.
        $orders = Order::all()
            ->filter(function ($order) {
                $meta = $order->meta ?? [];
                $tracking = $meta['postex_tracking_number'] ?? null;
                $status = $meta['postex_status'] ?? '';
                
                return !empty($tracking) && 
                       strtoupper($tracking) !== 'NULL' && 
                       !in_array($status, ['Delivered', 'Returned', 'Cancelled']);
            });

        $this->info("Found {$orders->count()} active shipments to sync.");

        $updatedCount = 0;

        foreach ($orders as $order) {
            $trackingNumber = $order->meta['postex_tracking_number'];
            $oldStatus = $order->meta['postex_status'] ?? 'UnBooked';

            $this->comment("Syncing Order #{$order->id} (Tracking: {$trackingNumber})...");

            $response = $postExService->trackOrder($trackingNumber);

            if (($response['statusCode'] ?? null) == '200' && isset($response['dist'])) {
                $dist = $response['dist'];
                $newStatus = $dist['transactionStatus'] ?? null;

                if ($newStatus && $newStatus !== $oldStatus) {
                    $meta = $order->meta ?? [];
                    $meta['postex_status'] = $newStatus;

                    $updateData = ['meta' => $meta];

                    // If order is delivered, update Lunar order status to payment-received
                    if ($newStatus === 'Delivered') {
                        $updateData['status'] = 'payment-received';
                    }

                    // If order is returned, update Lunar order status to returned
                    if ($newStatus === 'Returned') {
                        $updateData['status'] = 'returned';
                    }

                    $order->update($updateData);

                    $this->info("Updated Order #{$order->id}: Status changed from '{$oldStatus}' to '{$newStatus}'");
                    Log::info("PostEx Status Sync: Order #{$order->id} tracking {$trackingNumber} changed from '{$oldStatus}' to '{$newStatus}'");

                    $updatedCount++;
                }
            } else {
                $this->error("Failed to fetch tracking data for Order #{$order->id}");
            }
        }

        $this->info("PostEx Status Sync complete. Updated {$updatedCount} orders.");
    }
}
