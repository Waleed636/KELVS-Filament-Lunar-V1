<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartialOrderResource\Pages;
use App\Models\PartialOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartialOrderResource extends Resource
{
    protected static ?string $model = PartialOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Shop Management';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->disabled()
                            ->placeholder('N/A'),
                        Forms\Components\TextInput::make('phone')
                            ->disabled()
                            ->placeholder('N/A'),
                        Forms\Components\TextInput::make('email')
                            ->disabled()
                            ->placeholder('N/A'),
                        Forms\Components\TextInput::make('session_id')
                            ->disabled()
                            ->placeholder('N/A'),
                    ])->columns(2),

                Forms\Components\Section::make('Shipping / Address Information')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->disabled()
                            ->placeholder('N/A'),
                        Forms\Components\TextInput::make('city')
                            ->disabled()
                            ->placeholder('N/A'),
                        Forms\Components\TextInput::make('province')
                            ->disabled()
                            ->placeholder('N/A'),
                        Forms\Components\TextInput::make('postalcode')
                            ->disabled()
                            ->placeholder('N/A'),
                    ])->columns(2),

                Forms\Components\Section::make('Cart Information')
                    ->schema([
                        Forms\Components\Placeholder::make('cart_contents')
                            ->label('Cart Items')
                            ->content(function ($record) {
                                if (!$record || !$record->cart_contents || !is_array($record->cart_contents)) {
                                    return 'No items in cart.';
                                }
                                
                                $html = '<table class="w-full text-left border-collapse" style="width: 100%; border-spacing: 0;">';
                                $html .= '<thead>';
                                $html .= '<tr style="border-bottom: 2px solid #e5e7eb;"><th style="padding-bottom: 8px; font-weight: bold; text-align: left;">Product Name</th><th style="padding-bottom: 8px; font-weight: bold; text-align: center;">Qty</th><th style="padding-bottom: 8px; font-weight: bold; text-align: right;">Price</th></tr>';
                                $html .= '</thead>';
                                $html .= '<tbody>';
                                foreach ($record->cart_contents as $item) {
                                    $name = $item['name'] ?? 'Product';
                                    $qty = $item['quantity'] ?? 1;
                                    $price = isset($item['price']) ? 'PKR ' . number_format($item['price'], 2) : 'N/A';
                                    $html .= '<tr style="border-bottom: 1px solid #f3f4f6;">';
                                    $html .= '<td style="padding: 8px 0; text-align: left;">' . e($name) . '</td>';
                                    $html .= '<td style="padding: 8px 0; text-align: center;">' . e($qty) . '</td>';
                                    $html .= '<td style="padding: 8px 0; text-align: right;">' . e($price) . '</td>';
                                    $html .= '</tr>';
                                }
                                $html .= '</tbody>';
                                $html .= '</table>';
                                
                                return new \Illuminate\Support\HtmlString($html);
                            }),
                        
                        Forms\Components\TextInput::make('cart_total')
                            ->label('Cart Grand Total')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => 'PKR ' . number_format($state, 2)),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('province')
                    ->label('Province')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('cart_total')
                    ->label('Cart Total')
                    ->formatStateUsing(fn ($state) => 'PKR ' . number_format($state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->timezone('Asia/Karachi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->timezone('Asia/Karachi')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartialOrders::route('/'),
        ];
    }
}
