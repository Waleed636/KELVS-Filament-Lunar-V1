<?php

namespace App\Support\Sky;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use LaraZeus\Sky\Classes\ContentEditor;
use Lunar\Models\Product;

class CustomRichEditor implements ContentEditor
{
    public static function component(): Component
    {
        return RichEditor::make('content')
            ->required()
            ->hintAction(
                Action::make('insertProductCard')
                    ->label('+ Insert Product Card')
                    ->icon('heroicon-m-shopping-bag')
                    ->color('primary')
                    ->modalHeading('Embed Product Card in Article')
                    ->modalDescription('Choose a product from your store catalog to insert an instant cash-on-delivery order card.')
                    ->modalSubmitActionLabel('Insert into Article')
                    ->form([
                        Select::make('product_slug')
                            ->label('Select KELVS Product')
                            ->placeholder('Search products...')
                            ->options(function () {
                                try {
                                    return Product::with('urls')->get()->mapWithKeys(function ($p) {
                                        $slug = $p->urls->first()?->slug;
                                        $name = $p->attr('name') ?? $slug;
                                        return $slug ? [$slug => $name] : [];
                                    })->filter()->all();
                                } catch (\Throwable $e) {
                                    return [];
                                }
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, $set, $get) {
                        $slug = $data['product_slug'] ?? null;
                        if ($slug) {
                            $current = (string) ($get('content') ?? '');
                            $set('content', $current . "<p>[product:{$slug}]</p>");
                        }
                    })
            );
    }

    public static function render(string $content): string
    {
        return str(html_entity_decode($content))
            ->replace(['prompt(', 'eval(', '&lt;script', '<script'], '');
    }
}
