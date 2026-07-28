<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('lunar_products')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('image_path');
            $table->text('caption')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_feedbacks');
    }
};
