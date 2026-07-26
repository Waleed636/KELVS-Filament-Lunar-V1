<?php

namespace App\Filament\Resources\PromotionalBarResource\Pages;

use App\Filament\Resources\PromotionalBarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPromotionalBars extends ListRecords
{
    protected static string $resource = PromotionalBarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
