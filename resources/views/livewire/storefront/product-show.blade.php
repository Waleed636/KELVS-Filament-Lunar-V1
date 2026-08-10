<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-[#111111] bg-white">
    
    @php
        $sku = $activeVariant?->sku;
        
        $priceRecord = $activeVariant?->prices->first();
        $price = $priceRecord?->price;
        $comparePrice = $priceRecord?->compare_price;
        $hasDiscount = $comparePrice && $comparePrice->value > $price->value;
        
        // Fetch media items from Spatie Media Library on the Product model
        $mediaItems = $product->getMedia('images');
        $allImages = [];
        $primaryUrl = null;
        $primaryZoomUrl = null;
        
        if ($mediaItems->isNotEmpty()) {
            foreach ($mediaItems as $media) {
                $originalUrl = parse_url($media->getUrl(), PHP_URL_PATH);
                $smallUrl = $media->hasGeneratedConversion('small') ? parse_url($media->getUrl('small'), PHP_URL_PATH) : $originalUrl;
                $largeUrl = $media->hasGeneratedConversion('large') ? parse_url($media->getUrl('large'), PHP_URL_PATH) : $originalUrl;

                $allImages[] = [
                    'small' => $smallUrl,
                    'large' => $largeUrl,
                    'original' => $originalUrl,
                ];
                
                // Track primary image
                if ($media->getCustomProperty('primary') === true && !$primaryUrl) {
                    $primaryUrl = $largeUrl;
                    $primaryZoomUrl = $originalUrl;
                }
            }
            // Fall back to first image if no primary is specified
            if (!$primaryUrl && count($allImages) > 0) {
                $primaryUrl = $allImages[0]['large'];
                $primaryZoomUrl = $allImages[0]['original'];
            }
        }
        
        // Fall back to hardcoded placeholders if no database images exist
        if (!$primaryUrl) {
            $fallbackImage = match($sku) {
                'KELVS-CLEAN-01' => '/images/cleanser.png',
                'KELVS-NIAC-01' => '/images/niacinamide.png',
                'KELVS-BHA-01' => '/images/bha.png',
                'KELVS-HYA-01' => '/images/hyaluronic.png',
                'KELVS-CER-01' => '/images/ceramide.png',
                'KELVS-SPF-01' => '/images/sunshield.png',
                default => '/images/hero_lifestyle.png'
            };
            $primaryUrl = $fallbackImage;
            $primaryZoomUrl = $fallbackImage;
            $allImages = [[
                'small' => $fallbackImage,
                'large' => $fallbackImage,
                'original' => $fallbackImage,
            ]];
        }
        
        $productImage = $primaryUrl;
        $productZoomImage = $primaryZoomUrl;
    @endphp

    <!-- Breadcrumbs -->
    <nav class="flex text-[10px] text-gray-400 uppercase tracking-widest mb-10" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="/" class="hover:text-[#111111] transition">Home</a></li>
            <li><span class="text-gray-300">/</span></li>
            <li class="text-[#111111] truncate max-w-xs font-semibold">{{ $product->attr('name') }}</li>
        </ol>
    </nav>

    <!-- Main Grid -->
    <div x-data="{ activeImage: '{{ $productImage }}', activeZoomImage: '{{ $productZoomImage }}', zoomOpen: false }" class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
        
        <!-- Left Column: Product Media -->
        <div class="lg:col-span-6 flex flex-col space-y-4">
            <!-- Main Image Frame -->
            <div 
                @click="zoomOpen = true" 
                class="aspect-square w-full rounded-2xl overflow-hidden bg-[#f6f6f5] border border-gray-150 flex items-center justify-center p-12 relative shadow-sm cursor-zoom-in group"
            >
                <img src="{{ $productImage }}" :src="activeImage" fetchpriority="high" alt="{{ $product->attr('name') }}" class="object-contain w-full h-full transition duration-300 group-hover:scale-[1.02]">
                
                <!-- Hover indicator icon -->
                <div class="absolute bottom-4 right-4 bg-white/80 backdrop-blur-sm p-2 rounded-full border border-gray-200 shadow-sm opacity-0 group-hover:opacity-100 transition duration-350">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                    </svg>
                </div>
            </div>

            <!-- Lightbox Zoom Modal -->
            <div 
                x-data="{ scale: 1 }"
                x-show="zoomOpen" 
                x-transition:enter="transition ease-out duration-350"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @keydown.escape.window="zoomOpen = false; scale = 1"
                @click="zoomOpen = false; scale = 1"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4 sm:p-6 md:p-10"
                style="display: none;"
            >
                <!-- Close Button -->
                <button 
                    type="button" 
                    @click="zoomOpen = false; scale = 1" 
                    class="absolute top-6 right-6 text-white/70 hover:text-white transition focus:outline-none z-10"
                    aria-label="Close modal"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Zoomed Image Container -->
                <div 
                    @click.away="zoomOpen = false; scale = 1" 
                    class="w-full max-w-5xl h-full max-h-[85vh] flex items-center justify-center overflow-auto rounded-xl bg-[#f6f6f5] relative"
                >
                    <div class="inline-block transition-transform duration-200" :style="'transform: scale(' + scale + ')'">
                        <img 
                            :src="activeZoomImage" 
                            alt="Product detail zoom" 
                            :class="scale === 1 ? 'cursor-zoom-in' : 'cursor-zoom-out'"
                            class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl"
                            @click.stop="scale = (scale === 1 ? 2.2 : 1)"
                        >
                    </div>
                </div>
            </div>

            <!-- Gallery Thumbnails -->
            @if(count($allImages) > 1)
                <div class="flex flex-wrap gap-3 py-1">
                    @foreach($allImages as $img)
                        <button 
                            type="button"
                            @click="activeImage = '{{ $img['large'] }}'; activeZoomImage = '{{ $img['original'] }}'"
                            :class="activeImage === '{{ $img['large'] }}' ? 'border-[#111111] ring-1 ring-[#111111]' : 'border-gray-200 hover:border-gray-400'"
                            class="w-20 h-20 rounded-lg overflow-hidden border bg-[#f6f6f5] flex items-center justify-center p-2 transition focus:outline-none"
                        >
                            <img src="{{ $img['small'] }}" class="object-contain w-full h-full" alt="Thumbnail">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
 
        <!-- Right Column: Product Info -->
        <div class="lg:col-span-6 flex flex-col justify-between h-full space-y-8">
            <div class="space-y-6">
                <!-- Title & Status -->
                <div class="space-y-2">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-[#111111] tracking-tight leading-tight">
                        {{ $product->attr('name') }}
                    </h1>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">SKU: {{ $activeVariant?->sku ?? 'N/A' }}</p>
                        
                        <!-- Star Rating Summary -->
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center text-amber-400">
                                @php
                                    $ratingVal = $this->averageRating;
                                    $fullStars = floor($ratingVal);
                                    $hasHalf = ($ratingVal - $fullStars) >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);
                                @endphp
                                @for($i = 0; $i < $fullStars; $i++)
                                    <svg class="w-8 h-8 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                @if($hasHalf)
                                    <div class="relative w-8 h-8">
                                        <svg class="absolute text-gray-250 fill-current w-8 h-8" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <div class="overflow-hidden absolute top-0 left-0 h-full w-[50%]">
                                            <svg class="text-amber-400 fill-current w-8 h-8" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </div>
                                    </div>
                                @endif
                                @for($i = 0; $i < $emptyStars; $i++)
                                    <svg class="w-8 h-8 text-gray-250 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <a href="#reviews-section" class="text-sm font-bold text-gray-500 hover:text-[#111111] transition underline underline-offset-4 decoration-dotted">
                                {{ $ratingVal > 0 ? $ratingVal : 'No reviews yet' }} ({{ $this->reviews->count() }} {{ \Illuminate\Support\Str::plural('review', $this->reviews->count()) }})
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Price -->
                <div class="flex items-center gap-4">

                    <div class="text-2xl font-extrabold text-[#111111] bg-gray-50 border border-gray-150 px-6 py-3.5 rounded-lg inline-block">
                        {{ $price?->formatted ?? 'N/A' }}
                    </div>

                    @if($hasDiscount)
                        <div class="flex items-center gap-2.5">
                            <span class="line-through text-gray-500 text-base font-semibold" style="text-decoration: line-through;">
                                {{ $comparePrice->formatted }}
                            </span>
                            
                            @php
                                $savings = (($comparePrice->value - $price->value) / $comparePrice->value) * 100;
                                $discountPercent = round($savings);
                            @endphp
                            
                            <span class="text-[9px] font-extrabold text-red-700 bg-red-50 border border-red-100 rounded px-2 py-0.5 uppercase tracking-widest">
                                Save {{ $discountPercent }}%
                            </span>
                        </div>
                    @endif
                </div>

                <!-- ── Benefits / Short Description ──────────────────────── -->
                @php
                    $shortDesc = $product->attr('short_description');
                    // Split by newline so each line becomes its own bullet point
                    $benefitLines = $shortDesc
                        ? array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags((string) $shortDesc))))
                        : [];
                    // Join all lines into a single flowing sentence for display
                    $benefitText = implode(' ', $benefitLines);
                @endphp

                @if($benefitText)
                <div class="rounded-xl bg-[#f7f7f7] border border-[#ebebeb] px-6 py-5">
                    <p class="text-[1.05rem] sm:text-lg font-bold text-[#7a7a7a] leading-relaxed tracking-tight">
                        {{ $benefitText }}
                    </p>
                </div>
                @endif

                <!-- Science-Led Highlights Bar -->
                @php
                    $ph = $product->attr('formula_ph');
                    $actives = $product->attr('active_ingredients');
                    $concern = $product->attr('target_concern');
                    $texture = $product->attr('texture');
                    $hasHighlights = $ph || $actives || $concern || $texture;
                @endphp

                @if($hasHighlights)
                <div class="rounded-xl border border-gray-200 bg-[#fbfbfa] text-xs shadow-sm divide-y divide-gray-200/60 overflow-hidden mb-6">
                    @if($ph || $actives)
                    <div class="grid grid-cols-2 gap-4 p-4 bg-white">
                        @if($ph)
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Formula pH</span>
                            <span class="font-extrabold text-[#111111]">{{ $ph }}</span>
                        </div>
                        @endif
                        @if($actives)
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Key Actives</span>
                            <span class="font-extrabold text-[#111111]">{{ $actives }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($concern)
                    <div class="flex flex-col gap-0.5 p-4">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Target Concern</span>
                        <span class="font-extrabold text-[#111111]">{{ $concern }}</span>
                    </div>
                    @endif
                    @if($texture)
                    <div class="flex flex-col gap-0.5 p-4">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Texture</span>
                        <span class="font-extrabold text-[#111111]">{{ $texture }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Live visitor & Sold count indicators -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <!-- Live Visitor Count -->
                    @php
                        $seed = ($product->id % 6) + 8; // Deterministic seed (8 to 13)
                    @endphp
                    <div x-data="{
                            count: {{ $seed }},
                            fluctuate() {
                                setInterval(() => {
                                    let change = Math.floor(Math.random() * 3) - 1; // -1, 0, or +1
                                    this.count = Math.max(5, this.count + change);
                                }, 8000 + Math.random() * 4000); // dynamic fluctuation interval
                            }
                         }" 
                         x-init="fluctuate()"
                         class="inline-flex items-center gap-2 bg-[#fbfbfa] border border-gray-200/60 px-4 py-2.5 rounded-lg text-xs font-semibold text-gray-500 shadow-sm w-full sm:w-auto"
                    >
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>
                            <span x-text="count" class="text-[#111111] font-bold"></span> people are viewing this product
                        </span>
                    </div>

                    <!-- Sold Count -->
                    @php
                        $soldCount = (($product->id * 3) % 15) + 12; // Deterministic count between 12 and 26
                    @endphp
                    <div class="inline-flex items-center gap-2 bg-[#fbfbfa] border border-gray-200/60 px-4 py-2.5 rounded-lg text-xs font-semibold text-gray-500 shadow-sm w-full sm:w-auto">
                        <span class="text-sm">🔥</span>
                        <span>
                            <span class="text-[#111111] font-bold">{{ $soldCount }} sold</span> in the last 24 hours
                        </span>
                    </div>
                </div>

                <!-- Variants Selection if multiple -->
                @if($product->variants->count() > 1)
                    <div class="space-y-3">
                        <label for="variant" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Select Variant
                        </label>
                        <select id="variant" wire:model.live="variantId" class="w-full bg-white border border-gray-300 rounded-md text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                            @foreach($product->variants as $var)
                                <option value="{{ $var->id }}">
                                    {{ $var->sku }} - {{ $var->prices->first()?->price->formatted }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <!-- Delivery Estimator & Countdown -->
            @php
                $now = now();
                $cutoffHour = 15; // 3 PM
                $shippingDaysMin = 2;
                $shippingDaysMax = 3;

                if ($now->hour >= $cutoffHour) {
                    $dispatchDate = $now->copy()->addDay();
                } else {
                    $dispatchDate = $now;
                }

                $estMin = $dispatchDate->copy()->addDays($shippingDaysMin);
                $estMax = $dispatchDate->copy()->addDays($shippingDaysMax);
                
                if ($estMin->isSunday()) $estMin->addDay();
                if ($estMax->isSunday()) $estMax->addDay();

                $formattedEstMin = $estMin->format('D, M j');
                $formattedEstMax = $estMax->format('D, M j');

                $cutoffToday = $now->copy()->hour($cutoffHour)->minute(0)->second(0);
                if ($now->hour >= $cutoffHour) {
                    $targetTime = $cutoffToday->addDay();
                } else {
                    $targetTime = $cutoffToday;
                }
                $secondsLeft = $targetTime->diffInSeconds($now);
            @endphp
            <div x-data="{
                    secondsLeft: {{ $secondsLeft }},
                    hours: 0,
                    minutes: 0,
                    updateTimer() {
                        if (this.secondsLeft <= 0) return;
                        this.hours = Math.floor(this.secondsLeft / 3600);
                        this.minutes = Math.floor((this.secondsLeft % 3600) / 60);
                    }
                 }"
                 x-init="
                    updateTimer();
                    setInterval(() => {
                        secondsLeft--;
                        if (secondsLeft < 0) secondsLeft = 86400;
                        updateTimer();
                    }, 1000);
                 "
                 class="flex items-center gap-3 bg-amber-50/50 border border-amber-200/50 px-5 py-3 rounded-xl text-xs font-semibold text-amber-800 shadow-sm w-full"
            >
                <span class="text-lg animate-pulse">⚡</span>
                <div class="leading-tight">
                    <p>Order in <span class="font-extrabold text-[#111111]"><span x-text="hours"></span>h <span x-text="minutes"></span>m</span> to get it by <span class="font-extrabold text-[#111111]">{{ $formattedEstMin }} - {{ $formattedEstMax }}</span></p>
                    <p class="text-[10px] text-amber-700/80 font-medium mt-0.5">Dispatched via Express Courier with Cash on Delivery</p>
                </div>
            </div>

            <!-- Cart Controls -->
            <div id="main-cart-controls" class="space-y-6">
                @if(session()->has('message'))
                    <div class="p-4 text-sm text-[#111111] bg-[#e8dcd2] rounded-md font-bold shadow-sm border border-[#e8dcd2]">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row items-stretch gap-4">
                    <!-- Quantity Selector -->
                    <div class="flex items-center border border-gray-300 rounded-md bg-white w-full sm:w-auto h-14 sm:min-w-[140px] px-4 justify-between gap-4 shrink-0">
                        <button type="button" class="text-gray-400 hover:text-[#111111] transition font-bold text-lg" wire:click="$set('quantity', {{ max(1, $quantity - 1) }})">
                            &minus;
                        </button>
                        <input type="number" wire:model.live="quantity" min="1" class="bg-transparent border-0 text-center w-12 text-[#111111] font-bold focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <button type="button" class="text-gray-400 hover:text-[#111111] transition font-bold text-lg" wire:click="$set('quantity', {{ $quantity + 1 }})">
                            &plus;
                        </button>
                    </div>

                    <!-- Add to Cart Button -->
                    <button type="button" wire:click="addToCart" class="w-full sm:flex-1 h-14 bg-white border border-[#111111] hover:bg-gray-50 text-[#111111] font-bold rounded-md tracking-wider uppercase text-xs transition duration-300 flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span>Add to Cart</span>
                    </button>

                    <!-- Buy Now Button -->
                    <button type="button" wire:click="$dispatch('open-buy-now', { variantId: {{ $variantId }}, quantity: {{ $quantity }} })" class="w-full sm:flex-1 h-14 bg-[#111111] hover:bg-[#222222] text-white font-bold rounded-md tracking-wider uppercase text-xs transition duration-300 flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Buy Now</span>
                    </button>
                </div>

                <!-- Assurance Badges -->
                <div class="grid grid-cols-2 gap-4 pt-6 border-t border-gray-100">
                    <div class="flex items-center gap-3 bg-gray-50/50 border border-gray-200/50 rounded-xl p-4">
                        <div class="text-xl shrink-0">💵</div>
                        <div>
                            <p class="text-xs font-bold text-[#111111]">Cash on Delivery</p>
                            <p class="text-[10px] text-gray-400 font-medium">Pay on delivery nationwide</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-gray-50/50 border border-gray-200/50 rounded-xl p-4">
                        <div class="text-xl shrink-0">🚚</div>
                        <div>
                            <p class="text-xs font-bold text-[#111111]">Free Shipping</p>
                            <p class="text-[10px] text-gray-400 font-medium">On all orders above Rs. 2,000</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ── Product Details & In-Depth Information Accordions ─────────── -->
    @if(!empty($this->descriptionSections))
    <div class="mt-20 sm:mt-28 pt-12 sm:pt-16 border-t border-gray-150 max-w-5xl">
        <!-- Section Header -->
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-[11px] sm:text-xs font-extrabold uppercase tracking-wider mb-2.5">
                <svg class="w-3.5 h-3.5 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Product Science & Formulation</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#111111] tracking-tight">Everything You Need to Know</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Key actives, clinical targets, application routine, and frequently asked questions.</p>
        </div>

        <!-- Interactive Accordion Stack -->
        <div x-data="{ 
                openSections: { 0: true }, 
                toggle(idx) { 
                    this.openSections[idx] = !this.openSections[idx]; 
                } 
             }" 
             class="space-y-3.5">
            @foreach($this->descriptionSections as $idx => $section)
                <div class="rounded-2xl border border-gray-200 bg-[#fbfbfa] overflow-hidden transition-all duration-200 hover:border-gray-300 shadow-xs">
                    <!-- Accordion Trigger Button -->
                    <button type="button" 
                            @click="toggle({{ $idx }})" 
                            class="w-full px-5 sm:px-6 py-4.5 sm:py-5 flex items-center justify-between gap-4 text-left transition focus:outline-none bg-white hover:bg-gray-50/70">
                        <div class="flex items-center gap-3.5 sm:gap-4 min-w-0">
                            <!-- Section Icon -->
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gray-100/80 border border-gray-200/60 flex items-center justify-center shrink-0 text-[#111111]">
                                @if($section['type'] === 'overview')
                                    <svg class="w-5 h-5 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                @elseif($section['type'] === 'usage')
                                    <svg class="w-5 h-5 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($section['type'] === 'ingredients')
                                    <svg class="w-5 h-5 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                @elseif($section['type'] === 'faq')
                                    <svg class="w-5 h-5 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Title & Subtitle -->
                            <div class="min-w-0">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="text-sm sm:text-base font-extrabold text-[#111111] tracking-tight">{{ $section['title'] }}</span>
                                    @if(!empty($section['badge']))
                                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $section['badge'] }}
                                        </span>
                                    @endif
                                </div>
                                @if(!empty($section['subtitle']))
                                    <p class="text-[11px] sm:text-xs text-gray-400 font-medium truncate mt-0.5">{{ $section['subtitle'] }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Toggle Icon -->
                        <div class="w-8 h-8 rounded-full border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 transition-transform duration-200"
                             :class="openSections[{{ $idx }}] ? 'rotate-180 bg-black text-white border-black' : 'text-gray-500'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>

                    <!-- Accordion Content Panel -->
                    <div x-show="openSections[{{ $idx }}]" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="border-t border-gray-150 bg-[#fafafa] px-5 sm:px-7 py-5 sm:py-6">
                        
                        @if($section['type'] === 'faq' && !empty($section['faq_items']))
                            <!-- Interactive Nested FAQ Accordions -->
                            <div x-data="{ openFaq: null }" class="space-y-2.5">
                                @foreach($section['faq_items'] as $qIdx => $faq)
                                    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden shadow-2xs">
                                        <button type="button" 
                                                @click="openFaq = (openFaq === {{ $qIdx }} ? null : {{ $qIdx }})"
                                                class="w-full p-3.5 sm:p-4 flex items-center justify-between gap-3 text-left focus:outline-none hover:bg-gray-50/80 transition">
                                            <span class="text-xs sm:text-sm font-bold text-[#111111] flex items-center gap-2">
                                                <span class="w-5 h-5 rounded-full bg-[#111111] text-white text-[10px] font-extrabold flex items-center justify-center shrink-0">Q</span>
                                                <span>{{ $faq['question'] }}</span>
                                            </span>
                                            <span class="w-6 h-6 rounded-full border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 text-xs font-bold text-gray-600 transition"
                                                  :class="openFaq === {{ $qIdx }} ? 'rotate-45 bg-[#111111] text-white border-black' : ''">
                                                +
                                            </span>
                                        </button>
                                        <div x-show="openFaq === {{ $qIdx }}" 
                                             x-transition
                                             class="px-4 sm:px-5 pb-4 pt-1 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100 bg-[#fbfbfa]">
                                            <div class="prose prose-sm max-w-none">
                                                {!! $faq['answer'] !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Rich HTML Content for Overview, Usage, Ingredients -->
                            <div class="text-sm sm:text-base text-gray-600 leading-relaxed space-y-3 prose prose-sm sm:prose-base max-w-none prose-headings:text-[#111111] prose-headings:font-extrabold prose-strong:text-[#111111] prose-strong:font-bold prose-ul:my-2 prose-li:my-0.5">
                                {!! $section['content'] !!}
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ── Verified Customer Showcase Carousel ──────────────────────────── -->
    @if($this->whatsappFeedbacks->isNotEmpty())
    <div x-data="{ 
            activeModalImage: null, 
            activeModalCaption: '', 
            activeModalName: '',
            scrollLeft() { $refs.carousel.scrollBy({ left: -300, behavior: 'smooth' }) },
            scrollRight() { $refs.carousel.scrollBy({ left: 300, behavior: 'smooth' }) }
         }" 
         class="mt-24 sm:mt-32 pt-16 sm:pt-20 border-t border-gray-150 w-full max-w-7xl mx-auto px-4 sm:px-6">
        
        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-3 py-1 rounded-full text-[11px] sm:text-xs font-extrabold uppercase tracking-wider mb-2.5">
                    <svg class="w-3.5 h-3.5 fill-current text-emerald-600 shrink-0" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span>Verified Customer Showcase</span>
                </div>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-[#111111] tracking-tight">Real Customer Photos & Results</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Unfiltered customer photos, unboxing moments, and real feedback.</p>
            </div>
            
            <!-- Navigation Controls (Desktop & Mobile) -->
            <div class="flex items-center gap-3 self-start sm:self-auto">
                <div class="flex items-center gap-1.5">
                    <button type="button" @click="scrollLeft()" class="w-9 h-9 rounded-full border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 flex items-center justify-center transition shadow-sm focus:outline-none">
                        &larr;
                    </button>
                    <button type="button" @click="scrollRight()" class="w-9 h-9 rounded-full border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 flex items-center justify-center transition shadow-sm focus:outline-none">
                        &rarr;
                    </button>
                </div>
                <span class="text-[11px] text-gray-400 font-medium">Swipe or tap to zoom</span>
            </div>
        </div>

        <!-- Carousel Container -->
        <div x-ref="carousel" class="flex gap-4 sm:gap-6 overflow-x-auto snap-x snap-mandatory pb-6 pt-1 scrollbar-none w-full">
            @foreach($this->whatsappFeedbacks as $feedback)
                <div @click="activeModalImage = '{{ Storage::url($feedback->image_path) }}'; activeModalCaption = '{{ e($feedback->caption) }}'; activeModalName = '{{ e($feedback->customer_name) }}'"
                     style="width: 250px; min-width: 250px; height: 390px; max-height: 60vh;"
                     class="snap-start shrink-0 sm:!w-[320px] sm:!min-w-[320px] sm:!h-[500px] sm:!max-h-[75vh] group cursor-pointer relative rounded-2xl overflow-hidden bg-gray-900 border border-gray-200/80 shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    
                    <!-- Feedback Image (Fixed Uniform Bounds + Auto Trim) -->
                    <img src="{{ Storage::url($feedback->image_path) }}" 
                         alt="{{ $feedback->customer_name ?? 'Customer Review' }}" 
                         style="width: 100%; height: 100%; object-fit: cover; object-position: center;"
                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">

                    <!-- Top Glass Header Overlay -->
                    <div class="absolute inset-x-0 top-0 p-2.5 sm:p-3.5 bg-gradient-to-b from-black/80 via-black/40 to-transparent flex items-center justify-between z-10">
                        <span class="bg-emerald-500/90 backdrop-blur-sm text-white text-[9px] sm:text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full flex items-center gap-1 border border-emerald-400/30">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Verified Review
                        </span>
                        <div class="flex items-center text-amber-400 text-[10px] sm:text-xs">
                            @for($i = 0; $i < ($feedback->rating ?? 5); $i++)
                                ★
                            @endfor
                        </div>
                    </div>

                    <!-- Click to Expand Prompt Overlay (On Hover) -->
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none z-10">
                        <span class="bg-white/95 backdrop-blur-md text-[#111111] text-[11px] sm:text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 transform scale-95 group-hover:scale-100 transition-transform">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            Tap to Zoom
                        </span>
                    </div>

                    <!-- Bottom Content Overlay -->
                    <div class="absolute inset-x-0 bottom-0 p-3 sm:p-4 bg-gradient-to-t from-black/90 via-black/60 to-transparent text-white pt-8 z-10">
                        @if($feedback->caption)
                            <p class="text-[11px] sm:text-xs font-medium text-gray-200 italic line-clamp-2 mb-1.5 leading-tight sm:leading-relaxed">
                                "{{ $feedback->caption }}"
                            </p>
                        @endif
                        <div class="flex items-center justify-between border-t border-white/20 pt-1.5 text-[10px] sm:text-[11px]">
                            <span class="font-bold text-white truncate max-w-[120px] sm:max-w-[180px]">{{ $feedback->customer_name ?? 'Verified Customer' }}</span>
                            <span class="text-emerald-400 font-semibold text-[9px] sm:text-[10px] shrink-0">Verified Photo</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Lightbox Zoom Modal -->
        <template x-teleport="body">
            <div x-show="activeModalImage" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="activeModalImage = null"
                 class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 bg-black/90 backdrop-blur-md"
                 style="display: none;">
                
                <!-- Backdrop Click -->
                <div class="absolute inset-0" @click="activeModalImage = null"></div>

                <!-- Modal Content Card -->
                <div class="relative max-w-md sm:max-w-2xl w-full bg-[#111111] rounded-2xl overflow-hidden shadow-2xl z-10 border border-gray-800 flex flex-col max-h-[92vh]">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-3.5 border-b border-gray-800 bg-[#181818]">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs sm:text-sm font-bold text-white truncate" x-text="activeModalName || 'Customer Photo'"></span>
                        </div>
                        <button type="button" @click="activeModalImage = null" class="text-gray-400 hover:text-white transition p-1 text-xl font-bold leading-none">
                            &times;
                        </button>
                    </div>

                    <!-- Scrollable Modal Image -->
                    <div class="overflow-y-auto p-2 flex-1 bg-black flex items-center justify-center min-h-[300px]">
                        <img :src="activeModalImage" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg">
                    </div>

                    <!-- Footer Caption -->
                    <div x-show="activeModalCaption" class="p-3.5 border-t border-gray-800 bg-[#181818] text-xs text-gray-300 italic">
                        "<span x-text="activeModalCaption"></span>"
                    </div>
                </div>
            </div>
        </template>
    </div>
    @endif

    <!-- ── Customer Reviews Section ──────────────────────────────────── -->
    <div id="reviews-section" class="mt-20 sm:mt-28 pt-12 sm:pt-16 border-t border-gray-150 max-w-7xl">
        <!-- Section Header with Total Badge & Write Review CTA -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-[11px] sm:text-xs font-extrabold uppercase tracking-wider mb-2.5">
                    <svg class="w-3.5 h-3.5 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    <span>Verified Customer Ratings</span>
                </div>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-[#111111] tracking-tight flex items-center gap-3">
                    <span>Customer Reviews</span>
                    <span class="text-xs sm:text-sm font-bold bg-[#111111] text-white px-2.5 py-0.5 rounded-full">{{ $this->reviews->count() }}</span>
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Real feedback from genuine verified buyers across Pakistan.</p>
            </div>

            <!-- Write Review CTA Button (Quick Trigger) -->
            <button type="button" 
                    wire:click="toggleReviewForm"
                    class="self-start sm:self-auto inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-[#111111] text-xs font-extrabold uppercase tracking-wider shadow-xs transition duration-200">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span>{{ $showReviewForm ? 'Close Form' : 'Write a Review' }}</span>
            </button>
        </div>

        <!-- Reviews Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            <!-- Left Side: Summary Stats & Optional Collapsible Form -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Stats Summary Card -->
                <div class="rounded-2xl border border-gray-200 bg-[#fafafa] p-6 space-y-6 shadow-xs">
                    <!-- Big Score & Stars -->
                    <div class="text-center sm:text-left">
                        <div class="flex items-baseline justify-center sm:justify-start gap-2">
                            <span class="text-4xl sm:text-5xl font-extrabold text-[#111111] tracking-tight">{{ $this->averageRating }}</span>
                            <span class="text-xs font-bold text-gray-400 uppercase">out of 5.0</span>
                        </div>
                        <div class="flex items-center justify-center sm:justify-start text-amber-400 mt-2">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 {{ $i < floor($this->averageRating) ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Based on {{ $this->reviews->count() }} verified {{ \Illuminate\Support\Str::plural('rating', $this->reviews->count()) }}</p>
                    </div>

                    <!-- Interactive Click-to-Filter Rating Distribution Bars -->
                    <div class="space-y-2 border-t border-gray-200/70 pt-5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Filter by Star Rating</p>
                        @foreach($this->ratingDistribution as $stars => $percentage)
                            @php
                                $starCount = $this->ratingCounts[$stars] ?? 0;
                                $isActive = $selectedRating === $stars;
                            @endphp
                            <button type="button" 
                                    wire:click="filterByRating({{ $stars }})" 
                                    class="w-full flex items-center text-xs group py-1.5 px-2 rounded-lg transition {{ $isActive ? 'bg-[#111111] text-white shadow-xs' : 'hover:bg-gray-100 text-gray-600' }}">
                                <span class="w-12 text-left font-bold {{ $isActive ? 'text-white' : 'text-gray-700' }}">{{ $stars }} ★</span>
                                <div class="flex-1 h-2.5 bg-gray-200 rounded-full mx-2.5 overflow-hidden">
                                    <div class="h-full bg-amber-400 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="w-10 text-right font-semibold text-[11px] {{ $isActive ? 'text-gray-200' : 'text-gray-400' }}">
                                    {{ $starCount }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                    @if($selectedRating)
                    <div class="pt-2">
                        <button type="button" 
                                wire:click="clearRatingFilter" 
                                class="w-full text-center text-xs font-bold text-gray-600 hover:text-black py-2 border border-dashed border-gray-300 rounded-lg hover:border-gray-400 transition bg-white">
                            ✕ Clear Star Filter (Showing {{ $selectedRating }}★ only)
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Collapsible Write a Review Form -->
                @if($showReviewForm)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 space-y-4 shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-150 pb-3">
                        <h3 class="text-sm font-extrabold uppercase tracking-widest text-[#111111]">Write a Review</h3>
                        <button type="button" wire:click="toggleReviewForm" class="text-gray-400 hover:text-black text-xl leading-none font-bold">&times;</button>
                    </div>
                    
                    @if(session()->has('review_message'))
                        <div class="p-4 text-xs text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 font-semibold">
                            {{ session('review_message') }}
                        </div>
                    @else
                        <form wire:submit="submitReview" class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label for="newName" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Your Name</label>
                                <input type="text" id="newName" wire:model.live="newName" placeholder="e.g. Ayesha Khan" class="w-full bg-[#fbfbfa] border border-gray-200 rounded-lg text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                                @error('newName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Rating -->
                            <div>
                                <label for="newRating" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Rating</label>
                                <select id="newRating" wire:model.live="newRating" class="w-full bg-[#fbfbfa] border border-gray-200 rounded-lg text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                                    <option value="5">★★★★★ (5 Stars - Excellent)</option>
                                    <option value="4">★★★★☆ (4 Stars - Very Good)</option>
                                    <option value="3">★★★☆☆ (3 Stars - Average)</option>
                                    <option value="2">★★☆☆☆ (2 Stars - Below Average)</option>
                                    <option value="1">★☆☆☆☆ (1 Star - Poor)</option>
                                </select>
                                @error('newRating') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Title -->
                            <div>
                                <label for="newTitle" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Review Title (Optional)</label>
                                <input type="text" id="newTitle" wire:model.live="newTitle" placeholder="e.g. Cleared my dark spots in 3 weeks!" class="w-full bg-[#fbfbfa] border border-gray-200 rounded-lg text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                                @error('newTitle') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Comment -->
                            <div>
                                <label for="newComment" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Your Experience / Comment</label>
                                <textarea id="newComment" wire:model.live="newComment" rows="4" placeholder="How did this product work for your skin type? How long did it take to see results?" class="w-full bg-[#fbfbfa] border border-gray-200 rounded-lg text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]"></textarea>
                                @error('newComment') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="w-full h-11 bg-[#111111] hover:bg-[#222222] text-white font-bold rounded-lg tracking-wider uppercase text-[10px] transition duration-300 flex items-center justify-center shadow-sm">
                                Submit Review
                            </button>
                        </form>
                    @endif
                </div>
                @endif
            </div>

            <!-- Right Side: Interactive Filter Toolbar & Individual Reviews List -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Filter Pills & Sorting Bar -->
                <div class="flex flex-wrap items-center justify-between gap-4 pb-2 border-b border-gray-150">
                    <!-- Rating Pills -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" 
                                wire:click="clearRatingFilter" 
                                class="px-3 py-1.5 rounded-full text-xs font-extrabold transition {{ is_null($selectedRating) ? 'bg-[#111111] text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            All ({{ $this->reviews->count() }})
                        </button>
                        @foreach([5, 4, 3, 2, 1] as $stars)
                            @if(($this->ratingCounts[$stars] ?? 0) > 0)
                                <button type="button" 
                                        wire:click="filterByRating({{ $stars }})" 
                                        class="px-3 py-1.5 rounded-full text-xs font-extrabold transition {{ $selectedRating === $stars ? 'bg-[#111111] text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $stars }} ★ ({{ $this->ratingCounts[$stars] }})
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Sort Selector -->
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Sort:</span>
                        <select wire:model.live="sortReviewsBy" class="bg-white border border-gray-200 rounded-lg text-xs font-bold py-1.5 pl-2.5 pr-8 focus:outline-none focus:ring-1 focus:ring-[#111111] cursor-pointer">
                            <option value="latest">Newest First</option>
                            <option value="highest">Highest Rating</option>
                            <option value="lowest">Lowest Rating</option>
                        </select>
                    </div>
                </div>

                <!-- Reviews Feed -->
                @if($this->filteredReviews->isEmpty())
                    <div class="rounded-2xl border border-dashed border-gray-200 py-16 text-center text-gray-400 bg-[#fafafa]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 opacity-50 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <p class="font-bold text-sm text-[#111111]">No reviews found matching this filter</p>
                        <p class="text-xs mt-1 text-gray-400">Try selecting another star rating or clear the filter.</p>
                        @if($selectedRating)
                            <button type="button" wire:click="clearRatingFilter" class="mt-4 inline-flex items-center px-4 py-2 bg-[#111111] text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition shadow-xs">
                                View All Reviews
                            </button>
                        @endif
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($this->filteredReviews as $review)
                            <div class="rounded-2xl border border-gray-200/90 bg-[#fbfbfa] p-5 sm:p-6 space-y-3.5 shadow-2xs hover:border-gray-300 transition">
                                <!-- Top Row: Initial Avatar, Name, Verification, Date -->
                                <div class="flex items-center justify-between text-xs gap-3">
                                    <div class="flex items-center gap-3">
                                        <!-- Avatar with Customer Initial -->
                                        <div class="w-8 h-8 rounded-full bg-[#111111] text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow-xs">
                                            {{ strtoupper(substr($review->customer_name ?? 'C', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-sm text-[#111111]">{{ $review->customer_name }}</span>
                                                <span class="flex items-center gap-1 text-[9px] font-extrabold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                    </svg>
                                                    Verified Buyer
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-gray-400 font-semibold text-[11px] shrink-0">{{ $review->created_at?->format('M d, Y') ?? 'Recent' }}</span>
                                </div>

                                <!-- Middle Row: Star Rating & Title -->
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center text-amber-400">
                                            @for($i = 0; $i < 5; $i++)
                                                <svg class="w-4 h-4 {{ $i < $review->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        @if($review->title)
                                            <span class="font-extrabold text-[#111111] text-sm tracking-tight">{{ $review->title }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Review Body with Expand/Collapse toggle for long text -->
                                @php
                                    $commentText = $review->comment ?? '';
                                    $isLong = mb_strlen($commentText) > 220;
                                @endphp
                                @if($isLong)
                                    <div x-data="{ expanded: false }">
                                        <p x-show="!expanded" class="text-sm text-gray-600 leading-relaxed">
                                            {{ mb_substr($commentText, 0, 220) }}…
                                            <button type="button" @click="expanded = true" class="text-xs font-bold text-[#111111] hover:underline ml-1">
                                                Read more &rarr;
                                            </button>
                                        </p>
                                        <p x-show="expanded" class="text-sm text-gray-600 leading-relaxed whitespace-pre-line" style="display: none;">
                                            {{ $commentText }}
                                            <button type="button" @click="expanded = false" class="block text-xs font-bold text-[#111111] hover:underline mt-1.5">
                                                Show less &larr;
                                            </button>
                                        </p>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $commentText }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Progressive Disclosure / Load More Button -->
                    @if($this->hasMoreReviews)
                        <div class="text-center pt-4">
                            <button type="button" 
                                    wire:click="loadMoreReviews"
                                    class="inline-flex items-center gap-2 px-8 py-3.5 bg-white border border-gray-300 hover:border-black text-[#111111] text-xs font-extrabold uppercase tracking-wider rounded-xl shadow-xs hover:shadow-md transition duration-200">
                                <span wire:loading.remove wire:target="loadMoreReviews">
                                    Load More Reviews ({{ $this->remainingReviewsCount }} remaining)
                                </span>
                                <span wire:loading wire:target="loadMoreReviews" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Loading reviews...
                                </span>
                            </button>
                        </div>
                    @endif
                @endif
            </div>

        </div>

    <!-- Shift WhatsApp button and recent buyer toast to clear the sticky checkout bar on mobile/desktop -->
    <style>
        body .wa-btn-wrap {
            bottom: 80px !important; /* Move WhatsApp button up on the product page */
        }
        @media (max-width: 639px) {
            body .recent-buyer-toast {
                bottom: 5rem !important; /* Move toast up on mobile screen sizes to match WhatsApp */
            }
        }
        @media (min-width: 640px) {
            body .recent-buyer-toast {
                bottom: 5rem !important; /* Keep toast shifted up on desktop viewports to clear bottom bar span */
            }
        }
    </style>

    <!-- Sticky Add to Cart & Buy Now Bar -->
    <div x-data="{
             showSticky: false,
             init() {
                 const observer = new IntersectionObserver((entries) => {
                     this.showSticky = !entries[0].isIntersecting;
                 }, { threshold: 0 });
                 const target = document.getElementById('main-cart-controls');
                 if (target) {
                     observer.observe(target);
                 }
             }
         }"
         x-show="showSticky"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-250 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-[99] bg-white/95 backdrop-blur-md border-t border-gray-200/80 shadow-[0_-6px_25px_rgba(0,0,0,0.06)] py-3 px-4 sm:px-6 lg:px-8"
         style="display: none;"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <!-- Left: Product details (hidden on tiny mobile, visible sm+) -->
            <div class="hidden sm:flex items-center gap-3 min-w-0">
                <img src="{{ $productImage }}" class="w-10 h-10 object-contain rounded-lg bg-[#f6f6f5] border border-gray-200/50 shrink-0 shadow-sm">
                <div class="min-w-0 text-left">
                    <p class="font-bold text-xs text-[#111111] truncate max-w-[200px] md:max-w-[320px]">{{ $product->attr('name') }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[11px] font-extrabold text-[#111111]">{{ $price?->formatted }}</span>
                        @if($hasDiscount)
                        <span class="line-through text-gray-400 text-[10px]" style="text-decoration: line-through;">{{ $comparePrice->formatted }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Action Buttons (full width on mobile, auto on sm+) -->
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <button type="button" 
                        wire:click="addToCart" 
                        class="flex-1 sm:flex-initial h-11 px-5 bg-white border border-[#111111] hover:bg-gray-50 text-[#111111] font-bold rounded-md tracking-wider uppercase text-[10px] transition duration-300 flex items-center justify-center gap-1.5 shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span>Add to Cart</span>
                </button>

                <button type="button" 
                        wire:click="$dispatch('open-buy-now', { variantId: {{ $variantId }}, quantity: {{ $quantity }} })" 
                        class="flex-1 sm:flex-initial h-11 px-6 bg-[#111111] hover:bg-[#222222] text-white font-bold rounded-md tracking-wider uppercase text-[10px] transition duration-300 flex items-center justify-center gap-1.5 shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Buy Now</span>
                </button>
            </div>
        </div>
    </div>

    @script
    <script>
        @if($dataLayerPayload)
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                event: '{{ $dataLayerPayload['eventName'] }}',
                event_id: '{{ $dataLayerPayload['eventId'] }}',
                ecommerce: @json($dataLayerPayload['ecommerceData'])
            });
        @endif
    </script>
    @endscript

</div>
