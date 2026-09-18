<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Lunar\Admin\Filament\Resources\OrderResource\Pages\ListOrders as BaseListOrders;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\PostExService;

class ListOrders extends BaseListOrders
{
    protected static string $resource = \App\Filament\Resources\OrderResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(parent::getHeaderActions(), [
            Action::make('sync_postex')
                ->label('Sync PostEx Shipments')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Sync PostEx Tracking Statuses')
                ->modalDescription('This will query PostEx for all active shipments, update their statuses, and automatically queue tracking/delivery emails.')
                ->action(function (PostExService $postExService) {
                    $results = $postExService->syncAllActiveShipments();

                    Notification::make()
                        ->title('PostEx Sync Completed')
                        ->body("Processed {$results['total']} active shipment(s). Updated: {$results['updated']}, Synced: {$results['synced']}, Errors: {$results['failed']}.")
                        ->success()
                        ->send();
                }),
        ]);
    }
}
