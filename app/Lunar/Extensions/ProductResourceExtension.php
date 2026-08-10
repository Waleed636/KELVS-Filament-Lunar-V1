<?php

namespace App\Lunar\Extensions;

use App\Lunar\Pages\ManageProductFaqs;
use Filament\Forms;
use Filament\Forms\Form;
use Lunar\Admin\Support\Extending\ResourceExtension;

class ProductResourceExtension extends ResourceExtension
{
    /**
     * Extend the main Edit Product form to add Q&A / FAQs repeater directly on the edit page.
     */
    public function extendForm(Form $form): Form
    {
        return $form->schema([
            ...$form->getComponents(),
            Forms\Components\Section::make('Frequently Asked Questions (Q&A)')
                ->description('Manage product-specific Q&As displayed in the storefront collapsible FAQ accordion.')
                ->schema([
                    Forms\Components\Repeater::make('faqs')
                        ->relationship('faqs')
                        ->orderColumn('position')
                        ->schema([
                            Forms\Components\TextInput::make('question')
                                ->label('Question')
                                ->placeholder('e.g. Can I use this serum every day?')
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->inline(false)
                                ->columnSpan(1),

                            Forms\Components\RichEditor::make('answer')
                                ->label('Answer')
                                ->placeholder('Write the detailed answer with routine guidance, precautions, etc...')
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'New Question')
                        ->collapsible()
                        ->collapsed(false)
                        ->reorderableWithButtons()
                        ->addActionLabel('+ Add Question & Answer')
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
    }

    /**
     * Extend the right-hand Sub-Navigation menu on the Edit Product page.
     */
    public function extendSubNavigation(array $subnav): array
    {
        array_splice($subnav, 1, 0, [ManageProductFaqs::class]);

        return $subnav;
    }

    /**
     * Extend the Resource Pages to register the sub-navigation route.
     */
    public function extendPages(array $pages): array
    {
        $pages['faqs'] = ManageProductFaqs::route('/{record}/faqs');

        return $pages;
    }
}
