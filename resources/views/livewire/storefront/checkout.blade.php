<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    @if($orderCompleted && $completedOrder)
        <!-- Order Success State -->
        <div class="max-w-3xl mx-auto text-center py-12 space-y-8 animate-fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <div class="space-y-3">
                <h1 class="text-4xl font-extrabold text-slate-100 tracking-tight">Order Placed Successfully!</h1>
                <p class="text-slate-400 max-w-lg mx-auto leading-relaxed">
                    Thank you for your purchase. Your order has been registered and is currently being processed.
                </p>
                <div class="inline-block bg-slate-900 border border-slate-800 px-4 py-2 rounded-lg font-mono text-sm text-amber-400">
                    Order Reference: #{{ $completedOrder->reference }}
                </div>
            </div>

            <!-- Order Details Card -->
            <div class="bg-slate-900/50 border border-slate-900 rounded-xl p-8 text-left space-y-6">
                <h3 class="text-lg font-bold text-slate-200 border-b border-slate-850 pb-4">Order Details</h3>
                
                <!-- Products -->
                <div class="space-y-4">
                    @foreach($completedOrder->lines as $line)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-3">
                                <span class="text-slate-400">{{ $line->quantity }}x</span>
                                <span class="font-medium text-slate-200">{{ $line->description }}</span>
                            </div>
                            <span class="font-semibold text-slate-200">{{ $line->sub_total->formatted }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="border-t border-slate-850 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-slate-400">
                        <span>Shipping</span>
                        <span class="font-medium text-slate-200">{{ $completedOrder->shipping_total->formatted }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Tax</span>
                        <span class="font-medium text-slate-200">{{ $completedOrder->tax_total->formatted }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-slate-800 pt-4">
                        <span class="text-slate-200">Total Paid</span>
                        <span class="text-amber-400">{{ $completedOrder->total->formatted }}</span>
                    </div>
                </div>

                <!-- Addresses -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-850 pt-6 text-xs text-slate-400">
                    <div>
                        <h4 class="font-bold text-slate-300 uppercase tracking-wider mb-2">Shipping Address</h4>
                        <p class="font-semibold text-slate-200">{{ $completedOrder->shippingAddress?->first_name }} {{ $completedOrder->shippingAddress?->last_name }}</p>
                        <p>{{ $completedOrder->shippingAddress?->line_one }}</p>
                        @if($completedOrder->shippingAddress?->line_two)
                            <p>{{ $completedOrder->shippingAddress?->line_two }}</p>
                        @endif
                        <p>{{ $completedOrder->shippingAddress?->city }}, {{ $completedOrder->shippingAddress?->state }} {{ $completedOrder->shippingAddress?->postcode }}</p>
                        <p>{{ $completedOrder->shippingAddress?->country?->name }}</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-300 uppercase tracking-wider mb-2">Payment Method</h4>
                        <p class="font-semibold text-slate-200 uppercase">{{ str_replace('-', ' ', $completedOrder->transactions->first()?->card_type ?? 'Cash') }}</p>
                        <p class="mt-4 font-bold text-slate-300 uppercase tracking-wider mb-2">Billing Address</p>
                        <p class="font-semibold text-slate-200">{{ $completedOrder->billingAddress?->first_name }} {{ $completedOrder->billingAddress?->last_name }}</p>
                        <p>{{ $completedOrder->billingAddress?->line_one }}</p>
                        <p>{{ $completedOrder->billingAddress?->city }}, {{ $completedOrder->billingAddress?->state }} {{ $completedOrder->billingAddress?->postcode }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <a href="/" wire:navigate class="inline-block px-8 py-3.5 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-300 hover:to-orange-400 text-slate-950 font-bold rounded-lg shadow-lg transition">
                    Continue Shopping
                </a>
            </div>
        </div>
    @else
        <!-- Active Checkout Flow -->
        <h1 class="text-3xl font-extrabold text-slate-100 tracking-tight mb-8">Secure Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
            
            <!-- Left: Checkout Forms -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Shipping Address Section -->
                <div class="bg-slate-900/30 border border-slate-900 rounded-xl p-8 space-y-6">
                    <h2 class="text-lg font-bold text-slate-100 flex items-center space-x-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-950 text-xs font-bold">1</span>
                        <span>Shipping Information</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">First Name</label>
                            <input type="text" wire:model.blur="shippingAddress.first_name" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @error('shippingAddress.first_name') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Last Name</label>
                            <input type="text" wire:model.blur="shippingAddress.last_name" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @error('shippingAddress.last_name') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Company Name (Optional)</label>
                            <input type="text" wire:model.blur="shippingAddress.company_name" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Address Line 1</label>
                            <input type="text" wire:model.blur="shippingAddress.line_one" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @error('shippingAddress.line_one') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Address Line 2 (Optional)</label>
                            <input type="text" wire:model.blur="shippingAddress.line_two" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">City</label>
                            <input type="text" wire:model.blur="shippingAddress.city" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @error('shippingAddress.city') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">State / Province</label>
                            <input type="text" wire:model.blur="shippingAddress.state" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @error('shippingAddress.state') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Postcode / ZIP</label>
                            <input type="text" wire:model.blur="shippingAddress.postcode" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @error('shippingAddress.postcode') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Country</label>
                            <select wire:model.live="shippingAddress.country_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('shippingAddress.country_id') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Contact Email</label>
                            <input type="email" wire:model.blur="shippingAddress.contact_email" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @error('shippingAddress.contact_email') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Contact Phone</label>
                            <input type="text" wire:model.blur="shippingAddress.contact_phone" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @error('shippingAddress.contact_phone') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Billing Address Selection -->
                <div class="bg-slate-900/30 border border-slate-900 rounded-xl p-8 space-y-6">
                    <h2 class="text-lg font-bold text-slate-100 flex items-center space-x-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-950 text-xs font-bold">2</span>
                        <span>Billing Information</span>
                    </h2>

                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" wire:model.live="sameAsShipping" class="rounded bg-slate-950 border-slate-800 text-amber-500 focus:ring-0">
                        <span class="text-sm font-semibold text-slate-300">Billing address is the same as shipping</span>
                    </label>

                    @if(!$sameAsShipping)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-900 animate-fade-in">
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">First Name</label>
                                <input type="text" wire:model.blur="billingAddress.first_name" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                @error('billingAddress.first_name') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Last Name</label>
                                <input type="text" wire:model.blur="billingAddress.last_name" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                @error('billingAddress.last_name') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Address Line 1</label>
                                <input type="text" wire:model.blur="billingAddress.line_one" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                @error('billingAddress.line_one') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Address Line 2 (Optional)</label>
                                <input type="text" wire:model.blur="billingAddress.line_two" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">City</label>
                                <input type="text" wire:model.blur="billingAddress.city" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                @error('billingAddress.city') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">State / Province</label>
                                <input type="text" wire:model.blur="billingAddress.state" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                @error('billingAddress.state') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Postcode / ZIP</label>
                                <input type="text" wire:model.blur="billingAddress.postcode" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                @error('billingAddress.postcode') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Country</label>
                                <select wire:model.live="billingAddress.country_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm px-4 py-3 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('billingAddress.country_id') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Shipping Method Section -->
                <div class="bg-slate-900/30 border border-slate-900 rounded-xl p-8 space-y-6">
                    <h2 class="text-lg font-bold text-slate-100 flex items-center space-x-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-950 text-xs font-bold">3</span>
                        <span>Shipping Options</span>
                    </h2>

                    @if($shippingOptions->isEmpty())
                        <div class="p-6 bg-slate-950 border border-slate-900 rounded-lg text-center text-sm text-slate-500">
                            Please fill in your shipping details (First Name, Address, City, Postcode, and Country) above to see available shipping methods.
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($shippingOptions as $option)
                                <label class="flex items-center justify-between p-5 bg-slate-950 border {{ $shippingOptionHandle === $option->identifier ? 'border-amber-400' : 'border-slate-850' }} hover:border-amber-400/50 rounded-xl cursor-pointer transition">
                                    <div class="flex items-center space-x-4">
                                        <input type="radio" wire:model.live="shippingOptionHandle" value="{{ $option->identifier }}" class="bg-slate-950 border-slate-800 text-amber-500 focus:ring-0">
                                        <div>
                                            <span class="block text-sm font-bold text-slate-200">{{ $option->name }}</span>
                                            <span class="block text-xs text-slate-500 mt-1">{{ $option->description }}</span>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-100">{{ $option->price->formatted }}</span>
                                </label>
                            @endforeach
                            @error('shippingOptionHandle') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <!-- Payment Details Section -->
                <div class="bg-slate-900/30 border border-slate-900 rounded-xl p-8 space-y-6">
                    <h2 class="text-lg font-bold text-slate-100 flex items-center space-x-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-slate-950 text-xs font-bold">4</span>
                        <span>Payment Details</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="flex items-center space-x-4 p-5 bg-slate-950 border {{ $paymentMethod === 'cod' ? 'border-amber-400' : 'border-slate-850' }} hover:border-amber-400/50 rounded-xl cursor-pointer transition">
                            <input type="radio" wire:model.live="paymentMethod" value="cod" class="bg-slate-950 border-slate-800 text-amber-500 focus:ring-0">
                            <div>
                                <span class="block text-sm font-bold text-slate-200">Cash on Delivery</span>
                                <span class="block text-xs text-slate-500 mt-1">Pay when package arrives.</span>
                            </div>
                        </label>
                        <label class="flex items-center space-x-4 p-5 bg-slate-950 border {{ $paymentMethod === 'card' ? 'border-amber-400' : 'border-slate-850' }} hover:border-amber-400/50 rounded-xl cursor-pointer transition">
                            <input type="radio" wire:model.live="paymentMethod" value="card" class="bg-slate-950 border-slate-800 text-amber-500 focus:ring-0">
                            <div>
                                <span class="block text-sm font-bold text-slate-200">Mock Card Payment</span>
                                <span class="block text-xs text-slate-500 mt-1">Simulate instant secure checkout.</span>
                            </div>
                        </label>
                        @error('paymentMethod') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            <!-- Right: Order Summary Sidebar -->
            <div class="bg-slate-900/40 border border-slate-900 rounded-xl p-8 space-y-6 lg:sticky lg:top-24">
                <h2 class="text-lg font-bold text-slate-100">Order Summary</h2>

                <!-- Lines -->
                <div class="space-y-4 max-h-60 overflow-y-auto pr-2 border-b border-slate-900 pb-6">
                    @foreach($cart->lines as $line)
                        @php
                            $variant = $line->purchasable;
                            $product = $variant->product;
                        @endphp
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-3 truncate">
                                <span class="text-slate-500 font-bold">{{ $line->quantity }}x</span>
                                <span class="text-slate-350 truncate hover:text-amber-400 transition">
                                    {{ $product->attr('name') }}
                                </span>
                            </div>
                            <span class="font-semibold text-slate-200 flex-shrink-0">{{ $line->subTotal->formatted }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Pricing Breakdown -->
                <div class="space-y-4 border-b border-slate-900 pb-6 text-sm">
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-200">{{ $cart->subTotal->formatted }}</span>
                    </div>
                    @php
                        $selectedOption = $this->shippingOptions->first(fn($opt) => $opt->identifier === $this->shippingOptionHandle);
                    @endphp
                    <div class="flex justify-between text-slate-400">
                        <span>Shipping</span>
                        <span class="font-semibold text-slate-200">
                            {{ $selectedOption ? $selectedOption->price->formatted : 'Select shipping' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Taxes</span>
                        <span class="font-semibold text-slate-200">{{ $cart->taxTotal->formatted }}</span>
                    </div>
                </div>

                <!-- Total -->
                @php
                    $subTotalVal = $cart->subTotal->value;
                    $taxVal = $cart->taxTotal->value;
                    $shippingVal = $selectedOption ? $selectedOption->price->value : 0;
                    $grandTotalValue = $subTotalVal + $taxVal + $shippingVal;
                    // Format grand total nicely using Lunar Price object
                    $priceObj = new \Lunar\DataTypes\Price($grandTotalValue, $cart->currency, 1);
                    $formattedGrandTotal = $priceObj->formatted();
                @endphp
                <div class="flex justify-between items-center text-lg font-bold">
                    <span class="text-slate-200">Total</span>
                    <span class="text-amber-400 font-extrabold">{{ $formattedGrandTotal }}</span>
                </div>

                <!-- CTA -->
                <div class="pt-4">
                    <button type="button" wire:click="placeOrder" wire:loading.attr="disabled" class="w-full py-4 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-300 hover:to-orange-400 text-slate-950 font-bold rounded-lg shadow-lg hover:shadow-amber-500/20 hover:scale-[1.01] transition duration-300 flex items-center justify-center space-x-2">
                        <span wire:loading.remove wire:target="placeOrder">Place Order</span>
                        <span wire:loading wire:target="placeOrder" class="inline-flex items-center">
                            <!-- Loading spinner SVG -->
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing Order...
                        </span>
                    </button>
                    <p class="text-[10px] text-slate-500 text-center mt-3 leading-relaxed">
                        By placing your order, you agree to our Terms of Service & Privacy Policy.
                    </p>
                </div>

            </div>

        </div>
    @endif
</div>
