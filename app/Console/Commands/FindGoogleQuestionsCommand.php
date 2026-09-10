<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FindGoogleQuestionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kelvs:find-questions 
                            {keyword : The search term e.g. "vitamin c serum"} 
                            {--country=pk : Country code (pk for Pakistan, us, etc.)} 
                            {--save : Save the output to a text file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape free Google Autocomplete & People questions for any product or keyword';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $keyword = trim($this->argument('keyword'));
        $country = strtolower($this->option('country') ?: 'pk');

        if (empty($keyword)) {
            $this->error('Please provide a keyword. Example: php artisan kelvs:find-questions "vitamin c serum"');
            return Command::FAILURE;
        }

        $this->info("Fetching live Google autocomplete questions for [{$keyword}] (Country: {$country})...\n");

        $questionPrefixes = [
            'How'       => ['how', 'how to use', 'how often', 'how long'],
            'What'      => ['what', 'what does', 'what is', 'which'],
            'Why'       => ['why', 'why use'],
            'Can / Is'  => ['can', 'is', 'does'],
            'Where'     => ['where to buy', 'where'],
            'Intent'    => ['best', 'price in pakistan', 'benefits of', 'side effects of', 'for acne', 'for glowing skin'],
        ];

        $results = [];
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        ];

        foreach ($questionPrefixes as $category => $prefixes) {
            $results[$category] = [];

            foreach ($prefixes as $p) {
                $query = $p . ' ' . $keyword;
                $url = 'https://suggestqueries.google.com/complete/search?client=firefox&gl=' . urlencode($country) . '&hl=en&q=' . urlencode($query);

                try {
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                        ->timeout(5)
                        ->withHeaders(['User-Agent' => $headers[0]])
                        ->get($url);

                    if ($response->successful()) {
                        $data = $response->json();
                        if (!empty($data[1]) && is_array($data[1])) {
                            foreach ($data[1] as $item) {
                                $itemClean = trim(html_entity_decode($item, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                                if (!empty($itemClean) && !in_array($itemClean, $results[$category], true)) {
                                    $results[$category][] = $itemClean;
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore transient network blips
                }
            }
        }

        $this->newLine(1);

        $totalQueries = 0;
        $outputLines = [];
        $outputLines[] = "=======================================================";
        $outputLines[] = " Google Pakistan Search Questions for: {$keyword}";
        $outputLines[] = " Generated on: " . now()->toDateTimeString();
        $outputLines[] = "=======================================================\n";

        foreach ($results as $category => $items) {
            if (empty($items)) {
                continue;
            }

            $count = count($items);
            $totalQueries += $count;

            $this->line("<fg=yellow;options=bold>── [{$category}] ({$count} queries) ───────────────────────────</>");
            $outputLines[] = "── [{$category}] ({$count} queries) ───────────────────────────";

            foreach ($items as $idx => $item) {
                $this->line("  <fg=green>•</> {$item}");
                $outputLines[] = "  • {$item}";
            }
            $this->newLine();
            $outputLines[] = "";
        }

        $this->info("Total unique questions & search queries discovered: {$totalQueries}");

        if ($this->option('save')) {
            $filename = 'google_questions_' . \Illuminate\Support\Str::slug($keyword) . '.txt';
            $filePath = base_path($filename);
            file_put_contents($filePath, implode("\n", $outputLines));
            $this->info("Saved to file: <fg=cyan>{$filename}</>");
        }

        return Command::SUCCESS;
    }
}
