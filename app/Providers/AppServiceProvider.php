<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        try {
            $reflector = new \ReflectionClass(\Lunar\Admin\LunarPanelManager::class);
            $property = $reflector->getProperty('resources');
            $property->setAccessible(true);
            $resources = $property->getValue();
            foreach ($resources as $key => $resource) {
                if ($resource === \Lunar\Admin\Filament\Resources\OrderResource::class) {
                    $resources[$key] = \App\Filament\Resources\OrderResource::class;
                }
            }
            $property->setValue(null, $resources);
        } catch (\Exception $e) {
            // Fallback
        }

        \Lunar\Admin\Support\Facades\LunarPanel::panel(function (\Filament\Panel $panel) {
            return $panel->plugin(new \Lunar\Shipping\ShippingPlugin());
        })->register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Lunar\Facades\Telemetry::optOut();

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => '
                <style>
                    /* Custom order status row backgrounds (targeting the tds to override opaque cell bg) */
                    .order-status-shipped td {
                        background-color: rgba(59, 130, 246, 0.08) !important;
                    }
                    .order-status-shipped {
                        border-left: 4px solid rgba(59, 130, 246, 1) !important;
                    }
                    .order-status-returned td {
                        background-color: rgba(249, 115, 22, 0.08) !important;
                    }
                    .order-status-returned {
                        border-left: 4px solid rgba(249, 115, 22, 1) !important;
                    }
                    .order-status-cancelled td {
                        background-color: rgba(239, 68, 68, 0.08) !important;
                    }
                    .order-status-cancelled {
                        border-left: 4px solid rgba(239, 68, 68, 1) !important;
                    }

                    /* Override Filament SelectColumn size to make it compact and premium */
                    .fi-ta-select {
                        min-width: 120px !important;
                        width: auto !important;
                        max-width: 140px !important;
                        padding-top: 0.25rem !important;
                        padding-bottom: 0.25rem !important;
                    }
                    .fi-ta-select select {
                        padding-top: 0.15rem !important;
                        padding-bottom: 0.15rem !important;
                        padding-left: 0.375rem !important;
                        padding-right: 1.5rem !important;
                        font-size: 0.75rem !important;
                        line-height: 1rem !important;
                        height: auto !important;
                        border-radius: 0.375rem !important;
                    }
                </style>
            '
        );

        // Safeguard: Clean up CartLines when a ProductVariant is deleted to prevent 500 crashes
        \Lunar\Models\ProductVariant::deleting(function (\Lunar\Models\ProductVariant $variant) {
            \Lunar\Models\CartLine::where('purchasable_type', $variant->getMorphClass())
                ->where('purchasable_id', $variant->id)
                ->delete();
        });

        // Safeguard: Clean up CartLines when a Product is deleted
        \Lunar\Models\Product::deleting(function (\Lunar\Models\Product $product) {
            $variantIds = $product->variants()->pluck('id');
            if ($variantIds->isNotEmpty()) {
                \Lunar\Models\CartLine::where('purchasable_type', 'product_variant')
                    ->whereIn('purchasable_id', $variantIds)
                    ->delete();
            }
        });

        // Safeguard: Automatically clean up orphaned CartLines when a Cart is loaded to prevent TypeError in calculations
        \Lunar\Models\Cart::retrieved(function (\Lunar\Models\Cart $cart) {
            \Illuminate\Support\Facades\DB::table('lunar_cart_lines')
                ->where('cart_id', $cart->id)
                ->where('purchasable_type', 'product_variant')
                ->whereNotExists(function ($query) {
                    $query->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('lunar_product_variants')
                        ->whereColumn('lunar_product_variants.id', 'lunar_cart_lines.purchasable_id')
                        ->whereNull('lunar_product_variants.deleted_at');
                })
                ->delete();
        });
    }
}
