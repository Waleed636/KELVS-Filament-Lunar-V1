<div class="relative bg-white text-[#111111]">

    <!-- Floating Success Toast for Add-to-Cart Action -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3500)" x-show="show" x-transition:enter="transform ease-out duration-300 transition" x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed bottom-6 right-6 z-50 bg-[#111111] text-white px-5 py-4 rounded-md shadow-xl text-sm flex items-center space-x-3 border border-gray-800">
            <svg class="w-5 h-5 text-[#e8dcd2]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-medium tracking-wide">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Shop Hero Banner -->
    <section class="bg-[#fbfbfa] border-b border-gray-100 py-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <span class="inline-block text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400 mb-4">Complete Collection</span>
            <h1 class="text-4xl sm:text-5xl font-bold text-[#111111] mb-4">Science-Backed Skincare</h1>
            <p class="text-gray-500 max-w-xl mx-auto leading-relaxed text-sm">Targeted clinical solutions designed for optimal dermal performance. Clean, simplified formulas, formulated for heat and humidity.</p>
        </div>
    </section>

    <!-- Product Grid Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Grid Header -->
            <div class="flex flex-col sm:flex-row items-center justify-between border-b border-gray-100 pb-6 mb-12 gap-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-extrabold">
                        Showing {{ $products->count() }} Formulations
                    </p>
                </div>
                <div class="flex items-center space-x-2 text-xs text-gray-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-semibold uppercase tracking-wider text-gray-500">All Batches Freshly Seeded</span>
                </div>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12">
                    @foreach($products as $product)
                        @php
                            $variant = $product->variants->first();
                            $priceRecord = $variant?->prices->first();
                            $price = $priceRecord?->price;
                            $comparePrice = $priceRecord?->compare_price;
                            $hasDiscount = $comparePrice && $comparePrice->value > $price->value;
                            $formattedPrice = $price ? $price->formatted : 'N/A';
                            $sku = $variant?->sku;
                            
                            // Fetch media item from Spatie Media Library
                            $media = $product->getMedia('images')->first(fn ($media) => $media->getCustomProperty('primary') === true) 
                                ?? $product->getMedia('images')->first();
                            
                            $productImage = $media 
                                ? parse_url($media->getUrl(), PHP_URL_PATH) 
                                : match($sku) {
                                    'KELVS-CLEAN-01' => '/images/cleanser.png',
                                    'KELVS-NIAC-01' => '/images/niacinamide.png',
                                    'KELVS-BHA-01' => '/images/bha.png',
                                    'KELVS-HYA-01' => '/images/hyaluronic.png',
                                    'KELVS-CER-01' => '/images/ceramide.png',
                                    'KELVS-SPF-01' => '/images/sunshield.png',
                                    default => '/images/hero_lifestyle.png'
                                };

                            // Map SKU to benefit line
                            $benefit = match($sku) {
                                'KELVS-CLEAN-01' => 'Purifies & Hydrates Skin',
                                'KELVS-NIAC-01' => 'Controls Oil & Tightens Pores',
                                'KELVS-BHA-01' => 'Clears Pores & Smooths Texture',
                                'KELVS-HYA-01' => 'Plumps & Hydrates Dry Skin',
                                'KELVS-CER-01' => 'Repairs Barrier & Locks Moisture',
                                'KELVS-SPF-01' => 'Broad-Spectrum SPF 50+ Protection',
                                default => 'Dermatological Formula'
                            };
                        @endphp
                        
                        <div class="group flex flex-col bg-white border border-gray-150 rounded-xl overflow-hidden hover:border-gray-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition duration-300">
                            
                            <!-- Image Container -->
                            <a href="/products/{{ $product->urls->first()?->slug }}" wire:navigate class="aspect-square w-full bg-[#f6f6f5] flex items-center justify-center relative overflow-hidden border-b border-gray-100 p-8">
                                <img src="{{ $productImage }}" alt="{{ $product->attr('name') }}" class="object-contain w-full h-full group-hover:scale-[1.03] transition duration-500" loading="lazy">
                                
                                @if($variant && $variant->stock < 10)
                                    <span class="absolute top-4 left-4 text-[9px] uppercase tracking-widest font-extrabold text-[#111111] bg-[#e8dcd2] px-2.5 py-1 rounded">
                                        Limited Batch
                                    </span>
                                @endif

                                @if($hasDiscount)
                                    @php
                                        $savings = (($comparePrice->value - $price->value) / $comparePrice->value) * 100;
                                        $discountPercent = round($savings);
                                    @endphp
                                    <span class="absolute top-4 right-4 text-[9px] font-extrabold text-red-700 bg-red-50 border border-red-100 rounded px-2.5 py-1 uppercase tracking-widest">
                                        Save {{ $discountPercent }}%
                                    </span>
                                @endif
                            </a>

                            <!-- Card details -->
                            <div class="p-6 flex-grow flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <span class="text-[10px] uppercase font-extrabold tracking-widest text-gray-400 block">{{ $benefit }}</span>
                                    <h3 class="text-base font-bold text-[#111111] hover:opacity-85 transition line-clamp-1">
                                        <a href="/products/{{ $product->urls->first()?->slug }}" wire:navigate>
                                            {{ $product->attr('name') }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                        {{ strip_tags($product->attr('description')) }}
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-lg font-extrabold text-[#111111]">{{ $formattedPrice }}</span>
                                        @if($hasDiscount)
                                            <span class="line-through text-gray-400 text-xs font-semibold" style="text-decoration: line-through;">
                                                {{ $comparePrice->formatted }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($variant)
                                        <button wire:click="addToCart({{ $variant->id }})" class="px-4 py-2 bg-[#111111] hover:bg-[#222222] text-white text-[11px] uppercase tracking-wider font-extrabold rounded-[4px] transition duration-200">
                                            Add to Cart
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 font-semibold italic">Sold Out</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 bg-[#fbfbfa] rounded-2xl border border-dashed border-gray-200">
                    <p class="text-sm text-gray-500">No products found. Please run database seeders.</p>
                </div>
            @endif

        </div>
    </section>

</div>
