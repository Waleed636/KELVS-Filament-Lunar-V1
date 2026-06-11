<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;

class ProductShortDescriptionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Find the existing "core" product attribute group ────────────────
        // Lunar seeds a default group with handle "details" or "core" for products.
        // We'll attach to it — or fall back to creating an "Overview" group.
        $group = AttributeGroup::where('handle', 'details')
            ->where('attributable_type', 'product')
            ->first()
            ?? AttributeGroup::where('handle', 'core')
                ->where('attributable_type', 'product')
                ->first()
            ?? AttributeGroup::firstOrCreate(
                [
                    'handle'            => 'overview',
                    'attributable_type' => 'product',
                ],
                [
                    'name'     => ['en' => 'Overview'],
                    'position' => 1,
                ]
            );

        // ── Create the short_description attribute ──────────────────────────
        $attr = Attribute::firstOrCreate(
            [
                'handle'         => 'short_description',
                'attribute_type' => 'product',
            ],
            [
                'attribute_group_id' => $group->id,
                'name'               => ['en' => 'Details'],
                'type'               => \Lunar\FieldTypes\TranslatedText::class,
                'required'           => false,
                'searchable'         => true,
                'filterable'         => false,
                'system'             => false,
                'position'           => 3,   // after name & description
                'configuration'      => [],
            ]
        );

        $this->command->info("✅  short_description attribute created (ID: {$attr->id}).");

        // ── Assign to ALL existing product types ────────────────────────────
        foreach (\Lunar\Models\ProductType::all() as $type) {
            $type->mappedAttributes()->syncWithoutDetaching([$attr->id]);
        }

        $this->command->info('✅  short_description assigned to all product types.');
    }
}
