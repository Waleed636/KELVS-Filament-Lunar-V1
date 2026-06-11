<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-[#111111] bg-white">
    
    @php
        $sku = $activeVariant?->sku;
        $productImage = match($sku) {
            'KELVS-CLEAN-01' => '/images/cleanser.png',
            'KELVS-NIAC-01' => '/images/niacinamide.png',
            'KELVS-BHA-01' => '/images/bha.png',
            'KELVS-HYA-01' => '/images/hyaluronic.png',
            'KELVS-CER-01' => '/images/ceramide.png',
            'KELVS-SPF-01' => '/images/sunshield.png',
            default => '/images/hero_lifestyle.png'
        };
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

    <!-- Breadcrumbs -->
    <nav class="flex text-[10px] text-gray-400 uppercase tracking-widest mb-10" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="/" wire:navigate class="hover:text-[#111111] transition">Home</a></li>
            <li><span class="text-gray-300">/</span></li>
            <li class="text-[#111111] truncate max-w-xs font-semibold">{{ $product->attr('name') }}</li>
        </ol>
    </nav>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
        
        <!-- Left Column: Product Media -->
        <div class="lg:col-span-6 flex flex-col space-y-4">
            <div class="aspect-square w-full rounded-2xl overflow-hidden bg-[#f6f6f5] border border-gray-150 flex items-center justify-center p-12 relative shadow-sm">
                <img src="{{ $productImage }}" alt="{{ $product->attr('name') }}" class="object-contain w-full h-full">
            </div>
        </div>
 
        <!-- Right Column: Product Info -->
        <div class="lg:col-span-6 flex flex-col justify-between h-full space-y-8">
            <div class="space-y-6">
                <!-- Title & Status -->
                <div class="space-y-2">
                    <span class="text-[11px] uppercase font-extrabold tracking-widest text-[#111111] bg-gray-50 border border-gray-150 px-2.5 py-1 rounded inline-block">
                        {{ $benefit }}
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-[#111111] tracking-tight leading-tight">
                        {{ $product->attr('name') }}
                    </h1>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">SKU: {{ $activeVariant?->sku ?? 'N/A' }}</p>
                </div>

                <!-- Price -->
                <div class="text-2xl font-extrabold text-[#111111] bg-gray-50 border border-gray-150 px-6 py-3.5 rounded-lg inline-block">
                    {{ $activeVariant?->prices->first()?->price->formatted ?? 'N/A' }}
                </div>

                <!-- Short description / details -->
                <div class="text-sm text-gray-650 leading-relaxed font-normal space-y-4 border-t border-b border-gray-100 py-6">
                    <p>{!! $product->attr('description') !!}</p>
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

            <!-- Cart Controls -->
            <div class="space-y-6">
                @if(session()->has('message'))
                    <div class="p-4 text-sm text-[#111111] bg-[#e8dcd2] rounded-md font-bold shadow-sm border border-[#e8dcd2]">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <!-- Quantity Selector -->
                    <div class="flex items-center border border-gray-300 rounded-md bg-white h-14 w-full sm:w-auto px-4 justify-between sm:justify-start gap-4">
                        <button type="button" class="text-gray-400 hover:text-[#111111] transition font-bold text-lg" wire:click="$set('quantity', {{ max(1, $quantity - 1) }})">
                            &minus;
                        </button>
                        <input type="number" wire:model.live="quantity" min="1" class="bg-transparent border-0 text-center w-12 text-[#111111] font-bold focus:outline-none focus:ring-0">
                        <button type="button" class="text-gray-400 hover:text-[#111111] transition font-bold text-lg" wire:click="$set('quantity', {{ $quantity + 1 }})">
                            &plus;
                        </button>
                    </div>

                    <!-- Add to Cart Button -->
                    <button type="button" wire:click="addToCart" class="flex-grow w-full h-14 bg-[#111111] hover:bg-[#222222] text-white font-bold rounded-md tracking-wider uppercase text-xs transition duration-300 flex items-center justify-center space-x-2 shadow-sm">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span>Add to Cart</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

</div>
