<?php

namespace App\Filament\Pages;

use App\Models\ProductFaq;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Lunar\Models\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GoogleQuestionExplorer extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    protected static ?string $navigationGroup = 'Shop Management';

    protected static ?string $navigationLabel = 'Google Question Explorer';

    protected static ?string $title = 'Google Search & Question Explorer';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.google-question-explorer';

    public static function getNavigationUrl(): string
    {
        try {
            return static::getUrl();
        } catch (\Throwable $e) {
            return url('/admin/google-question-explorer');
        }
    }

    public string $keyword = '';

    public ?string $selectedProductId = null;

    public string $country = 'pk';

    public array $results = [];

    public int $totalCount = 0;

    public string $searchedKeyword = '';

    public array $products = [];

    public function mount(): void
    {
        // Load existing store products for quick selection
        try {
            $this->products = Product::query()
                ->get()
                ->mapWithKeys(function ($p) {
                    $name = $p->attr('name') ?? "Product #{$p->id}";
                    return [$p->id => $name];
                })
                ->toArray();
        } catch (\Throwable $e) {
            $this->products = [];
        }

        // Default suggestion
        $this->keyword = 'vitamin c serum';
    }

    public function updatedSelectedProductId($value): void
    {
        if ($value && isset($this->products[$value])) {
            $productName = $this->products[$value];
            // Clean up name for search keyword e.g. "KELVS Vitamin C Serum | SAP..." -> "vitamin c serum"
            $cleaned = preg_replace('/^KELVS\s+/i', '', $productName);
            $cleaned = explode('|', $cleaned)[0];
            $cleaned = explode('-', $cleaned)[0];
            $this->keyword = strtolower(trim($cleaned));
        }
    }

    public function search(): void
    {
        $term = trim($this->keyword);

        if (empty($term)) {
            Notification::make()
                ->title('Please enter a search keyword')
                ->warning()
                ->send();
            return;
        }

        $this->searchedKeyword = $term;

        $questionPrefixes = [
            'How' => ['how', 'how to use', 'how often', 'how long', 'how much'],
            'What & Which' => ['what', 'what does', 'what is', 'which'],
            'Why & When' => ['why', 'why use', 'when to use', 'should i'],
            'Can / Is / Does' => ['can', 'is', 'does'],
            'Buyer Intent & Pakistan' => [
                'best', 
                'price in pakistan', 
                'benefits of', 
                'side effects of', 
                'for acne', 
                'for glowing skin',
                'derma'
            ],
        ];

        $results = [];
        $total = 0;
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
        ];

        foreach ($questionPrefixes as $category => $prefixes) {
            $results[$category] = [];

            foreach ($prefixes as $p) {
                $query = $p . ' ' . $term;
                $url = 'https://suggestqueries.google.com/complete/search?client=firefox&gl=' . urlencode($this->country) . '&hl=en&q=' . urlencode($query);

                try {
                    $response = Http::withoutVerifying()
                        ->timeout(4)
                        ->withHeaders($headers)
                        ->get($url);

                    if ($response->successful()) {
                        $data = $response->json();
                        if (!empty($data[1]) && is_array($data[1])) {
                            foreach ($data[1] as $item) {
                                $itemClean = trim(html_entity_decode($item, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                                if (!empty($itemClean) && !in_array($itemClean, $results[$category], true)) {
                                    $results[$category][] = $itemClean;
                                    $total++;
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Continue with next
                }
            }
        }

        $this->results = $results;
        $this->totalCount = $total;

        Notification::make()
            ->title("Found {$total} Google questions for \"{$term}\"")
            ->success()
            ->send();
    }

    public function addAsProductFaq(string $question): void
    {
        if (!$this->selectedProductId) {
            Notification::make()
                ->title('Please select a product first')
                ->body('Choose a product from the dropdown above to attach this question directly to it.')
                ->warning()
                ->send();
            return;
        }

        $exists = ProductFaq::where('product_id', $this->selectedProductId)
            ->where('question', $question)
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Question already exists for this product')
                ->info()
                ->send();
            return;
        }

        ProductFaq::create([
            'product_id' => $this->selectedProductId,
            'question'   => $question,
            'answer'     => 'Draft answer: Add product-specific information here.',
            'is_active'  => false, // draft state until user edits answer
            'position'   => 99,
        ]);

        $productName = $this->products[$this->selectedProductId] ?? "Product #{$this->selectedProductId}";

        Notification::make()
            ->title('Question added to Product FAQs!')
            ->body("Added as draft to \"{$productName}\". You can now edit its answer under Product FAQs.")
            ->success()
            ->send();
    }

    public function exportTxt(): StreamedResponse
    {
        $term = $this->searchedKeyword ?: $this->keyword;
        $slug = Str::slug($term);
        $filename = "google_questions_{$slug}_{$this->country}.txt";

        $lines = [];
        $lines[] = "=======================================================";
        $lines[] = " Google Questions for: {$term} (Country: {$this->country})";
        $lines[] = " Generated on: " . now()->toDateTimeString();
        $lines[] = " Total Discovered: {$this->totalCount}";
        $lines[] = "=======================================================\n";

        foreach ($this->results as $category => $items) {
            if (empty($items)) {
                continue;
            }
            $count = count($items);
            $lines[] = "── [{$category}] ({$count} queries) ───────────────────────────";
            foreach ($items as $item) {
                $lines[] = "• " . $item;
            }
            $lines[] = "";
        }

        $content = implode("\n", $lines);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
