<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductFaqResource\Pages;
use App\Models\ProductFaq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Models\Product;

class ProductFaqResource extends Resource
{
    protected static ?string $model = ProductFaq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Shop Management';

    protected static ?string $navigationLabel = 'Product FAQs';

    protected static ?string $modelLabel = 'Product FAQ';

    protected static ?string $pluralModelLabel = 'Product FAQs';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('FAQ Information')
                    ->description('Create or edit product-specific questions and answers for the collapsible FAQ accordion.')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Product $record) => $record->attr('name') ?? "Product #{$record->id}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('question')
                            ->label('Question')
                            ->placeholder('e.g. Can I use this serum every day?')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('answer')
                            ->label('Answer')
                            ->placeholder('Write the detailed answer with routine guidance, precautions, etc...')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('position')
                                    ->label('Display Order / Position')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first in the FAQ list.'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active (Visible on Storefront)')
                                    ->default(true)
                                    ->inline(false)
                                    ->helperText('Toggle off to temporarily hide this FAQ without deleting it.'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product')
                    ->label('Product')
                    ->formatStateUsing(fn ($record) => $record->product?->attr('name') ?? "Product #{$record->product_id}")
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('question')
                    ->label('Question')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(60),

                Tables\Columns\TextColumn::make('answer')
                    ->label('Answer')
                    ->html()
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\TextColumn::make('position')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Filter by Product')
                    ->relationship('product', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Product $record) => $record->attr('name') ?? "Product #{$record->id}")
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('product_id', 'asc');
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
            'index'  => Pages\ListProductFaqs::route('/'),
            'create' => Pages\CreateProductFaq::route('/create'),
            'edit'   => Pages\EditProductFaq::route('/{record}/edit'),
        ];
    }
}
