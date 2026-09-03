@props([
    'slug' => null,
    'product' => null,
    'badge' => 'Dermatologist-Formulated Solution',
])

@php
    $resolvedProduct = $product;
    if (!$resolvedProduct && $slug) {
        $slugMap = [
            'kelvs-whitening-serum'       => 'kelvs-whitening-serum-alpha-arbutin-hyaluronic-acid-fades-dark-spots',
            'kelvs-vitamin-c-serum'       => 'kelvs-vitamin-c-serum-sodium-ascorbyl-phosphate-brightens-skin-fights-acne',
            'anti-aging-serum'            => 'kelvs-anti-aging-serum-niacinamide-hyaluronic-acid-minimizes-pores-controls-oil',
            'kelvs-anti-aging-serum'      => 'kelvs-anti-aging-serum-niacinamide-hyaluronic-acid-minimizes-pores-controls-oil',
            'kelvs-hydrating-serum'       => 'kelvs-hydration-serum-vitamin-b5-hyaluronic-acid-deep-hydration-skin-barrier-repair',
            'kelvs-bha-serum'             => 'kelvs-bha-salicylic-acid-2-serum-salicylic-acid-hyaluronic-acid-clear-pores-fight-acne',
            'kelvs-gentle-cleanser'       => 'kelvs-gentle-cleanser-sodium-lauroyl-sarcosinate-coco-glucoside-sulfate-free-daily-cleanse',
            'keratin-hair-masque'         => 'kelvs-keratin-hair-masque-hydrolyzed-keratin-coconut-castor-oil-repair-frizz-control-breakage',
            'kelvs-anti-dandruff-shampoo' => 'kelvs-anti-dandruff-shampoo-zinc-pyrithione-cetrimonium-chloride-eliminate-flakes-soothe-scalp',
        ];
        $targetSlug = $slugMap[$slug] ?? $slug;

        try {
            $resolvedProduct = \Lunar\Models\Product::with(['variants.prices.currency', 'thumbnail', 'urls'])
                ->whereHas('urls', function ($q) use ($targetSlug, $slug) {
                    $q->where('slug', $targetSlug)->orWhere('slug', 'like', "%{$slug}%");
                })->first();
        } catch (\Throwable $e) {
            $resolvedProduct = null;
        }
    }

    if ($resolvedProduct) {
        $pName = $resolvedProduct->attr('name') ?? 'KELVS Skincare';
        $pSlug = $resolvedProduct->urls->first()?->slug ?? '';
        $pUrl  = url('/products/' . $pSlug);
        
        $variant = $resolvedProduct->variants->first();
        $priceRecord = $variant?->prices->first();
        $factor = 10 ** ($priceRecord->currency?->decimal_places ?? \Lunar\Models\Currency::getDefault()?->decimal_places ?? 2);
        $priceVal = $priceRecord ? ((float) $priceRecord->price->value / $factor) : 0;
        
        $media = $resolvedProduct->getMedia('images')->first();
        $pImage = $media ? $media->getUrl() : ($resolvedProduct->thumbnail?->getUrl() ?? asset('images/hero_lifestyle.png'));
    }
@endphp

@if(isset($resolvedProduct) && $resolvedProduct)
<div class="not-prose my-8 bg-[#FAF7F4] border border-[#E8DCD2] rounded-2xl p-5 sm:p-7 shadow-sm transition-all hover:shadow-md">
    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 sm:gap-7">
        
        <!-- Product Image -->
        <a href="{{ $pUrl }}" class="shrink-0 group relative overflow-hidden rounded-xl bg-white p-2.5 border border-[#EFE8E1] shadow-inner">
            <img src="{{ $pImage }}" alt="{{ $pName }}" class="w-32 h-32 sm:w-36 sm:h-36 object-contain rounded-lg transition-transform duration-300 group-hover:scale-105" />
            <span class="absolute top-2 left-2 bg-[#111111] text-white text-[10px] font-bold px-2 py-0.5 rounded tracking-wider uppercase">
                Official
            </span>
        </a>

        <!-- Product Details -->
        <div class="flex-1 text-center sm:text-left">
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-[#A67C52] bg-[#A67C52]/10 px-2.5 py-0.5 rounded-full">
                    <svg class="w-3 h-3 text-[#A67C52]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    {{ $badge }}
                </span>
                <span class="text-xs text-amber-500 font-semibold flex items-center gap-1">
                    ★★★★★ <span class="text-gray-600 font-normal">(4.9 / 5.0)</span>
                </span>
            </div>

            <h3 class="text-lg sm:text-xl font-bold text-[#111111] leading-snug mb-1">
                <a href="{{ $pUrl }}" class="hover:underline">{{ $pName }}</a>
            </h3>

            <p class="text-xs sm:text-sm text-gray-600 mb-4 line-clamp-2">
                Scientifically formulated for maximum efficacy with zero harsh irritants. Restores skin balance, clears pores, and accelerates visible recovery.
            </p>

            <!-- Price & Order Action -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-[#EFE8E1]">
                <div>
                    @if($priceVal > 0)
                        <div class="text-xs text-gray-500 uppercase tracking-wider font-medium">Special Online Price</div>
                        <div class="text-lg sm:text-xl font-extrabold text-[#111111]">
                            Rs. {{ number_format($priceVal, 0) }}
                            <span class="text-xs font-normal text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded ml-1 border border-emerald-200">
                                Cash on Delivery
                            </span>
                        </div>
                    @endif
                </div>

                <a href="{{ $pUrl }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#111111] hover:bg-black text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-xl transition shadow hover:shadow-lg">
                    <span>Order with Cash on Delivery</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- Trust Badges -->
            <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-4 text-[11px] text-gray-500">
                <span class="inline-flex items-center gap-1">✓ Fast Delivery Across Pakistan</span>
                <span class="inline-flex items-center gap-1">✓ 7-Day Easy Returns</span>
                <span class="inline-flex items-center gap-1">✓ Free Delivery over Rs. 2,500</span>
            </div>
        </div>

    </div>
</div>
@endif
