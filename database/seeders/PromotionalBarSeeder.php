<?php

namespace Database\Seeders;

use App\Models\PromotionalBar;
use Illuminate\Database\Seeder;

class PromotionalBarSeeder extends Seeder
{
    public function run(): void
    {
        PromotionalBar::updateOrCreate(
            ['id' => 1],
            [
                'content' => '🔥 FREE Shipping on all orders over Rs. 3,000 across Pakistan!',
                'badge_text' => 'FREE SHIPPING',
                'button_text' => 'Shop Now',
                'button_url' => '/shop',
                'promo_code' => 'FREESHIP',
                'bg_color' => '#111111',
                'text_color' => '#ffffff',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        PromotionalBar::updateOrCreate(
            ['id' => 2],
            [
                'content' => '⚡ Special Launch Offer: Get 15% Off your order!',
                'badge_text' => 'LIMITED TIME',
                'button_text' => 'Explore Shop',
                'button_url' => '/shop',
                'promo_code' => 'KELVS15',
                'bg_color' => '#0f172a',
                'text_color' => '#f8fafc',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }
}
