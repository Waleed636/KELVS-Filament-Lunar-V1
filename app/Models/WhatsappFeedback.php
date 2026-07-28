<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Models\Product;

class WhatsappFeedback extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_feedbacks';

    protected $fillable = [
        'product_id',
        'customer_name',
        'image_path',
        'caption',
        'rating',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the associated product if specified.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Scope query to active feedback ordered by sort_order and latest.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc');
    }
}
