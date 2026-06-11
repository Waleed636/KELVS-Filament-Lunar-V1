<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-white text-[#111111]">
    
    <!-- Active Checkout Flow -->
    <div class="flex items-center space-x-2.5 mb-10 border-b border-gray-100 pb-6">
        <span class="text-xs uppercase font-extrabold tracking-[0.2em] text-gray-400">Secure Gateway</span>
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
        <h1 class="text-2xl font-bold text-[#111111] tracking-tight">Checkout</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <!-- Left: Checkout Forms -->
        <div class="lg:col-span-8 space-y-10">
            
            <!-- 1. Shipping Address Section -->
            <div class="bg-[#fbfbfa] border border-gray-150 rounded-xl p-8 space-y-6">
                <h2 class="text-base font-bold text-[#111111] flex items-center space-x-3">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#111111] text-white text-xs font-bold font-mono">1</span>
                    <span class="uppercase tracking-wider">Shipping Information</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Full Name</label>
                        <input type="text" placeholder="e.g. Waleed Ahmed" wire:model.blur="shippingAddress.first_name" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                        @error('shippingAddress.first_name') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Shipping Address</label>
                        <input type="text" placeholder="House number, street name, area..." wire:model.blur="shippingAddress.line_one" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                        @error('shippingAddress.line_one') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">City</label>
                        <input type="text" placeholder="e.g. Lahore" wire:model.blur="shippingAddress.city" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                        @error('shippingAddress.city') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">State / Province (Optional)</label>
                        <input type="text" placeholder="e.g. Punjab" wire:model.blur="shippingAddress.state" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                        @error('shippingAddress.state') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Postcode / ZIP</label>
                        <input type="text" placeholder="e.g. 54000" wire:model.blur="shippingAddress.postcode" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                        @error('shippingAddress.postcode') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Country</label>
                        <input type="text" readonly value="Pakistan" class="w-full bg-gray-50 border border-gray-200 rounded-[6px] text-gray-500 text-sm px-4 py-3 cursor-not-allowed outline-none">
                    </div>
                    <div class="sm:col-span-2 pt-2 border-t border-gray-150">
                        <span class="block text-xs font-semibold text-gray-500 mb-4">Contact Details (Please provide at least one contact method)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                                <input type="email" placeholder="e.g. name@example.com" wire:model.blur="shippingAddress.contact_email" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                                @error('shippingAddress.contact_email') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Phone Number</label>
                                <input type="text" placeholder="e.g. 03001234567" wire:model.blur="shippingAddress.contact_phone" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                                @error('shippingAddress.contact_phone') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Billing Address Selection -->
            <div class="bg-[#fbfbfa] border border-gray-150 rounded-xl p-8 space-y-6">
                <h2 class="text-base font-bold text-[#111111] flex items-center space-x-3">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#111111] text-white text-xs font-bold font-mono">2</span>
                    <span class="uppercase tracking-wider">Billing Information</span>
                </h2>

                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" wire:model.live="sameAsShipping" class="rounded border-gray-300 text-[#111111] focus:ring-[#111111] focus:ring-0">
                    <span class="text-sm font-semibold text-gray-650">Billing address is the same as shipping</span>
                </label>

                @if(!$sameAsShipping)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-gray-150 animate-fade-in">
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Full Name</label>
                            <input type="text" wire:model.blur="billingAddress.first_name" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                            @error('billingAddress.first_name') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Billing Address</label>
                            <input type="text" wire:model.blur="billingAddress.line_one" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                            @error('billingAddress.line_one') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">City</label>
                            <input type="text" wire:model.blur="billingAddress.city" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                            @error('billingAddress.city') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">State / Province (Optional)</label>
                            <input type="text" wire:model.blur="billingAddress.state" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                            @error('billingAddress.state') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Postcode / ZIP</label>
                            <input type="text" wire:model.blur="billingAddress.postcode" class="w-full bg-white border border-gray-200 rounded-[6px] text-[#111111] text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-[#111111] focus:border-[#111111] transition duration-200">
                            @error('billingAddress.postcode') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">Country</label>
                            <input type="text" readonly value="Pakistan" class="w-full bg-gray-50 border border-gray-200 rounded-[6px] text-gray-500 text-sm px-4 py-3 cursor-not-allowed outline-none">
                        </div>
                    </div>
                @endif
            </div>

            <!-- 3. Shipping Method Section -->
            <div class="bg-[#fbfbfa] border border-gray-150 rounded-xl p-8 space-y-6">
                <h2 class="text-base font-bold text-[#111111] flex items-center space-x-3">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#111111] text-white text-xs font-bold font-mono">3</span>
                    <span class="uppercase tracking-wider">Shipping Options</span>
                </h2>

                @if($shippingOptions->isEmpty())
                    <div class="p-6 bg-white border border-gray-150 rounded-lg text-center text-sm text-gray-400">
                        Please fill in your shipping details (Full Name, Address, City, and Postcode) above to see available shipping methods.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($shippingOptions as $option)
                            <label class="flex items-center justify-between p-5 bg-white border {{ $shippingOptionHandle === $option->identifier ? 'border-[#111111]' : 'border-gray-150' }} hover:border-[#111111]/65 rounded-xl cursor-pointer transition">
                                <div class="flex items-center space-x-4">
                                    <input type="radio" wire:model.live="shippingOptionHandle" value="{{ $option->identifier }}" class="border-gray-300 text-[#111111] focus:ring-0">
                                    <div>
                                        <span class="block text-sm font-bold text-[#111111]">{{ $option->name }}</span>
                                        <span class="block text-xs text-gray-400 mt-1">{{ $option->description }}</span>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-[#111111]">{{ $option->price->formatted }}</span>
                            </label>
                        @endforeach
                        @error('shippingOptionHandle') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            <!-- 4. Payment Details Section -->
            <div class="bg-[#fbfbfa] border border-gray-150 rounded-xl p-8 space-y-6">
                <h2 class="text-base font-bold text-[#111111] flex items-center space-x-3">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-[#111111] text-white text-xs font-bold font-mono">4</span>
                    <span class="uppercase tracking-wider">Payment Details</span>
                </h2>

                <div class="p-5 bg-white border border-[#111111] rounded-xl flex items-center space-x-4">
                    <div class="p-2 rounded-full bg-[#fbfbfa] text-[#111111]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-[#111111]">Cash on Delivery (COD)</span>
                        <span class="block text-xs text-gray-400 mt-1">Pay in cash when your order is delivered to your address.</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Order Summary Sidebar -->
        <div class="lg:col-span-4 bg-[#fbfbfa] border border-gray-150 rounded-xl p-8 space-y-6 lg:sticky lg:top-24">
            <h2 class="text-base font-bold text-[#111111] border-b border-gray-150 pb-4 uppercase tracking-wider">Order Summary</h2>

            <!-- Lines -->
            <div class="space-y-4 max-h-60 overflow-y-auto pr-2 border-b border-gray-150 pb-6">
                @foreach($cart->lines as $line)
                    @php
                        $variant = $line->purchasable;
                        $product = $variant->product;
                    @endphp
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-3 truncate">
                            <span class="text-gray-400 font-bold">{{ $line->quantity }}x</span>
                            <span class="text-[#111111] font-semibold truncate">
                                {{ $product->attr('name') }}
                            </span>
                        </div>
                        <span class="font-bold text-[#111111] flex-shrink-0">{{ $line->subTotal->formatted }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Pricing Breakdown -->
            <div class="space-y-3 border-b border-gray-150 pb-6 text-sm text-gray-500">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="font-semibold text-[#111111]">{{ $cart->subTotal->formatted }}</span>
                </div>
                @php
                    $selectedOption = $this->shippingOptions->first(fn($opt) => $opt->identifier === $this->shippingOptionHandle);
                @endphp
                <div class="flex justify-between">
                    <span>Shipping</span>
                    <span class="font-semibold text-[#111111]">
                        {{ $selectedOption ? $selectedOption->price->formatted : 'Select shipping' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span>Taxes</span>
                    <span class="font-semibold text-[#111111]">{{ $cart->taxTotal->formatted }}</span>
                </div>
            </div>

            <!-- Total -->
            @php
                $subTotalVal = $cart->subTotal->value;
                $taxVal = $cart->taxTotal->value;
                $shippingVal = $selectedOption ? $selectedOption->price->value : 0;
                $grandTotalValue = $subTotalVal + $taxVal + $shippingVal;
                $priceObj = new \Lunar\DataTypes\Price($grandTotalValue, $cart->currency, 1);
                $formattedGrandTotal = $priceObj->formatted();
            @endphp
            <div class="flex justify-between items-center text-base font-bold">
                <span class="text-[#111111] uppercase tracking-wide">Total</span>
                <span class="text-[#111111] font-extrabold text-lg">{{ $formattedGrandTotal }}</span>
            </div>

            <!-- CTA -->
            <div class="pt-4">
                <button type="button" wire:click="placeOrder" wire:loading.attr="disabled" class="w-full py-4 bg-[#111111] hover:bg-[#222222] text-white font-bold text-xs uppercase tracking-widest rounded-[6px] shadow-sm transition duration-300 flex items-center justify-center space-x-2">
                    <span wire:loading.remove wire:target="placeOrder">Place Order</span>
                    <span wire:loading wire:target="placeOrder" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
                <p class="text-[10px] text-gray-400 text-center mt-3 leading-relaxed">
                    By placing your order, you agree to our Terms & Privacy policy.
                </p>
            </div>

        </div>

    </div>
</div>
