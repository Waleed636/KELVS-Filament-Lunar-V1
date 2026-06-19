<?php

namespace App\Filament\Resources;

use Lunar\Admin\Filament\Resources\OrderResource as BaseOrderResource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\CustomerStatus;

class OrderResource extends BaseOrderResource
{
    /**
     * Override default table configuration to add row styling.
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
            });
    }

    /**
     * Override columns to replace the static status TextColumn with an interactive SelectColumn.
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
