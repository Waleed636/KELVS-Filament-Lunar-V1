<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Lunar\Models\Collection;
use Lunar\Models\Product;

class Home extends Component
{
    public function render()
    {
        $collections = Collection::take(4)->get();
        
        $products = Product::with(['variants.prices.currency', 'thumbnail', 'urls'])
            ->latest()
            ->take(8)
            ->get();

        $posts = collect();
        if (class_exists(\LaraZeus\Sky\Models\Post::class)) {
            $posts = \LaraZeus\Sky\Models\Post::published()
                ->latest()
                ->take(3)
                ->get();
        }

        return view('livewire.storefront.home', [
            'collections' => $collections,
            'products' => $products,
            'posts' => $posts,
        ])->layout('layouts.storefront');
    }
}
