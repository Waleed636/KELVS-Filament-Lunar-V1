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
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-[#111111] tracking-tight leading-tight">
                        {{ $product->attr('name') }}
                    </h1>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">SKU: {{ $activeVariant?->sku ?? 'N/A' }}</p>
                </div>

                <!-- Price -->
                <div class="text-2xl font-extrabold text-[#111111] bg-gray-50 border border-gray-150 px-6 py-3.5 rounded-lg inline-block">
                    {{ $activeVariant?->prices->first()?->price->formatted ?? 'N/A' }}
                </div>

                <!-- ── Benefits / Short Description ──────────────────────── -->
                @php
                    $shortDesc = $product->attr('short_description');
                    // Split by newline so each line becomes its own bullet point
                    $benefitLines = $shortDesc
                        ? array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags((string) $shortDesc))))
                        : [];
                @endphp

                @if(count($benefitLines))
                <div class="rounded-xl border border-[#e8dcd2] bg-[#fdf9f7] px-5 py-4 space-y-3">

                    <!-- Header -->
                    <p class="flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-widest text-[#a07850]">
                        <!-- Leaf / plant icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 8C8 10 5.9 16.17 3.82 19.34A1 1 0 0 0 4.69 21h.31a1 1 0 0 0 .76-.35C9 17 12 16 17 16V8z"/>
                            <path d="M17 8v8"/>
                        </svg>
                        What it does for you
                    </p>

                    <!-- Bullet list — each newline from the admin field becomes its own row -->
                    <ul class="space-y-2">
                        @foreach($benefitLines as $line)
                            <li class="flex items-start gap-2.5 text-sm text-[#3d3530] leading-snug">
                                <span class="mt-0.5 flex-shrink-0 w-4 h-4 rounded-full bg-[#e8dcd2] flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-[#a07850]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </span>
                                <span>{{ $line }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

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
                    <button type="button" wire:click="addToCart" class="w-full sm:flex-1 h-14 bg-[#111111] hover:bg-[#222222] text-white font-bold rounded-md tracking-wider uppercase text-xs transition duration-300 flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span>Add to Cart</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- ── Full Description — below the product grid ──────────────────── -->
    @if($product->attr('description'))
    <div class="mt-40 pt-20 border-t border-gray-100 max-w-4xl">
        <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-400 mb-5 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Product Description
        </h2>
        <div class="rounded-xl border border-gray-100 bg-[#fafafa] px-7 py-6">
            <div class="text-base text-gray-600 leading-7 font-normal space-y-4 prose prose-base max-w-none">
                {!! $product->attr('description') !!}
            </div>
        </div>
    </div>
    @endif

</div>
