<div x-data="{ 
         open: @entangle('isOpen'), 
         showModal: false 
     }" 
     x-show="showModal" 
     x-init="
         window.addEventListener('open-buy-now', () => { 
             showModal = true; 
         });
         $watch('open', value => {
             if (!value) {
                 showModal = false;
             }
         });
     "
     @keydown.escape.window="$wire.closeModal(); showModal = false"
     class="fixed inset-0 z-50 overflow-y-auto" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
      
    <!-- Backdrop with premium blur -->
    <div class="fixed inset-0 bg-black/40 backdrop-blur-md transition-opacity duration-300" 
         x-show="showModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$wire.closeModal(); showModal = false"></div>
 
    <!-- Modal Outer Wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4 sm:p-6 md:p-10">
        
        <!-- Modal Content Box -->
        <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-150 transform transition-all duration-300 flex flex-col lg:flex-row"
             x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
 
            <!-- Close Button -->
            <button type="button" 
                    @click="$wire.closeModal(); showModal = false" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition focus:outline-none z-10 p-2"
                    aria-label="Close modal">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
 
            @if($ready && $cart)
                <!-- Left: Form Area (Checkout details) -->
                <div class="flex-grow p-6 sm:p-8 md:p-10 lg:w-3/5 space-y-6 overflow-y-auto max-h-[85vh] lg:max-h-[none]">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Instant Checkout</span>
                        <h2 class="text-2xl font-extrabold text-[#111111] tracking-tight mt-1">Shipping Information</h2>
                    </div>
 
                    <form wire:submit.prevent="placeOrder" class="space-y-4">
                        <!-- Full Name -->
                        <div>
                            <label for="buy-now-name" class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">Full Name</label>
                            <input type="text" id="buy-now-name" wire:model.live.debounce.500ms="shippingAddress.first_name" placeholder="e.g. Waleed Ahmed" class="w-full bg-white border border-gray-200 rounded-md text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                            @error('shippingAddress.first_name') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
 
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Phone Number -->
                            <div>
                                <label for="buy-now-phone" class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">Phone Number</label>
                                <input type="text" id="buy-now-phone" wire:model.live.debounce.500ms="shippingAddress.contact_phone" placeholder="e.g. 03001234567" class="w-full bg-white border border-gray-200 rounded-md text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                                @error('shippingAddress.contact_phone') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
 
                            <!-- Email Address -->
                            <div>
                                <label for="buy-now-email" class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">Email Address</label>
                                <input type="email" id="buy-now-email" wire:model.live.debounce.500ms="shippingAddress.contact_email" placeholder="e.g. name@domain.com" class="w-full bg-white border border-gray-200 rounded-md text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                                @error('shippingAddress.contact_email') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
 
                        <!-- Shipping Address -->
                        <div>
                            <label for="buy-now-address" class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">Delivery Address</label>
                            <input type="text" id="buy-now-address" wire:model.live.debounce.500ms="shippingAddress.line_one" placeholder="House number, street address, area name" class="w-full bg-white border border-gray-200 rounded-md text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                            @error('shippingAddress.line_one') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
 
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <!-- City -->
                            <div class="col-span-2 sm:col-span-1">
                                <label for="buy-now-city" class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">City</label>
                                <input type="text" id="buy-now-city" wire:model.live.debounce.500ms="shippingAddress.city" placeholder="e.g. Lahore" class="w-full bg-white border border-gray-200 rounded-md text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                                @error('shippingAddress.city') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
 
                            <!-- Province -->
                            <div>
                                <label for="buy-now-state" class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">Province</label>
                                <input type="text" id="buy-now-state" wire:model.live.debounce.500ms="shippingAddress.state" placeholder="e.g. Punjab" class="w-full bg-white border border-gray-200 rounded-md text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                                @error('shippingAddress.state') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
 
                            <!-- Postcode -->
                            <div>
                                <label for="buy-now-postcode" class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">Postal Code</label>
                                <input type="text" id="buy-now-postcode" wire:model.live.debounce.500ms="shippingAddress.postcode" placeholder="e.g. 54000" class="w-full bg-white border border-gray-200 rounded-md text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                                @error('shippingAddress.postcode') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
 
                        <!-- Country Selector (Hidden or set default) -->
                        <div class="hidden">
                            <label for="buy-now-country" class="block text-[10px] font-bold text-gray-450 uppercase tracking-wider mb-1.5">Country</label>
                            <select id="buy-now-country" wire:model.live="shippingAddress.country_id" class="w-full bg-white border border-gray-200 rounded-md text-sm px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-[#111111]">
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
 
                        <!-- Payment Method Note -->
                        <div class="p-3 bg-gray-50 border border-gray-150 rounded-lg flex items-center gap-3 mt-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="text-xs font-semibold text-gray-650">Payment Method: Cash on Delivery (COD)</span>
                        </div>
 
                        <!-- Actions -->
                        <div class="pt-4 flex items-center justify-between gap-4">
                            <button type="button" 
                                    @click="$wire.closeModal(); showModal = false" 
                                    class="px-5 py-3 border border-gray-200 hover:bg-gray-50 text-gray-600 font-bold rounded-md text-xs tracking-wider uppercase transition">
                                Cancel
                            </button>
                            
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="flex-1 py-3.5 bg-[#111111] hover:bg-[#222222] text-white font-extrabold rounded-md text-xs tracking-wider uppercase transition flex items-center justify-center gap-2 shadow-sm disabled:opacity-50">
                                
                                <!-- Loading Spinner -->
                                <span wire:loading wire:target="placeOrder" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                
                                <span>Place COD Order</span>
                            </button>
                        </div>
                    </form>
                </div>
 
                <!-- Right: Order Summary Sidebar -->
                <div class="bg-gray-50/70 lg:w-2/5 p-6 sm:p-8 md:p-10 border-t lg:border-t-0 lg:border-l border-gray-150 flex flex-col justify-between space-y-6 overflow-y-auto max-h-[85vh] lg:max-h-[none]">
                    <div class="space-y-6">
                        <h3 class="text-sm font-extrabold uppercase tracking-widest text-[#111111] border-b border-gray-200 pb-3">Order Summary</h3>
 
                        <!-- Cart Line Items -->
                        <div class="space-y-4">
                            @foreach($cart->lines as $line)
                                @php
                                    $variant = $line->purchasable;
                                    $product = $variant?->product;
                                    $sku = $variant?->sku;
                                    
                                    // Fetch Spatie media items with primary fallback
                                    $media = $product?->getMedia('images')->first(fn ($m) => $m->getCustomProperty('primary') === true) 
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
                                        default => 'Clinical Formulation'
                                    };
                                @endphp
                                <div class="flex items-center space-x-4">
                                    <!-- Image Thumbnail -->
                                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-white flex-shrink-0 flex items-center justify-center border border-gray-150 p-1.5 shadow-sm">
                                        <img src="{{ $productImage }}" alt="{{ $product?->attr('name') ?? 'Product' }}" class="object-contain w-full h-full">
                                    </div>
                                    <!-- Details -->
                                    <div class="flex-grow space-y-0.5">
                                        <span class="text-[9px] uppercase font-bold text-gray-400 block tracking-wide">{{ $benefit }}</span>
                                        <h4 class="text-xs font-extrabold text-[#111111] line-clamp-1 leading-snug">{{ $product?->attr('name') ?? 'Product' }}</h4>
                                        <div class="flex items-center justify-between">
                                            <span class="text-[9px] text-gray-400 uppercase tracking-widest font-semibold">Qty: {{ $line->quantity }}</span>
                                            <span class="text-xs font-bold text-[#111111]">{{ $line->subTotal->formatted }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
 
                    <!-- Totals Block -->
                    <div class="space-y-4 border-t border-gray-200 pt-6">
                        <div class="flex justify-between text-xs text-gray-650">
                            <span>Subtotal</span>
                            <span class="font-bold text-[#111111]">{{ $cart->subTotal->formatted }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-650">
                            <span>Shipping</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded px-1.5 py-0.5 text-[10px] uppercase tracking-wider">Free Delivery</span>
                        </div>
                        <div class="flex justify-between items-center text-base font-extrabold pt-2 border-t border-dashed border-gray-200">
                            <span class="text-[#111111]">Total Amount</span>
                            <span class="text-[#111111] text-lg">{{ $cart->total->formatted }}</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Premium Loading Skeleton -->
                <div class="flex-grow p-6 sm:p-8 md:p-10 lg:w-3/5 space-y-6 animate-pulse">
                    <div>
                        <div class="h-3 w-20 bg-gray-200 rounded mb-2"></div>
                        <div class="h-8 w-48 bg-gray-200 rounded"></div>
                    </div>
                    <div class="space-y-4">
                        <!-- Full Name -->
                        <div class="space-y-2">
                            <div class="h-3 w-16 bg-gray-200 rounded"></div>
                            <div class="h-10 w-full bg-gray-100 rounded-md"></div>
                        </div>
                        <!-- Phone & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div class="h-3 w-20 bg-gray-200 rounded"></div>
                                <div class="h-10 w-full bg-gray-100 rounded-md"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 w-24 bg-gray-200 rounded"></div>
                                <div class="h-10 w-full bg-gray-100 rounded-md"></div>
                            </div>
                        </div>
                        <!-- Address -->
                        <div class="space-y-2">
                            <div class="h-3 w-24 bg-gray-200 rounded"></div>
                            <div class="h-10 w-full bg-gray-100 rounded-md"></div>
                        </div>
                        <!-- City, State, Postcode -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div class="col-span-2 sm:col-span-1 space-y-2">
                                <div class="h-3 w-12 bg-gray-200 rounded"></div>
                                <div class="h-10 w-full bg-gray-100 rounded-md"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 w-16 bg-gray-200 rounded"></div>
                                <div class="h-10 w-full bg-gray-100 rounded-md"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 w-20 bg-gray-200 rounded"></div>
                                <div class="h-10 w-full bg-gray-100 rounded-md"></div>
                            </div>
                        </div>
                        <!-- Actions -->
                        <div class="pt-4 flex items-center justify-between gap-4">
                            <div class="h-11 w-24 bg-gray-200 rounded-md"></div>
                            <div class="h-11 flex-1 bg-gray-200 rounded-md"></div>
                        </div>
                    </div>
                </div>
 
                <div class="bg-gray-50/70 lg:w-2/5 p-6 sm:p-8 md:p-10 border-t lg:border-t-0 lg:border-l border-gray-150 flex flex-col justify-between space-y-6 animate-pulse">
                    <div class="space-y-6">
                        <div class="h-4 w-32 bg-gray-200 rounded pb-3"></div>
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gray-200 rounded-lg"></div>
                            <div class="flex-grow space-y-2">
                                <div class="h-3 w-16 bg-gray-200 rounded"></div>
                                <div class="h-4 w-32 bg-gray-200 rounded"></div>
                                <div class="h-3 w-12 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4 border-t border-gray-200 pt-6">
                        <div class="flex justify-between">
                            <div class="h-3 w-12 bg-gray-200 rounded"></div>
                            <div class="h-3 w-16 bg-gray-200 rounded"></div>
                        </div>
                        <div class="flex justify-between">
                            <div class="h-3 w-16 bg-gray-200 rounded"></div>
                            <div class="h-3 w-20 bg-gray-200 rounded"></div>
                        </div>
                        <div class="flex justify-between items-center border-t border-dashed border-gray-200 pt-2">
                            <div class="h-4 w-20 bg-gray-200 rounded"></div>
                            <div class="h-5 w-24 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>
            @endif
 
        </div>
    </div>
</div>
