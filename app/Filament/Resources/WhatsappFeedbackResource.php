<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsappFeedbackResource\Pages;
use App\Models\WhatsappFeedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Models\Product;

class WhatsappFeedbackResource extends Resource
{
    protected static ?string $model = WhatsappFeedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Shop Management';

    protected static ?string $navigationLabel = 'WhatsApp Feedbacks';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Photo Showcase & Vertical Reviews')
                    ->description('Upload customer selfies, unboxing photos, Instagram stories, texture closeups, or WhatsApp chat screenshots to display in the vertical carousel on product pages.')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Feedback Image / Screenshot')
                            ->directory('whatsapp-feedbacks')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->openable()
                            ->downloadable()
                            ->deletable()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('product_id')
                            ->label('Attach to Product (Optional)')
                            ->relationship('product', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Product $record) => $record->attr('name') ?? "Product #{$record->id}")
                            ->searchable()
                            ->preload()
                            ->placeholder('Global — Display on All Products')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('customer_name')
                            ->label('Customer Name & Location')
                            ->placeholder('e.g. Fatima K. (Lahore)')
                            ->maxLength(255),

                        Forms\Components\Select::make('rating')
                            ->label('Star Rating')
                            ->options([
                                1 => '1 Star',
                                2 => '2 Stars',
                                3 => '3 Stars',
                                4 => '4 Stars',
                                5 => '5 Stars',
                            ])
                            ->default(5)
                            ->required(),

                        Forms\Components\Textarea::make('caption')
                            ->label('Customer Quote / Snippet (Optional)')
                            ->placeholder('e.g. "Received my parcel today! Packaging is super premium and texture feels amazing."')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first in the carousel.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active on Storefront')
                            ->default(true),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Preview')
                    ->disk('public')
                    ->square()
                    ->size(50),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->placeholder('Anonymous')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product')
                    ->label('Product Target')
                    ->formatStateUsing(fn ($record) => $record->product?->attr('name') ?? 'Global (All Products)'),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->color('warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
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
            'index' => Pages\ManageWhatsappFeedbacks::route('/'),
        ];
    }
}
