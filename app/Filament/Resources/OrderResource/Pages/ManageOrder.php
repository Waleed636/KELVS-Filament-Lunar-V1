<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Lunar\Admin\Filament\Resources\OrderResource\Pages\ManageOrder as BaseManageOrder;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Services\PostExService;

class ManageOrder extends BaseManageOrder
{
    protected static string $resource = \App\Filament\Resources\OrderResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(parent::getHeaderActions(), [
            $this->getBookPostExAction(),
            $this->getDownloadPostExAirwayBillAction(),
            $this->getCancelPostExBookingAction(),
            $this->getSyncPostExStatusAction(),
        ]);
    }

    /**
     * Book order on PostEx.
     */
    protected function getBookPostExAction(): Action
    {
        return Action::make('book_postex')
            ->label('Book with PostEx')
            ->modalHeading('Book Order on PostEx')
            ->modalSubmitActionLabel('Confirm Booking')
            ->icon('heroicon-o-truck')
            ->color('success')
            ->visible(function () {
                $meta = $this->record->meta ?? [];
                return !isset($meta['postex_tracking_number']);
            })
            ->form(function () {
                $postExService = app(PostExService::class);

                // 1. Get operational cities
                $rawCities = $postExService->getOperationalCities('delivery');
                $cities = [];
                foreach ($rawCities as $city) {
                    $name = $city['operationalCityName'];
                    $cities[$name] = $name;
                }

                // 2. Get pickup addresses
                $rawPickups = $postExService->getPickupAddresses();
                $pickups = [];
                foreach ($rawPickups as $pickup) {
                    $pickups[$pickup['addressCode']] = $pickup['address'] . ' (' . $pickup['cityName'] . ')';
                }

                // Match default city (case-insensitive)
                $shippingCity = $this->record->shippingAddress?->city ?? '';
                $defaultCity = collect($rawCities)->first(fn($c) => strtolower(trim($c['operationalCityName'])) === strtolower(trim($shippingCity)))['operationalCityName'] ?? null;

                // Items count
                $itemsCount = $this->record->lines->filter(fn ($line) => $line->type !== 'shipping')->sum('quantity');

                // Order details summary (items, quantity, SKU)
                $orderDetail = $this->record->lines
                    ->filter(fn ($line) => $line->type !== 'shipping')
                    ->map(function ($line) {
                        $sku = $line->purchasable?->sku ?? 'N/A';
                        return "{$line->quantity}x {$sku}";
                    })->implode(', ');

                return [
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('cityName')
                                ->label('Customer City (PostEx Match)')
                                ->options($cities)
                                ->searchable()
                                ->required()
                                ->default($defaultCity)
                                ->helperText('Please select the exact matching operational city in Pakistan.'),

                            Forms\Components\Select::make('pickupAddressCode')
                                ->label('Pickup Address (Warehouse)')
                                ->options($pickups)
                                ->required()
                                ->default(config('postex.default_pickup_address_code') ?? (array_key_first($pickups) ?: null)),

                            Forms\Components\Select::make('orderType')
                                ->label('Order Type')
                                ->options([
                                    'Normal' => 'Normal',
                                    'Reversed' => 'Reversed',
                                    'Replacement' => 'Replacement',
                                ])
                                ->required()
                                ->default(config('postex.default_order_type') ?? 'Normal'),

                            Forms\Components\TextInput::make('invoicePayment')
                                ->label('COD Amount to Collect (PKR)')
                                ->numeric()
                                ->required()
                                ->default((int) round($this->record->total->decimal)),

                            Forms\Components\TextInput::make('items')
                                ->label('Number of Pieces')
                                ->numeric()
                                ->required()
                                ->default($itemsCount),

                            Forms\Components\TextInput::make('orderRefNumber')
                                ->label('Order Reference Number')
                                ->required()
                                ->default($this->record->reference ?? $this->record->id)
                                ->disabled(),

                            Forms\Components\TextInput::make('customerName')
                                ->label('Customer Name')
                                ->required()
                                ->default($this->record->shippingAddress?->fullName ?? ''),

                            Forms\Components\TextInput::make('customerPhone')
                                ->label('Customer Phone')
                                ->required()
                                ->default(function () {
                                    $phone = $this->record->shippingAddress?->contact_phone ?? '';
                                    $cleanPhone = preg_replace('/\D/', '', $phone);
                                    if (str_starts_with($cleanPhone, '92')) {
                                        $cleanPhone = '0' . substr($cleanPhone, 2);
                                    }
                                    return $cleanPhone;
                                })
                                ->helperText('Format: 03xxxxxxxxx (11 digits)'),

                            Forms\Components\Textarea::make('deliveryAddress')
                                ->label('Delivery Address')
                                ->required()
                                ->columnSpan(2)
                                ->default($this->record->shippingAddress?->line_one . ($this->record->shippingAddress?->line_two ? ', ' . $this->record->shippingAddress->line_two : '')),

                            Forms\Components\Textarea::make('orderDetail')
                                ->label('Order Details (SKU/Items Info)')
                                ->columnSpan(2)
                                ->default($orderDetail),

                            Forms\Components\Textarea::make('transactionNotes')
                                ->label('Transaction / Delivery Notes')
                                ->columnSpan(2)
                                ->default($this->record->notes),
                        ]),
                ];
            })
            ->action(function (array $data) {
                $postExService = app(PostExService::class);

                // Add orderRefNumber explicitly since it was disabled/grayed out in form
                $data['orderRefNumber'] = $this->record->reference ?? $this->record->id;

                $response = $postExService->createOrder($data);

                if (($response['statusCode'] ?? null) == '200' && isset($response['dist'])) {
                    $dist = $response['dist'];
                    $trackingNumber = $dist['trackingNumber'] ?? null;
                    $orderStatus = $dist['orderStatus'] ?? 'UnBooked';
                    $orderDate = $dist['orderDate'] ?? now()->toDateTimeString();

                    $meta = $this->record->meta ?? [];
                    $meta['postex_tracking_number'] = $trackingNumber;
                    $meta['postex_status'] = $orderStatus;
                    $meta['postex_booked_at'] = $orderDate;
                    $meta['postex_city'] = $data['cityName'];
                    $meta['postex_order_type'] = $data['orderType'];

                    $this->record->update([
                        'meta' => $meta,
                        'status' => 'shipped',
                    ]);

                    Notification::make()
                        ->title('Order Booked on PostEx Successfully')
                        ->body("Tracking Number: {$trackingNumber}")
                        ->success()
                        ->send();

                    $this->dispatchActivityUpdated();
                } else {
                    $errorMsg = $response['statusMessage'] ?? 'Unknown Error';
                    Notification::make()
                        ->title('Failed to Book Order on PostEx')
                        ->body($errorMsg)
                        ->danger()
                        ->persistent()
                        ->send();
                }
            });
    }

    /**
     * Download PostEx airway bill.
     */
    protected function getDownloadPostExAirwayBillAction(): Action
    {
        return Action::make('download_postex_airway_bill')
            ->label('PostEx Airway Bill')
            ->icon('heroicon-o-document-arrow-down')
            ->color('info')
            ->visible(function () {
                $meta = $this->record->meta ?? [];
                return isset($meta['postex_tracking_number']);
            })
            ->action(function () {
                $meta = $this->record->meta ?? [];
                $trackingNumber = $meta['postex_tracking_number'] ?? null;

                if (!$trackingNumber) {
                    Notification::make()->title('No tracking number found')->danger()->send();
                    return;
                }

                $postExService = app(PostExService::class);
                $pdfContent = $postExService->getAirwayBill($trackingNumber);

                if ($pdfContent) {
                    return response()->streamDownload(
                        fn () => print($pdfContent),
                        "postex-airwaybill-{$trackingNumber}.pdf",
                        ['Content-Type' => 'application/pdf']
                    );
                }

                Notification::make()->title('Failed to download airway bill from PostEx')->danger()->send();
            });
    }

    /**
     * Cancel booking on PostEx.
     */
    protected function getCancelPostExBookingAction(): Action
    {
        return Action::make('cancel_postex_booking')
            ->label('Cancel PostEx Booking')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(function () {
                $meta = $this->record->meta ?? [];
                return isset($meta['postex_tracking_number']);
            })
            ->requiresConfirmation()
            ->action(function () {
                $meta = $this->record->meta ?? [];
                $trackingNumber = $meta['postex_tracking_number'] ?? null;

                if (!$trackingNumber) {
                    Notification::make()->title('No tracking number found')->danger()->send();
                    return;
                }

                $postExService = app(PostExService::class);
                $response = $postExService->cancelOrder($trackingNumber);

                if (($response['statusCode'] ?? null) == '200') {
                    unset($meta['postex_tracking_number']);
                    $meta['postex_status'] = 'Cancelled';

                    $this->record->update([
                        'meta' => $meta,
                        'status' => 'cancelled',
                    ]);

                    Notification::make()
                        ->title('PostEx Booking Cancelled')
                        ->success()
                        ->send();

                    $this->dispatchActivityUpdated();
                } else {
                    $errorMsg = $response['statusMessage'] ?? 'Unknown Error';
                    Notification::make()
                        ->title('Failed to Cancel PostEx Booking')
                        ->body($errorMsg)
                        ->danger()
                        ->send();
                }
            });
    }

    /**
     * Sync shipment status from PostEx.
     */
    protected function getSyncPostExStatusAction(): Action
    {
        return Action::make('sync_postex_status')
            ->label('Sync PostEx Status')
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->visible(function () {
                $meta = $this->record->meta ?? [];
                return isset($meta['postex_tracking_number']);
            })
            ->action(function () {
                $meta = $this->record->meta ?? [];
                $trackingNumber = $meta['postex_tracking_number'] ?? null;

                if (!$trackingNumber) {
                    return;
                }

                $postExService = app(PostExService::class);
                $response = $postExService->trackOrder($trackingNumber);

                if (($response['statusCode'] ?? null) == '200' && isset($response['dist'])) {
                    $dist = $response['dist'];
                    $status = $dist['transactionStatus'] ?? null;

                    if ($status) {
                        $meta['postex_status'] = $status;

                        $this->record->update(['meta' => $meta]);

                        Notification::make()
                            ->title('PostEx Status Synced')
                            ->body("Current Status: {$status}")
                            ->success()
                            ->send();

                        $this->dispatchActivityUpdated();
                    }
                } else {
                    Notification::make()
                        ->title('Failed to Sync Status from PostEx')
                        ->danger()
                        ->send();
                }
            });
    }
}
