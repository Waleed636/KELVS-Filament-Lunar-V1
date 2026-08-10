<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Lunar\Admin\Filament\Resources\ProductResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Pages\BaseManageRelatedRecords;

class ManageProductFaqs extends BaseManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'faqs';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-question-mark-circle';
    }

    public function getTitle(): string
    {
        return 'Q&A / Product FAQs';
    }

    public static function getNavigationLabel(): string
    {
        return 'Q&A / FAQs';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->label('Question')
                    ->placeholder('e.g. Can I use this serum every day?')
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('answer')
                    ->label('Answer')
                    ->placeholder('Write the detailed answer with routine advice, precautions, etc...')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('position')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first in the FAQ list.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active on Storefront')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Toggle off to hide this FAQ from the storefront.'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->reorderable('position')
            ->defaultSort('position', 'asc')
            ->columns([
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
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Q&A / FAQ')
                    ->modalHeading('Add New Question & Answer'),
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
}
