<?php

namespace App\Filament\Resources;

use LaraZeus\Sky\Filament\Resources\PageResource as BasePageResource;

class PageResource extends BasePageResource
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    public static function getNavigationLabel(): string
    {
        return __('Policies');
    }

    public static function getLabel(): string
    {
        return __('Policy');
    }

    public static function getPluralLabel(): string
    {
        return __('Policies');
    }
}
