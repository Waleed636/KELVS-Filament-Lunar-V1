<?php

namespace App\Console\Commands;

use App\Mail\CartRecoveryMail;
use App\Models\PartialOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Lunar\Models\Order;

class SendCartRecoveryEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carts:send-recovery-emails';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Scan abandoned partial orders and dispatch automated recovery email sequences';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning abandoned carts for recovery emails...');

        $now = now();
        $twoHoursAgo = $now->copy()->subHours(2);
        $threeDaysAgo = $now->copy()->subDays(3);

        // Fetch candidate partial orders
        $partialOrders = PartialOrder::query()
            ->whereNotNull('email')
            ->where('updated_at', '>=', $threeDaysAgo)
            ->where('recovery_email_count', '<', 2)
            ->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($partialOrders as $partialOrder) {
            $email = strtolower(trim((string) $partialOrder->email));

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedCount++;
                continue;
            }

            // Verify the cart has valid items
            if (empty($partialOrder->cart_contents) || !is_array($partialOrder->cart_contents)) {
                $skippedCount++;
                continue;
            }

            // Anti-embarrassment check: verify customer didn't complete an order with this email
            $hasPurchased = Order::whereHas('addresses', function ($q) use ($email) {
                $q->where('contact_email', $email);
            })->where('created_at', '>=', $partialOrder->created_at->subHours(2))->exists();

            if ($hasPurchased) {
                // Customer already purchased; clean up this partial order to prevent any future emails
                $partialOrder->delete();
                $skippedCount++;
                continue;
            }

            $currentCount = (int) $partialOrder->recovery_email_count;
            $shouldSendStep = null;

            // Step 1: 2 hours after abandonment
            if ($currentCount === 0 && $partialOrder->updated_at <= $twoHoursAgo) {
                $shouldSendStep = 1;
            }
            // Step 2: 24 hours after Step 1
            elseif ($currentCount === 1 && $partialOrder->last_recovery_email_sent_at && $partialOrder->last_recovery_email_sent_at <= $now->copy()->subHours(22)) {
                $shouldSendStep = 2;
            }

            if ($shouldSendStep !== null) {
                try {
                    Mail::to($email)->queue(new CartRecoveryMail($partialOrder, $shouldSendStep));

                    $partialOrder->update([
                        'recovery_email_count' => $shouldSendStep,
                        'last_recovery_email_sent_at' => now(),
                    ]);

                    $sentCount++;
                    Log::info("Cart Recovery: Queued Step {$shouldSendStep} email for {$email} (PartialOrder #{$partialOrder->id})");
                    $this->info("Queued Step {$shouldSendStep} email to {$email}");
                } catch (\Throwable $e) {
                    Log::error("Cart Recovery: Failed to send email to {$email}: " . $e->getMessage(), [
                        'exception' => $e,
                        'partial_order_id' => $partialOrder->id,
                    ]);
                }
            }
        }

        $this->info("Completed. Sent: {$sentCount}, Skipped: {$skippedCount}");
        Log::info("Cart Recovery Scan completed. Sent: {$sentCount}, Skipped: {$skippedCount}");
    }
}
