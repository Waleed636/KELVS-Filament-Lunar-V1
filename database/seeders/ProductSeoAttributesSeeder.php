<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;

class ProductSeoAttributesSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Find or create the SEO attribute group for Products ──────────────
        $group = AttributeGroup::firstOrCreate(
            [
                'handle'            => 'seo',
                'attributable_type' => 'product',
            ],
            [
                'name'     => ['en' => 'SEO'],
                'position' => 99,
            ]
        );

        // ── 2. Define the SEO attributes we want ────────────────────────────────
        $attributes = [
            [
                'handle'       => 'seo_title',
                'name'         => ['en' => 'SEO Title'],
                'type'         => \Lunar\FieldTypes\Text::class,
                'required'     => false,
                'searchable'   => true,
                'filterable'   => false,
                'system'       => false,
                'position'     => 1,
                'configuration' => ['max' => 60],
            ],
            [
                'handle'       => 'seo_description',
                'name'         => ['en' => 'SEO Description'],
                'type'         => \Lunar\FieldTypes\Text::class,
                'required'     => false,
                'searchable'   => false,
                'filterable'   => false,
                'system'       => false,
                'position'     => 2,
                'configuration' => ['max' => 160],
            ],
            [
                'handle'       => 'seo_keywords',
                'name'         => ['en' => 'SEO Keywords'],
                'type'         => \Lunar\FieldTypes\Text::class,
                'required'     => false,
                'searchable'   => false,
                'filterable'   => false,
                'system'       => false,
                'position'     => 3,
                'configuration' => [],
            ],
            [
                'handle'       => 'canonical_url',
                'name'         => ['en' => 'Canonical URL'],
                'type'         => \Lunar\FieldTypes\Text::class,
                'required'     => false,
                'searchable'   => false,
                'filterable'   => false,
                'system'       => false,
                'position'     => 4,
                'configuration' => [],
            ],
        ];

        foreach ($attributes as $data) {
            Attribute::firstOrCreate(
                [
                    'handle'            => $data['handle'],
                    'attribute_type'    => 'product',
                ],
                [
                    'attribute_group_id' => $group->id,
                    'name'               => $data['name'],
                    'type'               => $data['type'],
                    'required'           => $data['required'],
                    'searchable'         => $data['searchable'],
                    'filterable'         => $data['filterable'],
                    'system'             => $data['system'],
                    'position'           => $data['position'],
                    'configuration'      => $data['configuration'],
                ]
            );
        }

        $this->command->info('✅  SEO attributes seeded for Lunar products.');

        // ── 3. Assign the new attributes to ALL existing Product Types ──────────
        $productTypes = \Lunar\Models\ProductType::all();
        foreach ($productTypes as $type) {
            $seoAttributeIds = Attribute::where('attribute_type', 'product')
                ->where('attribute_group_id', $group->id)
                ->pluck('id');

            // syncWithoutDetaching keeps existing attribute assignments intact
            $type->mappedAttributes()->syncWithoutDetaching($seoAttributeIds);
        }

        $this->command->info('✅  SEO attributes assigned to all existing product types.');
    }
}
