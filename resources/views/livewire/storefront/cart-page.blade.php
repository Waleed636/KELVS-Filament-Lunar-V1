<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-3xl font-extrabold text-slate-100 tracking-tight mb-8">Your Shopping Cart</h1>

    @if(!$cart || $cart->lines->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-20 bg-slate-900/20 rounded-2xl border border-dashed border-slate-800">
            <svg class="w-16 h-16 text-slate-700 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h2 class="text-xl font-bold text-slate-300">Your cart is empty</h2>
            <p class="text-sm text-slate-500 mt-2 max-w-sm mx-auto">Fill it with premium products from our catalog to start your checkout process.</p>
            <div class="mt-8">
                <a href="/" wire:navigate class="px-6 py-3 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-300 hover:to-orange-400 text-slate-950 font-bold rounded-lg shadow-lg transition">
                    Continue Shopping
                </a>
            </div>
        </div>
    @else
        <!-- Cart Table and Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
            
            <!-- Cart Items List -->
            <div class="lg:col-span-2 space-y-6">
                @foreach($cart->lines as $line)
                    @php
                        $variant = $line->purchasable;
                        $product = $variant->product;
                    @endphp
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-6 bg-slate-900/50 border border-slate-900 rounded-xl hover:border-slate-800 transition duration-300 gap-6">
                        <!-- Left section: Thumbnail & Name -->
                        <div class="flex items-center space-x-4 flex-grow">
                            <div class="w-20 h-20 rounded-lg overflow-hidden bg-slate-950 flex-shrink-0 flex items-center justify-center border border-slate-850">
                                @if($product->thumbnail)
                                    <img src="{{ $product->thumbnail->getUrl('small') ?? $product->thumbnail->getUrl() }}" alt="{{ $product->attr('name') }}" class="object-cover w-full h-full">
                                @else
                                    <svg class="w-8 h-8 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-200 hover:text-amber-400 transition">
                                    <a href="/products/{{ $product->urls->first()?->slug }}" wire:navigate>
                                        {{ $product->attr('name') }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider">SKU: {{ $variant->sku }}</p>
                                @if($variant->getOptions()->isNotEmpty())
                                    <p class="text-xs text-slate-400 mt-1">
                                        @foreach($variant->getOptions() as $option)
                                            <span class="bg-slate-900 border border-slate-800 text-[10px] px-2 py-0.5 rounded text-slate-400 mr-1.5 uppercase font-medium tracking-wider">
                                                {{ $option }}
                                            </span>
                                        @endforeach
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Right section: Price, Quantity & Remove -->
                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-8 sm:gap-12 border-t border-slate-800/50 sm:border-0 pt-4 sm:pt-0">
                            <!-- Price & Subtotal -->
                            <div class="text-left sm:text-right">
                                <span class="block text-xs text-slate-500 font-medium">Price</span>
                                <span class="text-sm font-bold text-slate-200 block">{{ $line->subTotal->formatted }}</span>
                            </div>

                            <!-- Quantity Selector -->
                            <div>
                                <span class="block text-xs text-slate-500 font-medium mb-1.5 text-center sm:text-left">Qty</span>
                                <div class="flex items-center border border-slate-800 rounded-lg bg-slate-900 h-10 px-2 justify-center gap-2">
                                    <button type="button" class="text-slate-400 hover:text-amber-400 transition font-bold" wire:click="updateQuantity({{ $line->id }}, {{ $line->quantity - 1 }})">
                                        &minus;
                                    </button>
                                    <span class="w-8 text-center text-slate-200 font-bold text-sm select-none">{{ $line->quantity }}</span>
                                    <button type="button" class="text-slate-400 hover:text-amber-400 transition font-bold" wire:click="updateQuantity({{ $line->id }}, {{ $line->quantity + 1 }})">
                                        &plus;
                                    </button>
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <button type="button" class="p-2 text-slate-500 hover:text-red-400 transition mt-4 sm:mt-0" wire:click="removeLine({{ $line->id }})" title="Remove item">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Checkout Summary Card -->
            <div class="bg-slate-900/40 border border-slate-900 rounded-xl p-8 space-y-6">
                <h2 class="text-lg font-bold text-slate-100">Order Summary</h2>

                <div class="space-y-4 border-b border-slate-900 pb-6 text-sm">
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-200">{{ $cart->subTotal->formatted }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Discount</span>
                        <span class="font-semibold text-slate-200">-</span>
                    </div>
                    <div class="flex justify-between text-slate-400 text-xs">
                        <span>Taxes & Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>
                </div>

                <div class="flex justify-between items-center text-lg font-bold">
                    <span class="text-slate-200">Total</span>
                    <span class="text-amber-400">{{ $cart->total->formatted }}</span>
                </div>

                <div class="pt-4">
                    <a href="/checkout" wire:navigate class="block w-full py-4 text-center bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-300 hover:to-orange-400 text-slate-950 font-bold rounded-lg shadow-lg hover:shadow-amber-500/20 hover:scale-[1.01] transition duration-300">
                        Proceed to Checkout
                    </a>
                    <a href="/" wire:navigate class="block text-center text-xs font-semibold text-slate-400 hover:text-amber-400 transition mt-4">
                        &larr; Continue Shopping
                    </a>
                </div>
            </div>

        </div>
    @endif
</div>
