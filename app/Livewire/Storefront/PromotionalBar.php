<?php

namespace App\Livewire\Storefront;

use App\Models\PromotionalBar as PromotionalBarModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class PromotionalBar extends Component
{
    public function render()
    {
        // Safety check to prevent 500 error on live server if migration hasn't run yet
        if (!Schema::hasTable('promotional_bars')) {
            return view('livewire.storefront.promotional-bar', [
                'bars' => collect(),
            ]);
        }

        // Cache active promotional bars for 1 hour for zero-latency rendering
        $bars = Cache::remember('active_promotional_bars', 3600, function () {
            return PromotionalBarModel::active()->get();
        });

        return view('livewire.storefront.promotional-bar', [
            'bars' => $bars,
        ]);
    }
}
