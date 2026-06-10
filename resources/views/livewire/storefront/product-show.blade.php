<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    
    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-slate-500 uppercase tracking-wider mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="/" wire:navigate class="hover:text-amber-400">Home</a></li>
            <li><span class="text-slate-700">/</span></li>
            <li class="text-slate-300 truncate max-w-xs">{{ $product->attr('name') }}</li>
        </ol>
    </nav>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
        
        <!-- Left Column: Product Media -->
        <div class="flex flex-col space-y-4">
            <div class="aspect-square w-full rounded-2xl overflow-hidden bg-slate-900 border border-slate-900 flex items-center justify-center relative">
                @if($product->thumbnail)
                    <img src="{{ $product->thumbnail->getUrl() }}" alt="{{ $product->attr('name') }}" class="object-cover w-full h-full">
                @else
                    <svg class="w-24 h-24 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                @endif
            </div>
        </div>

        <!-- Right Column: Product Info -->
        <div class="flex flex-col justify-between">
            <div class="space-y-6">
                <!-- Title & Status -->
                <div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-100 tracking-tight">
                        {{ $product->attr('name') }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-2 uppercase tracking-widest">SKU: {{ $activeVariant?->sku ?? 'N/A' }}</p>
                </div>

                <!-- Price -->
                <div class="text-3xl font-extrabold text-amber-400 bg-slate-900/30 border border-slate-900 px-6 py-4 rounded-xl inline-block">
                    {{ $activeVariant?->prices->first()?->price->formatted ?? 'N/A' }}
                </div>

                <!-- Short description / details -->
                <div class="prose prose-invert text-sm text-slate-400 leading-relaxed max-w-none">
                    {!! $product->attr('description') !!}
                </div>

                <!-- Variants Selection if multiple -->
                @if($product->variants->count() > 1)
                    <div class="border-t border-slate-900 pt-6">
                        <label for="variant" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
                            Select Variant
                        </label>
                        <select id="variant" wire:model.live="variantId" class="w-full bg-slate-900 border border-slate-800 rounded-lg text-slate-300 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
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
            <div class="border-t border-slate-900 pt-8 mt-8 space-y-6">
                @if(session()->has('message'))
                    <div class="p-4 text-sm text-amber-950 bg-amber-400 rounded-lg font-semibold shadow-md">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <!-- Quantity Selector -->
                    <div class="flex items-center border border-slate-800 rounded-lg bg-slate-900 h-14 w-full sm:w-auto px-4 justify-between sm:justify-start gap-4">
                        <button type="button" class="text-slate-400 hover:text-amber-400 transition font-bold text-lg" wire:click="$set('quantity', {{ max(1, $quantity - 1) }})">
                            &minus;
                        </button>
                        <input type="number" wire:model.live="quantity" min="1" class="bg-transparent border-0 text-center w-12 text-slate-200 font-bold focus:outline-none focus:ring-0">
                        <button type="button" class="text-slate-400 hover:text-amber-400 transition font-bold text-lg" wire:click="$set('quantity', {{ $quantity + 1 }})">
                            &plus;
                        </button>
                    </div>

                    <!-- Add to Cart Button -->
                    <button type="button" wire:click="addToCart" class="flex-grow w-full h-14 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-300 hover:to-orange-400 text-slate-950 font-bold rounded-lg shadow-lg hover:shadow-amber-500/20 hover:scale-[1.01] transition duration-300 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span>Add to Cart</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

</div>
