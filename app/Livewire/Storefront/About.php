<?php

namespace App\Livewire\Storefront;

use Livewire\Component;

class About extends Component
{
    public function render()
    {
        return view('livewire.storefront.about')
            ->layout('layouts.storefront');
    }
}
