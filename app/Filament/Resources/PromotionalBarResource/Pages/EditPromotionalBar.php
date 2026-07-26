<?php

namespace App\Filament\Resources\PromotionalBarResource\Pages;

use App\Filament\Resources\PromotionalBarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromotionalBar extends EditRecord
{
    protected static string $resource = PromotionalBarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
