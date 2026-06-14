<?php

namespace App\Filament\Resources\PartialOrderResource\Pages;

use App\Filament\Resources\PartialOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartialOrders extends ListRecords
{
    protected static string $resource = PartialOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No manual creation since partial orders are captured automatically at checkout
        ];
    }
}
