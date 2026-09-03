<div class="bg-white min-h-screen">
    <!-- Hero Banner -->
    <section class="bg-[#f5f0eb] py-16 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-gray-400 mb-3">Help &amp; Answers</span>
            <h1 class="text-3xl sm:text-4xl font-bold text-[#111111] mb-4">Frequently Asked Questions</h1>
            <p class="text-gray-600 max-w-xl mx-auto text-sm sm:text-base leading-relaxed">
                Find answers to common questions about KELVS formulations, skincare routines, shipping, and orders.
            </p>
        </div>
    </section>

    <!-- Content Area -->
    <div class="max-w-4xl mx-auto px-4 py-16">
        @if(isset($faqs) && !$faqs->isEmpty())
            <div class="space-y-6" x-data="{ openFaq: null }">
                @foreach($faqs as $index => $faq)
                    <div class="border border-[#e8dcd2] rounded-2xl overflow-hidden bg-[#FAF7F4]/50 transition shadow-sm hover:shadow-md">
                        <button type="button" 
                                @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})"
                                class="w-full flex items-center justify-between gap-4 p-5 sm:p-6 text-left font-semibold text-[#111111] hover:text-black">
                            <span class="text-base sm:text-lg">{{ $faq->question }}</span>
                            <span class="shrink-0 w-8 h-8 rounded-full bg-white border border-[#e8dcd2] flex items-center justify-center text-gray-500 transition-transform duration-200"
                                  :class="openFaq === {{ $index }} ? 'rotate-180 bg-[#111111] text-white border-black' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </span>
                        </button>
                        <div x-show="openFaq === {{ $index }}" x-collapse class="px-5 sm:px-6 pb-6 pt-1 text-sm sm:text-base text-gray-600 border-t border-[#e8dcd2]/60 prose max-w-none">
                            {!! $faq->answer !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Friendly Empty State (Prevents Livewire Missing Root Tag 500 error) -->
            <div class="text-center py-16 px-4 bg-[#FAF7F4] rounded-2xl border border-[#e8dcd2]">
                <div class="w-14 h-14 rounded-full bg-white border border-[#e8dcd2] flex items-center justify-center mx-auto mb-4 text-gray-400 shadow-sm">
                    <svg class="w-7 h-7 text-[#A67C52]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-[#111111] mb-2">Frequently Asked Questions</h3>
                <p class="text-sm text-gray-600 max-w-md mx-auto mb-8 leading-relaxed">
                    Looking for product-specific answers? Each product on our store features an in-depth dermatological Q&amp;A section covering routines, actives, and results.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ url('/shop') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-[#111111] text-white text-xs font-bold uppercase tracking-wider hover:bg-black transition shadow">
                        Browse Products &amp; FAQs
                    </a>
                    <a href="{{ url('/about') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-gray-300 text-gray-700 text-xs font-bold uppercase tracking-wider hover:bg-gray-100 transition">
                        Our Science &amp; Story
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
