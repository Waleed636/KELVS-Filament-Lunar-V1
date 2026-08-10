<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\Product;
use Lunar\FieldTypes\TranslatedText;
use App\Models\ProductFaq;
use App\Services\ProductDescriptionParser;

class ProductFaqSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $totalFaqsCreated = 0;

        foreach ($products as $product) {
            $rawDesc = (string) $product->attr('description');
            if (empty(trim(strip_tags($rawDesc)))) {
                continue;
            }

            // 1. Extract and seed FAQs with separate Question & Answer columns
            $faqItems = ProductDescriptionParser::extractFaqItems($rawDesc);
            $position = 1;

            foreach ($faqItems as $faq) {
                ProductFaq::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'question'   => $faq['question'],
                    ],
                    [
                        'answer'    => $faq['answer'],
                        'position'  => $position++,
                        'is_active' => true,
                    ]
                );
                $totalFaqsCreated++;
            }

            // 2. Parse sections to populate how_to_use and ingredients_list attributes
            $sections = ProductDescriptionParser::parse($rawDesc);
            $attrData = $product->attribute_data;
            $updated = false;

            foreach ($sections as $section) {
                if ($section['type'] === 'usage' && !$product->attr('how_to_use')) {
                    $attrData->put('how_to_use', new TranslatedText(['en' => $section['content']]));
                    $updated = true;
                }
                if ($section['type'] === 'ingredients' && !$product->attr('ingredients_list')) {
                    $attrData->put('ingredients_list', new TranslatedText(['en' => $section['content']]));
                    $updated = true;
                }
            }

            if ($updated) {
                $product->attribute_data = $attrData;
                $product->save();
            }
        }

        $this->command->info("✅ Processed {$products->count()} products and seeded {$totalFaqsCreated} structured FAQs into the product_faqs table.");
    }
}
