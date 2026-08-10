<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Models\Product;

class ProductFaq extends Model
{
    use HasFactory;

    protected $table = 'product_faqs';

    protected $fillable = [
        'product_id',
        'question',
        'answer',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position'  => 'integer',
    ];

    /**
     * Get the associated product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Scope for active FAQs only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for sorted position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc')->orderBy('id', 'asc');
    }
}
