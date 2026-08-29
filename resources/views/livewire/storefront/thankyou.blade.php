@php
    $rawOrderTotal = number_format($completedOrder->total->value / (10 ** ($completedOrder->total->currency->decimal_places ?? 0)), 2, '.', '');
@endphp
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20"
     data-pixel-page="purchase_complete"
     data-pixel-event="Purchase"
     data-pixel-transaction-id="{{ $completedOrder->reference }}"
     data-pixel-value="{{ $rawOrderTotal }}"
     data-pixel-currency="PKR">
    <div class="text-center space-y-6 max-w-2xl mx-auto mb-16">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-[#e8dcd2]/40 text-[#111111] border border-[#e8dcd2]">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <div class="space-y-3">
            <span class="text-[10px] uppercase font-extrabold tracking-widest text-gray-400 block">Thank you for your order</span>
            <h1 class="text-4xl font-bold text-[#111111] tracking-tight">Order Placed Successfully</h1>
            <p class="text-sm text-gray-500 leading-relaxed max-w-md mx-auto">
                We've received your request. Your order is registered and will be prepared shortly for shipment.
            </p>
            <div class="inline-block bg-[#fbfbfa] border border-gray-150 px-5 py-2.5 rounded-lg font-mono text-xs text-[#111111] font-bold" id="order-reference" data-pixel-transaction-id="{{ $completedOrder->reference }}">
                Order Reference: #{{ $completedOrder->reference }}
            </div>
        </div>
    </div>

    <!-- Order Summary Details -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <!-- Left Column: Items and Addresses -->
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white border border-gray-150 rounded-xl p-8 space-y-6">
                <h3 class="text-base font-bold text-[#111111] border-b border-gray-100 pb-4">Ordered Items</h3>
                
                <div class="divide-y divide-gray-100">
                    @foreach($completedOrder->lines as $line)
                        <div class="flex items-center justify-between py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center space-x-4">
                                <span class="text-xs font-bold text-gray-400">{{ $line->quantity }}x</span>
                                <div>
                                    <span class="text-sm font-bold text-[#111111]">{{ $line->description }}</span>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-[#111111]">{{ $line->sub_total->formatted }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Addresses Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Shipping Address -->
                <div class="bg-white border border-gray-150 rounded-xl p-8 space-y-4">
                    <h4 class="text-xs uppercase font-extrabold tracking-wider text-gray-400">Shipping Destination</h4>
                    <div class="text-sm space-y-1.5 text-gray-600">
                        <p class="font-bold text-[#111111]">{{ $completedOrder->shippingAddress?->first_name }} {{ $completedOrder->shippingAddress?->last_name }}</p>
                        <p>{{ $completedOrder->shippingAddress?->line_one }}</p>
                        @if($completedOrder->shippingAddress?->line_two)
                            <p>{{ $completedOrder->shippingAddress?->line_two }}</p>
                        @endif
                        <p>{{ $completedOrder->shippingAddress?->city }}, {{ $completedOrder->shippingAddress?->state }} {{ $completedOrder->shippingAddress?->postcode }}</p>
                        <p class="font-semibold text-[#111111]">{{ $completedOrder->shippingAddress?->country?->name }}</p>
                        <p class="pt-2 text-xs text-gray-400">Phone: {{ $completedOrder->shippingAddress?->contact_phone }}</p>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-white border border-gray-150 rounded-xl p-8 space-y-4">
                    <h4 class="text-xs uppercase font-extrabold tracking-wider text-gray-400">Payment & Billing</h4>
                    <div class="text-sm space-y-1.5 text-gray-600">
                        <p class="font-bold text-[#111111] uppercase">
                            {{ str_replace('-', ' ', $completedOrder->transactions->first()?->card_type ?? 'Cash on Delivery') }}
                        </p>
                        <p class="text-xs text-gray-400">Transaction Status: Success</p>
                        
                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billing Address</p>
                            <p class="font-semibold text-[#111111]">{{ $completedOrder->billingAddress?->first_name }} {{ $completedOrder->billingAddress?->last_name }}</p>
                            <p>{{ $completedOrder->billingAddress?->line_one }}</p>
                            <p>{{ $completedOrder->billingAddress?->city }}, {{ $completedOrder->billingAddress?->state }} {{ $completedOrder->billingAddress?->postcode }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Final Totals -->
        <div class="lg:col-span-4 bg-[#fbfbfa] border border-gray-150 rounded-xl p-8 space-y-6">
            <h3 class="text-base font-bold text-[#111111] border-b border-gray-150 pb-4">Total Amount</h3>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal</span>
                    <span class="font-semibold text-[#111111]">{{ $completedOrder->sub_total->formatted }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Shipping Fee</span>
                    <span class="font-semibold text-[#111111]">{{ $completedOrder->shipping_total->formatted }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Tax</span>
                    <span class="font-semibold text-[#111111]">{{ $completedOrder->tax_total->formatted }}</span>
                </div>
                
                <div class="border-t border-gray-200 pt-4 flex justify-between items-center text-base font-bold">
                    <span class="text-[#111111]">Grand Total</span>
                    <span class="text-[#111111] font-extrabold text-lg" id="order-total" data-pixel-value="{{ $rawOrderTotal }}" data-pixel-currency="PKR">{{ $completedOrder->total->formatted }}</span>
                </div>
            </div>

            <div class="pt-4">
                <a href="/shop" class="w-full py-3.5 bg-[#111111] hover:bg-[#222222] text-white text-xs font-bold rounded-[6px] tracking-wide uppercase transition duration-300 flex items-center justify-center">
                    Continue Shopping
                </a>
            </div>
        </div>

    @script
    <script>
        @if($purchaseEventData)
            (function() {
                var payload = @json($purchaseEventData);
                if (payload && payload.ecommerceData && payload.ecommerceData.transaction_id) {
                    var dedupKey = 'fired_purchase_' + payload.ecommerceData.transaction_id;
                    if (sessionStorage.getItem(dedupKey)) return;
                    sessionStorage.setItem(dedupKey, '1');
                }

                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ ecommerce: null });
                
                var pushPayload = {
                    event:    payload.eventName,
                    event_id: payload.eventId,
                    ecommerce: payload.ecommerceData
                };
                if (payload.userData && Object.keys(payload.userData).length > 0) {
                    pushPayload.user_data = payload.userData;
                }
                window.dataLayer.push(pushPayload);
            })();
        @endif
    </script>
    @endscript

</div>
