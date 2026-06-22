<?php

namespace App\Filament\Resources;

use Lunar\Admin\Filament\Resources\OrderResource as BaseOrderResource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\CustomerStatus;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Services\PostExService;

class OrderResource extends BaseOrderResource
{
    /**
     * Override default table configuration to add row styling and PostEx action.
     */
    public static function getDefaultTable(Table $table): Table
    {
        return parent::getDefaultTable($table)
            // Conditional CSS classes for backgrounds and borders
            ->recordClasses(fn (Model $record) => match ($record->status) {
                'shipped' => 'order-status-shipped',
                'returned' => 'order-status-returned',
                'cancelled' => 'order-status-cancelled',
                default => null,
            })
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => \App\Filament\Resources\OrderResource\Pages\ManageOrder::getUrl(['record' => $record])),

                Tables\Actions\Action::make('book_postex_row')
                    ->label('Book PostEx')
                    ->icon('heroicon-m-truck')
                    ->color('success')
                    ->visible(fn (Model $record) => !isset($record->meta['postex_tracking_number']))
                    ->modalHeading('Book Order on PostEx')
                    ->modalSubmitActionLabel('Confirm Booking')
                    ->form(function (Model $record) {
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
                        $shippingCity = $record->shippingAddress?->city ?? '';
                        $defaultCity = collect($rawCities)->first(fn($c) => strtolower(trim($c['operationalCityName'])) === strtolower(trim($shippingCity)))['operationalCityName'] ?? null;

                        // Items count
                        $itemsCount = $record->lines->filter(fn ($line) => $line->type !== 'shipping')->sum('quantity');

                        // Order details summary (items, quantity, SKU)
                        $orderDetail = $record->lines
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
                                        ->default((int) round($record->total->decimal)),

                                    Forms\Components\TextInput::make('items')
                                        ->label('Number of Pieces')
                                        ->numeric()
                                        ->required()
                                        ->default($itemsCount),

                                    Forms\Components\TextInput::make('customerName')
                                        ->label('Customer Name')
                                        ->required()
                                        ->default($record->shippingAddress?->fullName ?? ''),

                                    Forms\Components\TextInput::make('customerPhone')
                                        ->label('Customer Phone')
                                        ->required()
                                        ->default(function () use ($record) {
                                            $phone = $record->shippingAddress?->contact_phone ?? '';
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
                                        ->default($record->shippingAddress?->line_one . ($record->shippingAddress?->line_two ? ', ' . $record->shippingAddress->line_two : '')),

                                    Forms\Components\Textarea::make('orderDetail')
                                        ->label('Order Details (SKU/Items Info)')
                                        ->columnSpan(2)
                                        ->default($orderDetail),

                                    Forms\Components\Textarea::make('transactionNotes')
                                        ->label('Transaction / Delivery Notes')
                                        ->columnSpan(2)
                                        ->default($record->notes),
                                ]),
                        ];
                    })
                    ->action(function (array $data, Model $record) {
                        $postExService = app(PostExService::class);

                        // Add orderRefNumber explicitly
                        $data['orderRefNumber'] = $record->reference ?? $record->id;

                        $response = $postExService->createOrder($data);

                        if (($response['statusCode'] ?? null) == '200' && isset($response['dist'])) {
                            $dist = $response['dist'];
                            $trackingNumber = $dist['trackingNumber'] ?? null;
                            $orderStatus = $dist['orderStatus'] ?? 'UnBooked';
                            $orderDate = $dist['orderDate'] ?? now()->toDateTimeString();

                            $meta = $record->meta ?? [];
                            $meta['postex_tracking_number'] = $trackingNumber;
                            $meta['postex_status'] = $orderStatus;
                            $meta['postex_booked_at'] = $orderDate;
                            $meta['postex_city'] = $data['cityName'];
                            $meta['postex_order_type'] = $data['orderType'];

                            $record->update([
                                'meta' => $meta,
                                'status' => 'shipped',
                            ]);

                            Notification::make()
                                ->title('Order Booked on PostEx Successfully')
                                ->body("Tracking Number: {$trackingNumber}")
                                ->success()
                                ->send();
                        } else {
                            $errorMsg = $response['statusMessage'] ?? 'Unknown Error';
                            Notification::make()
                                ->title('Failed to Book Order on PostEx')
                                ->body($errorMsg)
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    })
            ]);
    }

    /**
     * Override columns to replace the static status TextColumn with an interactive SelectColumn and add PostEx info.
     */
    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\SelectColumn::make('status')
                ->label(__('lunarpanel::order.table.status.label'))
                ->options(collect(config('lunar.orders.statuses', []))
                    ->mapWithKeys(fn ($config, $status) => [$status => $config['label']]))
                ->selectablePlaceholder(false)
                ->toggleable(),
            Tables\Columns\TextColumn::make('reference')
                ->label(__('lunarpanel::order.table.reference.label'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('customer_reference')
                ->label(__('lunarpanel::order.table.customer_reference.label'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('billingAddress.fullName')
                ->label(__('lunarpanel::order.table.customer.label'))
                ->toggleable()
                ->searchable(['first_name', 'last_name']),
            Tables\Columns\TextColumn::make('new_customer')
                ->label(__('lunarpanel::order.table.new_customer.label'))
                ->toggleable()
                ->formatStateUsing(fn (bool $state) => CustomerStatus::getLabel($state))
                ->color(fn (bool $state) => CustomerStatus::getColor($state))
                ->icon(fn (bool $state) => CustomerStatus::getIcon($state))
                ->badge(),
            Tables\Columns\TextColumn::make('tags.value')
                ->label(__('lunarpanel::order.table.tags.label'))
                ->badge()
                ->toggleable()
                ->separator(','),
            Tables\Columns\TextColumn::make('billingAddress.postcode')
                ->label(__('lunarpanel::order.table.postcode.label'))
                ->toggleable()
                ->searchable(),
            Tables\Columns\TextColumn::make('billingAddress.contact_email')
                ->label(__('lunarpanel::order.table.email.label'))
                ->toggleable()
                ->copyable()
                ->copyMessage(__('lunarpanel::order.table.email.copy_message'))
                ->copyMessageDuration(1500)
                ->searchable(),
            Tables\Columns\TextColumn::make('billingAddress.contact_phone')
                ->label(__('lunarpanel::order.table.phone.label'))
                ->toggleable(),
            
            // PostEx Info columns
            Tables\Columns\TextColumn::make('meta.postex_tracking_number')
                ->label('PostEx Tracking')
                ->copyable()
                ->badge()
                ->color('info')
                ->toggleable()
                ->searchable(),
            Tables\Columns\TextColumn::make('meta.postex_status')
                ->label('PostEx Status')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'UnBooked' => 'gray',
                    'Booked' => 'success',
                    'Cancelled' => 'danger',
                    default => 'primary',
                })
                ->toggleable(),

            Tables\Columns\TextColumn::make('total')
                ->label(__('lunarpanel::order.table.total.label'))
                ->toggleable()
                ->formatStateUsing(fn ($state): string => $state->formatted),
            Tables\Columns\TextColumn::make('placed_at')
                ->label(__('lunarpanel::order.table.date.label'))
                ->toggleable()
                ->dateTime()
                ->timezone('Asia/Karachi'),
        ];
    }

    public static function getDefaultPages(): array
    {
        return [
            'index' => \App\Filament\Resources\OrderResource\Pages\ListOrders::route('/'),
            'order' => \App\Filament\Resources\OrderResource\Pages\ManageOrder::route('/{record}'),
            'edit' => \App\Filament\Resources\OrderResource\Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
