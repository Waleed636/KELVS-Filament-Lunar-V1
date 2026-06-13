<?php

namespace App\Console\Commands;

use App\Models\EmailSubscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportOldSubscribers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-subscribers:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import old email subscribers from kelvsint.sql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path('kelvsint.sql');

        if (!File::exists($filePath)) {
            $this->error("The file kelvsint.sql was not found in the root directory: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Parsing kelvsint.sql for email subscribers...");

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("Failed to open kelvsint.sql for reading.");
            return Command::FAILURE;
        }

        $inInsertBlock = false;
        $importedCount = 0;
        $skippedCount = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Check if we are starting the insert statement for email_subscribers
            if (str_contains($line, 'INSERT INTO `email_subscribers`')) {
                $inInsertBlock = true;
                continue;
            }

            if ($inInsertBlock) {
                // Check if this line looks like an insert value row
                // e.g. (3, 'areeshaaslam786pak@gmail.com', 'popup', 'WELCOME10', '2026-04-04 18:21:14'),
                // Or (70, 'muhammadimranawan243@gmail.com', 'popup', 'WELCOME10', '2026-06-06 14:11:44');
                
                // Regular expression to match:
                // (id, 'email', 'source', 'discount_code', 'subscribed_at')[,;]
                // Note: subscribed_at could be NULL or a timestamp.
                // In kelvsint.sql it is 'YYYY-MM-DD HH:MM:SS'
                if (preg_match("/\((\d+),\s*'([^']*)',\s*'([^']*)',\s*'([^']*)',\s*'([^']*)'\)[,;]/", $line, $matches)) {
                    $id = (int)$matches[1];
                    $email = trim($matches[2]);
                    $source = trim($matches[3]);
                    $discountCode = trim($matches[4]);
                    $subscribedAt = trim($matches[5]);

                    // Use updateOrCreate to avoid duplicates
                    EmailSubscriber::updateOrCreate(
                        ['email' => $email],
                        [
                            'id' => $id,
                            'source' => $source,
                            'discount_code' => $discountCode,
                            'subscribed_at' => $subscribedAt,
                        ]
                    );

                    $importedCount++;
                } else {
                    // If we encounter a semicolon at the end of the line or a line that does not match, 
                    // and it marks the end of the insert block, we exit the block.
                    if (str_ends_with($line, ';') || !str_starts_with($line, '(')) {
                        $inInsertBlock = false;
                    }
                }
            }
        }

        fclose($handle);

        $this->info("Import completed successfully!");
        $this->info("Imported/Updated: {$importedCount} subscribers.");

        return Command::SUCCESS;
    }
}
