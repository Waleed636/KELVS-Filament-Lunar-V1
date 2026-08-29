<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MetaCatalogFeedService;
use Illuminate\Support\Facades\File;

class GenerateMetaCatalogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:generate-meta {--format=all : Format to generate (csv, xml, or all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and export Meta Product Catalog data feed (CSV / XML)';

    /**
     * Execute the console command.
     */
    public function handle(MetaCatalogFeedService $feedService): int
    {
        $this->info('🚀 Generating Meta Product Catalog feed from Lunar products...');

        $items = $feedService->getCatalogItems();
        $this->info("📦 Processed {$items->count()} product variants for Meta Catalog.");

        $tableData = $items->map(function ($item) {
            return [
                'ID / SKU'     => $item['id'],
                'Title'        => \Illuminate\Support\Str::limit($item['title'], 35),
                'Price'        => $item['price'],
                'Sale Price'   => $item['sale_price'] ?: '-',
                'Availability' => $item['availability'],
                'Category'     => \Illuminate\Support\Str::limit($item['fb_product_category'], 25),
            ];
        })->toArray();

        $this->table(['ID / SKU', 'Title', 'Price', 'Sale Price', 'Availability', 'Category'], $tableData);

        // Ensure directory exists
        $feedsDir = public_path('feeds');
        if (!File::isDirectory($feedsDir)) {
            File::makeDirectory($feedsDir, 0755, true);
        }

        $format = strtolower($this->option('format') ?: 'all');

        if ($format === 'all' || $format === 'csv') {
            $csvPath = public_path('feeds/meta-catalog.csv');
            File::put($csvPath, $feedService->generateCsv());
            $this->info("✅ CSV feed successfully saved to: {$csvPath}");
        }

        if ($format === 'all' || $format === 'xml') {
            $xmlPath = public_path('feeds/meta-catalog.xml');
            File::put($xmlPath, $feedService->generateXml());
            $this->info("✅ XML feed successfully saved to: {$xmlPath}");
        }

        $this->info('🎉 Meta Catalog generated successfully! You can now configure this URL in Meta Commerce Manager.');

        return Command::SUCCESS;
    }
}
