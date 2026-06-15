<?php

namespace App\Livewire\Storefront;

use Livewire\Component;

class About extends Component
{
    public function render()
    {
        return view('livewire.storefront.about')
            ->layout('layouts.storefront', [
                'seoTitle'       => 'Our Science & Story | About KELVS',
                'seoDescription' => 'Learn about KELVS, a brand rooted in science. Discover our commitment to dermatologically inspired formulas made with clean, high-efficacy active ingredients.',
                'seoKeywords'    => 'about KELVS, science led skincare, clean skincare ingredients, skincare lab, dermatologist tested, KELVS philosophy',
            ]);
    }
}
