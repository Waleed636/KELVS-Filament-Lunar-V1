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

    <!-- 1. HERO SECTION (FULL WIDTH SPLIT) -->
    <section class="relative bg-[#fbfbfa] border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center py-16 md:py-24 lg:min-h-[640px]">
                
                <!-- Left Content Column -->
                <div class="lg:col-span-5 flex flex-col justify-center space-y-6 md:space-y-8 text-left z-10">
                    <div class="inline-flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-[#111111]"></span>
                        <span class="text-xs uppercase font-extrabold tracking-widest text-gray-500">Clinical Formulations</span>
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-[#111111] leading-[1.1]">
                        Science-led skincare for real results.
                    </h1>
                    
                    <p class="text-base sm:text-lg text-gray-650 max-w-lg leading-relaxed font-normal">
                        Minimal formulas. Maximum performance. Developed to target core skin issues without the clutter of unnecessary fillers.
                    </p>
                    
                    <div class="flex flex-wrap gap-4 pt-2">
                        <a href="#featured-products" class="px-8 py-3.5 bg-[#111111] hover:bg-[#222222] text-white text-sm font-bold rounded-[6px] tracking-wide uppercase transition duration-300 shadow-sm">
                            Shop Bestsellers
                        </a>
                        <!-- <a href="#routine-builder" class="px-8 py-3.5 border border-[#111111] hover:bg-gray-50 text-[#111111] text-sm font-bold rounded-[6px] tracking-wide uppercase transition duration-300">
                            Explore Routine
                        </a> -->

                    </div>
                </div>

                <!-- Right Visual Column -->
                <div class="lg:col-span-7 relative flex justify-center lg:justify-end">
                    <div class="relative w-full aspect-[4/3] sm:aspect-[16/10] lg:aspect-square max-w-[620px] rounded-xl overflow-hidden shadow-sm border border-gray-200/40 bg-gray-50">
                        <img src="/images/hero_lifestyle.png" alt="KELVS Skincare Hero" class="object-cover w-full h-full object-center hover:scale-[1.01] transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent pointer-events-none"></div>
                    </div>
                    
                    <!-- Decorative subtle shadow/glow -->
                    <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-[#e8dcd2]/40 rounded-full blur-3xl -z-10"></div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. TRUST BAR (IMMEDIATELY BELOW HERO) -->
    <section class="bg-white border-b border-gray-150 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 text-center md:text-left">
                
                <!-- Trust Item 1 -->
                <div class="flex flex-col md:flex-row items-center md:items-start space-y-2 md:space-y-0 md:space-x-3.5">
                    <div class="p-2 rounded-full bg-gray-50 text-[#111111]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs uppercase font-extrabold tracking-wider text-[#111111]">Dermatologically Inspired</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Formulated for optimal efficacy</p>
                    </div>
                </div>

                <!-- Trust Item 2 -->
                <div class="flex flex-col md:flex-row items-center md:items-start space-y-2 md:space-y-0 md:space-x-3.5">
                    <div class="p-2 rounded-full bg-gray-50 text-[#111111]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs uppercase font-extrabold tracking-wider text-[#111111]">Pakistani Climate Ready</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Optimized for heat & humidity</p>
                    </div>
                </div>

                <!-- Trust Item 3 -->
                <div class="flex flex-col md:flex-row items-center md:items-start space-y-2 md:space-y-0 md:space-x-3.5">
                    <div class="p-2 rounded-full bg-gray-50 text-[#111111]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs uppercase font-extrabold tracking-wider text-[#111111]">Fast National Shipping</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Delivered within 2-4 working days</p>
                    </div>
                </div>

                <!-- Trust Item 4 -->
                <div class="flex flex-col md:flex-row items-center md:items-start space-y-2 md:space-y-0 md:space-x-3.5">
                    <div class="p-2 rounded-full bg-gray-50 text-[#111111]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs uppercase font-extrabold tracking-wider text-[#111111]">Zero Fillers or Fragrance</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Clean, highly focused ingredients</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 3. FEATURED PRODUCTS GRID -->
    <section id="featured-products" class="py-20 md:py-28 bg-[#ffffff]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col items-center text-center space-y-3 mb-16">
                <span class="text-xs font-bold tracking-widest uppercase text-gray-400 bg-gray-50 px-3 py-1 rounded">Shop Bestsellers</span>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-[#111111]">Our Clinical Formulations</h2>
                <p class="text-sm text-gray-500 max-w-md">Targeted solutions designed to deliver visible improvements for your skin type.</p>
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
                <!-- Seeding hint empty state -->
                <div class="text-center py-16 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-sm text-gray-500">No products found. Run database seeders to see products.</p>
                </div>
            @endif

        </div>
    </section>

    <!-- 4. BRAND STORY SECTION (SPLIT LAYOUT) -->
    <section class="py-20 md:py-28 bg-[#fbfbfa] border-t border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Story Image -->
                <div class="lg:col-span-6">
                    <div class="relative aspect-[4/3] sm:aspect-[16/10] lg:aspect-[4/3] rounded-xl overflow-hidden shadow-sm border border-gray-200 bg-white">
                        <img src="/images/brand_story.png" alt="KELVS Clean Skincare Laboratory" class="object-cover w-full h-full" loading="lazy">
                    </div>
                </div>

                <!-- Right Story Text -->
                <div class="lg:col-span-6 space-y-6 lg:pl-8">
                    <span class="text-xs font-bold tracking-widest uppercase text-gray-400 bg-white px-3 py-1 rounded border border-gray-100">Our Heritage</span>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-[#111111] leading-[1.2]">
                        Built to simplify skincare.
                    </h2>
                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed font-normal">
                        Skincare doesn't need to be complicated. KELVS was founded on a simple principle: build formulas that prioritize active ingredients in clinically proven concentrations.
                    </p>
                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed font-normal">
                        Our research focuses on creating clean formulations adapted specifically for the hot and humid Pakistani climate. No unnecessary fillers, no synthetic fragrances, no dyes. Just science-backed hydration and repair.
                    </p>
                    <div class="pt-2">
                        <a href="/sky" class="inline-flex items-center space-x-1 text-sm font-bold text-[#111111] border-b-2 border-[#111111] pb-1 hover:opacity-75 transition">
                            <span>Read Our Research</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 5. BESTSELLERS / SOCIAL PROOF -->
    <section class="py-20 md:py-28 bg-[#ffffff]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col items-center text-center space-y-3 mb-16">
                <span class="text-xs font-bold tracking-widest uppercase text-gray-400 bg-gray-50 px-3 py-1 rounded">Reviews</span>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-[#111111]">Validated by Real Users</h2>
                <p class="text-sm text-gray-500 max-w-md font-normal">Read genuine feedback from customers who simplified their routines.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Review 1 -->
                <div class="bg-gray-50/50 p-8 rounded-xl border border-gray-100 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex text-[#e8dcd2]">
                            <!-- Star SVGs -->
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4.5 h-4.5 fill-current text-[#111111]" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-700 italic leading-relaxed">
                            "this is a super Gentle wash Have used my 3rd Bottle since its fragrance free it can also be used as hygiene wash Loving it so far the best cleanser i have used so far"
                        </p>
                    </div>
                    <div class="flex items-center space-x-3 pt-4 border-t border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-[#111111]">SM</div>
                        <div>
                            <h5 class="text-xs font-bold text-[#111111]">Zunaira.</h5>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">Verified Buyer — Karachi</span>
                        </div>
                    </div>
                </div>

                <!-- Review 2 -->
                <div class="bg-gray-50/50 p-8 rounded-xl border border-gray-100 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex text-[#e8dcd2]">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4.5 h-4.5 fill-current text-[#111111]" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-700 italic leading-relaxed">
                            "KELVS Detangle Shampoo has been a wonderful addition to my hair care routine. Not only is it effective at detangling my hair without causing breakage, but it's also free from harmful chemicals that can be detrimental to both my hair and my health."
                        </p>
                    </div>
                    <div class="flex items-center space-x-3 pt-4 border-t border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-[#111111]">ZR</div>
                        <div>
                            <h5 class="text-xs font-bold text-[#111111]">Aisha.</h5>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">Verified Buyer — Lahore</span>
                        </div>
                    </div>
                </div>

                <!-- Review 3 -->
                <div class="bg-gray-50/50 p-8 rounded-xl border border-gray-100 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex text-[#e8dcd2]">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4.5 h-4.5 fill-current text-[#111111]" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-700 italic leading-relaxed">
                            "Love that some pakistani brand is doing this kind of providing such products at this price point
"
                        </p>
                    </div>
                    <div class="flex items-center space-x-3 pt-4 border-t border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-[#111111]">FK</div>
                        <div>
                            <h5 class="text-xs font-bold text-[#111111]">Uzma.</h5>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">Verified Buyer — Islamabad</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 6. ROUTINE BUILDER SECTION -->
    <section id="routine-builder" class="py-20 md:py-28 bg-[#d8d8d8]/20 border-t border-b border-gray-200/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col items-center text-center space-y-3 mb-16">
                <span class="text-xs font-bold tracking-widest uppercase text-gray-400 bg-white px-3 py-1 rounded border border-gray-150">Skin starts here</span>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-[#111111]">Good skin isn't just what you apply. It's how you live.</h2>
                <!-- <p class="text-sm text-gray-500 max-w-md font-normal">A minimal, science-backed approach. Cleanse, Treat, and Protect daily.</p> -->
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Step 1 -->
                <div class="bg-white p-8 rounded-xl border border-gray-150 flex flex-col justify-between space-y-8 hover:border-gray-300 transition duration-300">
                    <div class="space-y-4">
                        <div class="text-[36px] font-extrabold text-[#e8dcd2] leading-none">01</div>
                        <h3 class="text-lg font-bold text-[#111111]">HYDRATE</h3>
                        <p class="text-xs text-gray-500 leading-relaxed font-normal">
                            Your skin is ~64% water. When you're chronically under-hydrated, the skin barrier weakens, sebum production increases, and blemishes worsen. Aim for 2–3 litres daily — your cleanser and serum work significantly better when your skin isn't compensating for dehydration.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase">Step 01:  Hydrate</span>
                        <a href="/products/kelvs-gentle-gel-cleanser" wire:navigate class="text-xs font-bold text-[#111111] hover:opacity-75 flex items-center space-x-1">
                            <!-- <span>View Product</span>
                            <span>&rarr;</span> -->
                        </a>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="bg-white p-8 rounded-xl border border-gray-150 flex flex-col justify-between space-y-8 hover:border-gray-300 transition duration-300">
                    <div class="space-y-4">
                        <div class="text-[36px] font-extrabold text-[#e8dcd2] leading-none">02</div>
                        <h3 class="text-lg font-bold text-[#111111]">SLEEP</h3>
                        <p class="text-xs text-gray-500 leading-relaxed font-normal">
                            Skin cell turnover peaks between 11pm–4am. Cutting sleep short means less collagen repair, elevated cortisol (which directly triggers breakouts), and a compromised barrier. 7–9 hours isn't beauty advice — it's biology.

                        </p>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase">Step 02:  Sleep</span>
                        <a href="/products/kelvs-niacinamide-zinc-serum" wire:navigate class="text-xs font-bold text-[#111111] hover:opacity-75 flex items-center space-x-1">
                            <!-- <span>View Product</span>
                            <span>&rarr;</span> -->
                        </a>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-white p-8 rounded-xl border border-gray-150 flex flex-col justify-between space-y-8 hover:border-gray-300 transition duration-300">
                    <div class="space-y-4">
                        <div class="text-[36px] font-extrabold text-[#e8dcd2] leading-none">03</div>
                        <h3 class="text-lg font-bold text-[#111111]">MOVE</h3>
                        <p class="text-xs text-gray-500 leading-relaxed font-normal">
                            Exercise increases circulation, delivering oxygen and nutrients to skin cells while flushing out toxins through sweat. Even 20–30 minutes of daily movement measurably reduces cortisol — the same hormone that triggers breakouts and slows healing.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase">Step 03:  Excercise</span>
                        <a href="/products/kelvs-matte-sun-shield-spf-50" wire:navigate class="text-xs font-bold text-[#111111] hover:opacity-75 flex items-center space-x-1">
                            <!-- <span>View Product</span>
                            <span>&rarr;</span> -->
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 7. EMAIL / WHATSAPP CTA SECTION -->
    <section class="py-20 md:py-24 bg-[#ffffff]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#e8dcd2]/45 rounded-2xl p-8 md:p-16 border border-[#e8dcd2]/60 flex flex-col lg:flex-row items-center justify-between gap-8 md:gap-12 relative overflow-hidden">
                
                <!-- Inner background glow -->
                <div class="absolute right-0 bottom-0 w-80 h-80 bg-white/20 rounded-full blur-3xl -z-10"></div>
                
                <div class="max-w-lg space-y-4 text-center lg:text-left">
                    <div class="inline-flex items-center space-x-2 bg-white px-3 py-1 rounded border border-[#e8dcd2]/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#111111]"></span>
                        <span class="text-[10px] uppercase font-extrabold tracking-widest text-[#111111]">Skincare Club</span>
                    </div>
                    <h2 class="text-3xl font-bold tracking-tight text-[#111111] leading-[1.2]">
                        Get skincare guidance + exclusive drops
                    </h2>
                    <p class="text-xs text-gray-650 font-normal leading-relaxed">
                        Join our clean skincare community to receive professional guidance tailored to Pakistani climate, alongside early access to new releases.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto items-stretch">
                    <a href="https://wa.me/923124995545" target="_blank" class="px-8 py-3.5 bg-[#111111] hover:bg-[#222222] text-white text-xs font-bold rounded-[6px] tracking-wide uppercase transition duration-300 flex items-center justify-center space-x-2">
                        <!-- WhatsApp minimalist icon -->
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.734-1.458L0 24zm6.076-4.664c1.623.963 3.224 1.47 4.902 1.471 5.489 0 9.957-4.432 9.96-9.874.002-2.637-1.02-5.117-2.88-6.98-1.859-1.861-4.332-2.884-6.974-2.885-5.49 0-9.959 4.434-9.963 9.876-.001 1.761.472 3.428 1.368 4.933l-.979 3.57 3.666-.961zm10.741-6.938c-.3-.15-1.775-.875-2.049-.974-.275-.1-.475-.15-.675.15-.2.3-.775.974-.95 1.174-.175.2-.35.225-.65.075-.3-.15-1.265-.467-2.41-1.485-.89-.794-1.49-1.775-1.665-2.075-.175-.3-.019-.462.13-.611.135-.134.3-.35.45-.525.15-.175.2-.3.3-.5.1-.2.05-.375-.025-.525-.075-.15-.675-1.625-.925-2.225-.244-.589-.491-.51-.675-.519-.175-.008-.375-.01-.575-.01-.2 0-.525.075-.8.375-.275.3-1.05 1.025-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.22 5.11 4.52.714.31 1.272.495 1.704.633.717.227 1.369.195 1.884.118.574-.085 1.775-.725 2.025-1.425.25-.7.25-1.3.175-1.425-.075-.125-.275-.2-.575-.35z"/>
                        </svg>
                        <span>Join WhatsApp</span>
                    </a>
                    
                    <div class="flex items-stretch border border-gray-300 rounded-[6px] overflow-hidden bg-white max-w-sm">
                        <input type="email" placeholder="Your email address" class="px-4 py-3.5 text-xs text-[#111111] bg-white outline-none w-full border-none">
                        <button class="bg-[#111111] hover:bg-[#222222] text-white text-xs font-bold px-6 py-3.5 transition duration-300 whitespace-nowrap uppercase tracking-wider">
                            Get Offers
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>
