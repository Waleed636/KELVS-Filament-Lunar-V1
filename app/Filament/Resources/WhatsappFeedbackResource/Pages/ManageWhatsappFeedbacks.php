<?php

namespace App\Filament\Resources\WhatsappFeedbackResource\Pages;

use App\Filament\Resources\WhatsappFeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWhatsappFeedbacks extends ManageRecords
{
    protected static string $resource = WhatsappFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
