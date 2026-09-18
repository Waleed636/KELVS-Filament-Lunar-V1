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

        try {
            $results = $postExService->syncAllActiveShipments();

            $this->info("PostEx Status Sync complete.");
            $this->table(
                ['Active Shipments Found', 'Statuses Updated', 'Unchanged / Synced', 'Errors / Failed'],
                [[$results['total'], $results['updated'], $results['synced'], $results['failed']]]
            );

            Log::info("PostEx Status Sync completed via artisan command.", $results);
        } catch (\Throwable $e) {
            $this->error('PostEx Status Sync encountered an error: ' . $e->getMessage());
            Log::error('PostEx Status Sync Command Error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
