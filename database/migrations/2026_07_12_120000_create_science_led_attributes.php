<?php

use Illuminate\Database\Migrations\Migration;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\ProductType;

return new class extends Migration
{
    public function up(): void
    {
        // Find attribute group for product details/overview
        $group = AttributeGroup::where('handle', 'details')
            ->where('attributable_type', 'product')
            ->first()
            ?? AttributeGroup::where('handle', 'core')
                ->where('attributable_type', 'product')
                ->first()
            ?? AttributeGroup::where('handle', 'overview')
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

        $attributes = [
            [
                'handle' => 'formula_ph',
                'name' => ['en' => 'Formula pH'],
                'type' => \Lunar\FieldTypes\Text::class,
                'position' => 10,
            ],
            [
                'handle' => 'active_ingredients',
                'name' => ['en' => 'Active Ingredients'],
                'type' => \Lunar\FieldTypes\Text::class,
                'position' => 11,
            ],
            [
                'handle' => 'target_concern',
                'name' => ['en' => 'Target Concern'],
                'type' => \Lunar\FieldTypes\Text::class,
                'position' => 12,
            ],
            [
                'handle' => 'texture',
                'name' => ['en' => 'Texture'],
                'type' => \Lunar\FieldTypes\Text::class,
                'position' => 13,
            ],
        ];

        $attributeIds = [];
        foreach ($attributes as $data) {
            $attr = Attribute::firstOrCreate(
                [
                    'handle'            => $data['handle'],
                    'attribute_type'    => 'product',
                ],
                [
                    'attribute_group_id' => $group->id,
                    'name'               => $data['name'],
                    'type'               => $data['type'],
                    'required'           => false,
                    'searchable'         => false,
                    'filterable'         => false,
                    'system'             => false,
                    'position'           => $data['position'],
                    'configuration'      => [],
                ]
            );
            $attributeIds[] = $attr->id;
        }

        // Assign to all existing product types
        foreach (ProductType::all() as $type) {
            $type->mappedAttributes()->syncWithoutDetaching($attributeIds);
        }
    }

    public function down(): void
    {
        $handles = ['formula_ph', 'active_ingredients', 'target_concern', 'texture'];
        $attributes = Attribute::where('attribute_type', 'product')
            ->whereIn('handle', $handles)
            ->get();

        foreach ($attributes as $attr) {
            // Detach from product types mapping
            foreach (ProductType::all() as $type) {
                $type->mappedAttributes()->detach($attr->id);
            }
            $attr->delete();
        }
    }
};
