<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\Product;
use Lunar\FieldTypes\Text;

class ProductScienceAttributesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'KWS001' => [
                'active_ingredients' => 'Alpha Arbutin & Hyaluronic Acid',
                'target_concern' => 'Fades Dark Spots, Hyperpigmentation & Uneven Skin Tone',
                'texture' => 'Lightweight, fast-absorbing watery serum',
                'formula_ph' => '4.8 - 5.2',
            ],
            'KVCS001' => [
                'active_ingredients' => 'Sodium Ascorbyl Phosphate & Vitamin E',
                'target_concern' => 'Brightens Skin, Fights Acne & Boosts Glow',
                'texture' => 'Silky, lightweight liquid',
                'formula_ph' => '6.0 - 6.5',
            ],
            'KAAS' => [
                'active_ingredients' => 'Niacinamide & Hyaluronic Acid',
                'target_concern' => 'Minimizes Large Pores & Controls Excess Oil',
                'texture' => 'Smooth, non-greasy gel-like serum',
                'formula_ph' => '5.5 - 6.0',
            ],
            'KHS001' => [
                'active_ingredients' => 'Vitamin B5 & Pure Hyaluronic Acid',
                'target_concern' => 'Deep Hydration & Skin Barrier Repair',
                'texture' => 'Plush, hydrating watery gel',
                'formula_ph' => '5.0 - 5.5',
            ],
            'KAHA001' => [
                'active_ingredients' => 'Lactic Acid & Hyaluronic Acid',
                'target_concern' => 'Smoothes Skin Texture, Dullness & Even Tone',
                'texture' => 'Ultra-light, watery fluid',
                'formula_ph' => '3.6 - 3.8',
            ],
            'KBHA001' => [
                'active_ingredients' => 'Salicylic Acid 2% & Green Tea Extract',
                'target_concern' => 'Clears Pores, Blackheads & Fights Acne',
                'texture' => 'Clarifying, quick-absorbing fluid',
                'formula_ph' => '3.5 - 4.0',
            ],
            'KVES001' => [
                'active_ingredients' => 'Jojoba, Rosehip, Argan & Grapeseed Oils',
                'target_concern' => 'Dryness, Deep Nourishment & Skin Restoration',
                'texture' => 'Rich, luxurious facial oil',
                'formula_ph' => 'N/A (Water-free)',
            ],
            'KGC001' => [
                'active_ingredients' => 'Sodium Lauroyl Sarcosinate & Coco Glucoside',
                'target_concern' => 'Daily Gentle Cleansing & Dirt Removal',
                'texture' => 'Low-foaming, non-stripping gel-to-foam',
                'formula_ph' => '5.5 (Skin-friendly)',
            ],
            'KGAT001' => [
                'active_ingredients' => 'Glycolic Acid 6% AHA & Aloe Vera',
                'target_concern' => 'Exfoliation, Skin Brightening & Resurfacing',
                'texture' => 'Refreshing watery toner',
                'formula_ph' => '3.5 - 3.7',
            ],
            'KCT001' => [
                'active_ingredients' => 'Cucumber, Rose, Aloe Vera & Menthol',
                'target_concern' => 'Redness, Inflammation & Skin Soothing',
                'texture' => 'Cool, soothing liquid mist',
                'formula_ph' => '5.0 - 5.5',
            ],
            'KMW001' => [
                'active_ingredients' => 'Micellar Molecules & Cucumber Extract',
                'target_concern' => 'Gentle Makeup Removal & Daily Cleansing',
                'texture' => 'Light, clean, no-rinse water',
                'formula_ph' => '5.5 - 6.0',
            ],
            'KRW001' => [
                'active_ingredients' => 'Pure Rose Hydrosol',
                'target_concern' => 'Dehydration, Skin Refreshing & Toning',
                'texture' => 'Fine, aromatic mist',
                'formula_ph' => '5.0 - 5.5',
            ],
            'KAVM001' => [
                'active_ingredients' => 'Pure Aloe Vera Extract',
                'target_concern' => 'Soothing Sunburn, Redness & Irritation',
                'texture' => 'Cool, lightweight hydrating spray',
                'formula_ph' => '4.5 - 5.0',
            ],
            'KDTS001' => [
                'active_ingredients' => 'Cetrimonium Chloride & Panthenol',
                'target_concern' => 'Hair Tangles, Frizz & Breakage',
                'texture' => 'Creamy, rich lathering shampoo',
                'formula_ph' => '5.0 - 5.5',
            ],
            'KADS001' => [
                'active_ingredients' => 'Zinc Pyrithione & Cetrimonium Chloride',
                'target_concern' => 'Scalp Flakes, Dandruff & Itching',
                'texture' => 'Therapeutic, rich lathering shampoo',
                'formula_ph' => '5.5 - 6.0',
            ],
            'KKHM001' => [
                'active_ingredients' => 'Hydrolyzed Keratin, Coconut & Castor Oil',
                'target_concern' => 'Frizz Control, Dryness & Hair Repair',
                'texture' => 'Thick, conditioning treatment cream',
                'formula_ph' => '4.5 - 5.0',
            ],
            'KHRC001' => [
                'active_ingredients' => 'Detangle Shampoo + Keratin Hair Masque',
                'target_concern' => 'Hair Damage, Tangles & Stubborn Frizz',
                'texture' => 'Complete 2-Step Hair Repair Routine',
                'formula_ph' => 'Variable (Product specific)',
            ],
            'KSHRC001' => [
                'active_ingredients' => 'Anti-Dandruff Shampoo + Keratin Hair Masque',
                'target_concern' => 'Flaky Scalp & Damaged Hair Restoration',
                'texture' => 'Complete 2-Step Scalp & Hair Care Routine',
                'formula_ph' => 'Variable (Product specific)',
            ],
            'KCSSK001' => [
                'active_ingredients' => 'Cleanser + BHA + Vitamin C + Hydration Serum',
                'target_concern' => 'Acne-Prone Skin, Dullness & Dehydration',
                'texture' => 'Complete 4-Step Skincare Starter Routine',
                'formula_ph' => 'Variable (Product specific)',
            ],
        ];

        foreach (Product::all() as $product) {
            $sku = $product->variants->first()?->sku;
            if (isset($data[$sku])) {
                $attributeData = $product->attribute_data;
                $productData = $data[$sku];

                foreach ($productData as $key => $val) {
                    $attributeData->put($key, new Text($val));
                }

                $product->attribute_data = $attributeData;
                $product->save();
                $this->command->info("Updated {$product->attr('name')} ($sku) with science-led attributes.");
            }
        }
    }
}
