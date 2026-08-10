<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\ProductType;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create product_faqs table
        if (!Schema::hasTable('product_faqs')) {
            Schema::create('product_faqs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')
                    ->constrained('lunar_products')
                    ->cascadeOnDelete();
                $table->string('question', 500);
                $table->longText('answer');
                $table->integer('position')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['product_id', 'is_active', 'position']);
            });
        }

        // 2. Register Lunar Product Attributes for "how_to_use" and "ingredients_list"
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
                'handle'   => 'how_to_use',
                'name'     => ['en' => 'How to Use & Routine'],
                'type'     => \Lunar\FieldTypes\TranslatedText::class,
                'position' => 20,
            ],
            [
                'handle'   => 'ingredients_list',
                'name'     => ['en' => 'Full Ingredients (INCI)'],
                'type'     => \Lunar\FieldTypes\TranslatedText::class,
                'position' => 21,
            ],
        ];

        $attributeIds = [];
        foreach ($attributes as $data) {
            $attr = Attribute::firstOrCreate(
                [
                    'handle'         => $data['handle'],
                    'attribute_type' => 'product',
                ],
                [
                    'attribute_group_id' => $group->id,
                    'name'               => $data['name'],
                    'type'               => $data['type'],
                    'required'           => false,
                    'searchable'         => true,
                    'filterable'         => false,
                    'system'             => false,
                    'position'           => $data['position'],
                    'configuration'      => ['richtext' => true],
                ]
            );
            $attributeIds[] = $attr->id;
        }

        // Map to all existing product types
        foreach (ProductType::all() as $type) {
            $type->mappedAttributes()->syncWithoutDetaching($attributeIds);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_faqs');

        $handles = ['how_to_use', 'ingredients_list'];
        $attributes = Attribute::where('attribute_type', 'product')
            ->whereIn('handle', $handles)
            ->get();

        foreach ($attributes as $attr) {
            foreach (ProductType::all() as $type) {
                $type->mappedAttributes()->detach($attr->id);
            }
            $attr->delete();
        }
    }
};
