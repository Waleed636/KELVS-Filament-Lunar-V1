<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PromotionalBar extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'badge_text',
        'button_text',
        'button_url',
        'promo_code',
        'bg_color',
        'text_color',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Clear active promotional bars cache when record is created/updated/deleted.
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('active_promotional_bars');
        });

        static::deleted(function () {
            Cache::forget('active_promotional_bars');
        });
    }

    /**
     * Scope query to only fetch currently active promotional bars.
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc');
    }
}
