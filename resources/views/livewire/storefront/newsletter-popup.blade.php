<div 
    x-data="{ 
        show: false,
        copied: false,
        init() {
            if (!localStorage.getItem('kelvs_newsletter_dismissed')) {
                setTimeout(() => {
                    this.show = true;
                }, 5000);
            }
        },
        dismiss() {
            this.show = false;
            localStorage.setItem('kelvs_newsletter_dismissed', 'true');
        },
        copyToClipboard() {
            navigator.clipboard.writeText('{{ $discountCode }}').then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
    style="display: none;"
>
    <!-- Modal Container -->
    <div 
        @click.away="dismiss()"
        class="relative w-full max-w-3xl overflow-hidden bg-white rounded-lg shadow-2xl flex flex-col md:flex-row border border-gray-100 max-h-[90vh] md:max-h-none"
        x-show="show"
        x-transition:enter="transition ease-out duration-300 transform scale-95"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform scale-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        <!-- Close Button -->
        <button 
            @click="dismiss()" 
            class="absolute top-4 right-4 z-10 p-1.5 text-gray-400 hover:text-[#111111] bg-white/80 hover:bg-gray-100 rounded-full transition duration-200"
            aria-label="Close newsletter popup"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Left Column: Premium Branding -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-[#fbfafa] to-[#e8dcd2] p-8 md:p-12 flex flex-col justify-between items-center text-center relative overflow-hidden min-h-[180px] md:min-h-full">
            <!-- Decorative SVG Circles -->
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/30 rounded-full blur-xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/20 rounded-full blur-xl pointer-events-none"></div>

            <div class="my-auto z-10">
                <span class="text-2xl font-bold tracking-widest text-[#111111] block mb-2">KELVS</span>
                <div class="h-[1px] w-12 bg-[#111111]/20 mx-auto my-4"></div>
                <p class="text-xs uppercase tracking-widest text-gray-500 font-semibold mb-1">Dermatologist-Inspired</p>
                <p class="text-sm font-medium text-gray-700 italic">Science-Led Skincare for Real Results</p>
            </div>
        </div>

        <!-- Right Column: Form Block -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white overflow-y-auto">
            @if (!$submitted)
                <div>
                    <!-- Header -->
                    <span class="inline-block px-2.5 py-1 mb-3 text-[10px] font-bold tracking-widest text-[#111111] uppercase bg-[#e8dcd2] rounded-full">
                        Special Offer
                    </span>
                    <h3 class="text-2xl font-bold text-[#111111] tracking-tight leading-tight mb-2">
                        Get 10% OFF
                    </h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        Subscribe to receive skincare tips, exclusive launches, and code for 10% off your first order.
                    </p>

                    <!-- Form -->
                    <form wire:submit.prevent="submit" class="space-y-4">
                        <div>
                            <label for="newsletter-email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Email Address
                            </label>
                            <input 
                                type="email" 
                                id="newsletter-email" 
                                wire:model.defer="email"
                                placeholder="name@example.com"
                                class="w-full px-4 py-2.5 text-sm bg-[#fbfbfa] border @error('email') border-red-500 @else border-gray-200 @enderror rounded focus:outline-none focus:border-[#111111] transition duration-200"
                            />
                            @error('email')
                                <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="relative flex items-center py-2">
                            <div class="flex-grow border-t border-gray-200"></div>
                            <span class="flex-shrink mx-3 text-[10px] text-gray-400 font-bold uppercase tracking-wider">Or</span>
                            <div class="flex-grow border-t border-gray-200"></div>
                        </div>

                        <div>
                            <label for="newsletter-phone" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Phone Number
                            </label>
                            <input 
                                type="tel" 
                                id="newsletter-phone" 
                                wire:model.defer="phone"
                                placeholder="e.g. 03001234567"
                                class="w-full px-4 py-2.5 text-sm bg-[#fbfbfa] border @error('phone') border-red-500 @else border-gray-200 @enderror rounded focus:outline-none focus:border-[#111111] transition duration-200"
                            />
                            @error('phone')
                                <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <button 
                            type="submit" 
                            class="w-full py-3 mt-4 text-xs font-bold tracking-widest text-white uppercase bg-[#111111] hover:bg-[#222222] rounded transition duration-200 shadow-md hover:shadow-lg flex items-center justify-center space-x-2"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Claim Discount</span>
                            <span wire:loading class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        </button>
                    </form>
                </div>
            @else
                <!-- Success State -->
                <div 
                    class="text-center py-4"
                    x-init="localStorage.setItem('kelvs_newsletter_dismissed', 'true')"
                >
                    <!-- Success Icon -->
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-green-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-[#111111] mb-2">You're on the list!</h3>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed">
                        Thank you for subscribing. Use the discount code below at checkout to claim your 10% off:
                    </p>

                    <!-- Coupon Display Box -->
                    <div class="relative bg-gray-50 border border-dashed border-gray-300 rounded p-4 mb-6 group">
                        <div class="text-2xl font-mono font-bold tracking-widest text-[#111111] select-all">
                            {{ $discountCode }}
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-semibold">Click to copy code</p>
                        
                        <button 
                            @click="copyToClipboard()"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            title="Copy Code"
                        ></button>
                    </div>

                    <!-- Success Feedback Alert -->
                    <div 
                        x-show="copied" 
                        x-transition
                        class="text-green-600 text-xs font-semibold mb-6"
                        style="display: none;"
                    >
                        ✓ Code copied to clipboard!
                    </div>

                    <button 
                        @click="show = false"
                        class="px-6 py-2.5 text-xs font-bold tracking-widest text-[#111111] border border-gray-200 hover:border-[#111111] rounded uppercase transition duration-200"
                    >
                        Continue Shopping
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
