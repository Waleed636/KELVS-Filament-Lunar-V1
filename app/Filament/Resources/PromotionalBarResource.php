<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionalBarResource\Pages;
use App\Models\PromotionalBar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromotionalBarResource extends Resource
{
    protected static ?string $model = PromotionalBar::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Shop Management';

    protected static ?string $navigationLabel = 'Promotional Bars';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Announcement Content')
                    ->description('Specify the main text, badge, and promotional call-to-action.')
                    ->schema([
                        Forms\Components\TextInput::make('content')
                            ->label('Main Announcement Text')
                            ->placeholder('e.g., 🔥 Summer Sale: Enjoy up to 25% Off Storewide!')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('badge_text')
                            ->label('Badge Tag (Optional)')
                            ->placeholder('e.g., LIMITED TIME, HOT, FREE SHIPPING')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('promo_code')
                            ->label('Promo / Voucher Code (Optional)')
                            ->placeholder('e.g., FREESHIP or KELVS10')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('button_text')
                            ->label('Button Text (Optional)')
                            ->placeholder('e.g., Shop Now')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('button_url')
                            ->label('Button Target URL (Optional)')
                            ->placeholder('e.g., /shop or https://...')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Styling & Display Controls')
                    ->schema([
                        Forms\Components\ColorPicker::make('bg_color')
                            ->label('Background Color')
                            ->default('#111111')
                            ->required(),

                        Forms\Components\ColorPicker::make('text_color')
                            ->label('Text Color')
                            ->default('#ffffff')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active on Storefront')
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower values appear first in the announcement rotation.'),

                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Publish Date / Start Time (Optional)'),

                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Expiry Date / End Time (Optional)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content')
                    ->label('Announcement')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('badge_text')
                    ->label('Badge')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('promo_code')
                    ->label('Code')
                    ->placeholder('-'),

                Tables\Columns\ColorColumn::make('bg_color')
                    ->label('Background'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotionalBars::route('/'),
            'create' => Pages\CreatePromotionalBar::route('/create'),
            'edit' => Pages\EditPromotionalBar::route('/{record}/edit'),
        ];
    }
}
