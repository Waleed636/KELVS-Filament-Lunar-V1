<?php

namespace App\Livewire\Storefront;

use App\Models\Post;
use Livewire\Component;

class PageShow extends Component
{
    public string $slug;
    public ?Post $page = null;

    public function mount(string $slug)
    {
        $this->slug = $slug;

        $this->page = Post::where('post_type', 'page')
            ->where('status', 'publish')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function render()
    {
        $allPages = Post::where('post_type', 'page')
            ->where('status', 'publish')
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);

        $titleStr = is_array($this->page->title)
            ? ($this->page->title[app()->getLocale()] ?? $this->page->title['en'] ?? reset($this->page->title))
            : $this->page->title;

        return view('livewire.storefront.page-show', [
            'allPages' => $allPages,
        ])
        ->layout('layouts.storefront', [
            'title' => $titleStr . ' | KELVS Skincare',
        ]);
    }
}
