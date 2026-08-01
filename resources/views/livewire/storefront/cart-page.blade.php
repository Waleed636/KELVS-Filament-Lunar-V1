<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-[#111111] bg-white">
    <h1 class="text-3xl font-extrabold text-[#111111] tracking-tight mb-8">Your Shopping Cart</h1>

    @if(!$cart || $cart->lines->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-20 bg-[#fbfbfa] rounded-xl border border-dashed border-gray-200">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h2 class="text-xl font-bold text-[#111111]">Your cart is empty</h2>
            <p class="text-sm text-gray-505 mt-2 max-w-sm mx-auto">Fill it with premium products from our catalog to start your skincare routine.</p>
            <div class="mt-8">
                <a href="/" class="px-8 py-3.5 bg-[#111111] hover:bg-[#222222] text-white text-xs uppercase font-extrabold tracking-wider rounded-md transition shadow-sm">
                    Continue Shopping
                </a>
            </div>
        </div>
    @else
        <!-- Cart Table and Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Cart Items List -->
            <div class="lg:col-span-8 space-y-6">
                @foreach($cart->lines as $line)
                    @php
                        $variant = $line->purchasable;
                        $product = $variant?->product;
                        $sku = $variant?->sku;
                        
                        // Fetch media item from Spatie Media Library
                        $media = $product?->getMedia('images')->first(fn ($media) => $media->getCustomProperty('primary') === true) 
                            ?? $product?->getMedia('images')->first();
                        
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
                    @if($variant && $product)
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-6 bg-white border border-gray-150 rounded-xl hover:border-gray-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.01)] transition duration-300 gap-6">
                            <!-- Left section: Thumbnail & Name -->
                            <div class="flex items-center space-x-5 flex-grow">
                                <div class="w-20 h-20 rounded-lg overflow-hidden bg-[#f6f6f5] flex-shrink-0 flex items-center justify-center border border-gray-100 p-2">
                                    <img src="{{ $productImage }}" alt="{{ $product->attr('name') }}" class="object-contain w-full h-full">
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[9px] uppercase font-extrabold tracking-wider text-gray-400 block">{{ $benefit }}</span>
                                    <h3 class="text-sm font-bold text-[#111111] hover:opacity-80 transition">
                                        <a href="/products/{{ $product->urls->first()?->slug }}">
                                            {{ $product->attr('name') }}
                                        </a>
                                    </h3>
                                    <p class="text-[9px] text-gray-400 uppercase tracking-widest font-semibold">SKU: {{ $variant->sku }}</p>
                                </div>
                            </div>

                            <!-- Right section: Price, Quantity & Remove -->
                            <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-8 sm:gap-12 border-t border-gray-150 sm:border-0 pt-4 sm:pt-0">
                                <!-- Price & Subtotal -->
                                <div class="text-left sm:text-right">
                                    <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider">Price</span>
                                    <span class="text-sm font-bold text-[#111111] block">{{ $line->subTotal->formatted }}</span>
                                </div>

                                <!-- Quantity Selector -->
                                <div>
                                    <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1.5 text-center sm:text-left">Qty</span>
                                    <div class="flex items-center border border-gray-300 rounded bg-white h-10 px-2.5 justify-center gap-3">
                                        <button type="button" class="text-gray-400 hover:text-[#111111] transition font-bold" wire:click="updateQuantity({{ $line->id }}, {{ $line->quantity - 1 }})">
                                            &minus;
                                        </button>
                                        <span class="w-6 text-center text-[#111111] font-bold text-xs select-none">{{ $line->quantity }}</span>
                                        <button type="button" class="text-gray-400 hover:text-[#111111] transition font-bold" wire:click="updateQuantity({{ $line->id }}, {{ $line->quantity + 1 }})">
                                            &plus;
                                        </button>
                                    </div>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" class="p-2 text-gray-400 hover:text-red-500 transition mt-4 sm:mt-0" wire:click="removeLine({{ $line->id }})" title="Remove item">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Checkout Summary Card -->
            <div class="lg:col-span-4 bg-[#fbfbfa] border border-gray-150 rounded-xl p-8 space-y-6">
                <h2 class="text-lg font-bold text-[#111111]">Order Summary</h2>

                <div class="space-y-4 border-b border-gray-200/60 pb-6 text-sm">
                    <div class="flex justify-between text-gray-650">
                        <span>Subtotal</span>
                        <span class="font-bold text-[#111111]">{{ $cart->subTotal->formatted }}</span>
                    </div>
                    <div class="flex justify-between text-gray-650">
                        <span>Discount</span>
                        <span class="font-bold text-[#111111]">-</span>
                    </div>
                    <div class="flex justify-between text-gray-400 text-xs leading-relaxed">
                        <span>Taxes & Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>
                </div>

                <div class="flex justify-between items-center text-lg font-bold">
                    <span class="text-[#111111]">Total</span>
                    <span class="text-[#111111]">{{ $cart->total->formatted }}</span>
                </div>

                <div class="pt-4">
                    <a href="/checkout" class="block w-full py-4 text-center bg-[#111111] hover:bg-[#222222] text-white text-xs uppercase font-extrabold tracking-wider rounded-md shadow-sm transition duration-300">
                        Proceed to Checkout
                    </a>
                    <a href="/" class="block text-center text-xs font-bold text-gray-505 hover:text-[#111111] transition mt-4">
                        &larr; Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @endif

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
