<?php

namespace App\Console\Commands;

use App\Mail\CustomerWinBackMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Lunar\Models\Order;

class SendCustomerWinBackEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:send-winback-emails 
                            {--dry-run : Simulate the scan and display eligible customers without sending any emails}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Automatically scan all past customers whose last purchase was 30+ days ago and send win-back offer (MISSYOU10)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running in DRY RUN mode. No emails will actually be sent.');
        }

        $now = now();
        $thirtyDaysAgo = $now->copy()->subDays(30);

        $this->info('Automatically scanning all past customers inactive for 30+ days...');

        // Fetch candidate orders placed at least 30 days ago (no manual flags needed)
        $candidateOrders = Order::with('addresses')
            ->where('created_at', '<=', $thirtyDaysAgo)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->latest('created_at')
            ->get();

        $processedEmails = [];
        $sentCount = 0;
        $skippedCount = 0;

        foreach ($candidateOrders as $order) {
            $shippingAddress = $order->shippingAddress ?: $order->addresses->firstWhere('type', 'shipping');
            $billingAddress = $order->billingAddress ?: $order->addresses->firstWhere('type', 'billing');

            $email = strtolower(trim((string) ($billingAddress?->contact_email ?? $shippingAddress?->contact_email)));

            // Validate email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedCount++;
                continue;
            }

            // Deduplicate per run
            if (in_array($email, $processedEmails)) {
                $skippedCount++;
                continue;
            }
            $processedEmails[] = $email;

            // Guard 1: Anti-Spam check - verify if this order was already flagged
            $meta = (array) ($order->meta ?? []);
            if (!empty($meta['winback_email_sent_at'])) {
                $skippedCount++;
                continue;
            }

            // Guard 2: Verify the customer hasn't placed ANY newer order in the last 30 days
            $hasNewerOrder = Order::whereHas('addresses', function ($q) use ($email) {
                $q->where('contact_email', $email);
            })
            ->where('created_at', '>', $thirtyDaysAgo)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->exists();

            if ($hasNewerOrder) {
                $skippedCount++;
                continue;
            }

            // Customer is eligible!
            $customerName = $shippingAddress?->first_name 
                ?? $billingAddress?->first_name 
                ?? 'there';

            if ($dryRun) {
                $sentCount++;
                $this->line(" [DRY-RUN] Would queue Win-Back email to: {$email} (Customer: {$customerName}, Order #{$order->id} on {$order->created_at->format('Y-m-d')})");
                continue;
            }

            try {
                Mail::to($email)->queue(new CustomerWinBackMail($customerName, $email, $order, 'MISSYOU10'));

                $meta['winback_email_sent_at'] = now()->toIso8601String();
                $order->update(['meta' => $meta]);

                $sentCount++;
                $this->info("Queued Win-Back email to {$email} (Order #{$order->id})");
                Log::info("Customer Win-Back: Queued email to {$email} for Order #{$order->id}");
            } catch (\Throwable $e) {
                Log::error("Customer Win-Back Error sending to {$email}: " . $e->getMessage(), [
                    'order_id' => $order->id,
                    'exception' => $e,
                ]);
            }
        }

        $this->info("Win-Back Scan complete. Queued: {$sentCount}, Skipped: {$skippedCount}");
        Log::info("Customer Win-Back scan complete. Queued: {$sentCount}, Skipped: {$skippedCount}");
    }
}
