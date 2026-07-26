<div>
    @if($bars && $bars->count() > 0)
        <div x-data="{
                activeIndex: 0,
                count: {{ $bars->count() }},
                dismissed: false,
                copiedCode: null,
                timer: null,
                init() {
                    if (sessionStorage.getItem('kelvs_promo_dismissed') === 'true') {
                        this.dismissed = true;
                    }
                    if (this.count > 1) {
                        this.startAutoplay();
                    }
                },
                startAutoplay() {
                    this.stopAutoplay();
                    this.timer = setInterval(() => {
                        this.next();
                    }, 5000);
                },
                stopAutoplay() {
                    if (this.timer) clearInterval(this.timer);
                },
                next() {
                    this.activeIndex = (this.activeIndex + 1) % this.count;
                },
                prev() {
                    this.activeIndex = (this.activeIndex - 1 + this.count) % this.count;
                },
                dismiss() {
                    this.dismissed = true;
                    sessionStorage.setItem('kelvs_promo_dismissed', 'true');
                },
                copyCode(code) {
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(code);
                    }
                    this.copiedCode = code;
                    setTimeout(() => { this.copiedCode = null; }, 2500);
                }
             }"
             x-show="!dismissed"
             @mouseenter="stopAutoplay()"
             @mouseleave="count > 1 && startAutoplay()"
             class="relative w-full z-40 text-xs sm:text-sm font-medium tracking-wide shadow-sm">

            @foreach($bars as $index => $bar)
                <div x-show="activeIndex === {{ $index }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="w-full py-2.5 px-4 sm:px-12 flex items-center justify-center min-h-[40px] border-b border-black/10 text-center"
                     style="background-color: {{ $bar->bg_color }}; color: {{ $bar->text_color }};">

                    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-center gap-2 sm:gap-4 text-center">

                        @if($bar->badge_text)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] sm:text-xs font-extrabold uppercase tracking-wider bg-white/20 text-current backdrop-blur-sm shrink-0">
                                {{ $bar->badge_text }}
                            </span>
                        @endif

                        <span class="font-semibold">
                            {{ $bar->content }}
                        </span>

                        @if($bar->promo_code)
                            <button @click="copyCode('{{ $bar->promo_code }}')" 
                                    class="inline-flex items-center space-x-1 px-2 py-0.5 bg-white/15 hover:bg-white/30 border border-white/25 rounded transition duration-200 shrink-0 text-[11px] font-mono uppercase font-bold focus:outline-none"
                                    title="Click to copy promo code">
                                <span>Code: {{ $bar->promo_code }}</span>
                                <template x-if="copiedCode === '{{ $bar->promo_code }}'">
                                    <span class="text-[10px] text-emerald-300 font-sans font-extrabold ml-1">✓ Copied!</span>
                                </template>
                                <template x-if="copiedCode !== '{{ $bar->promo_code }}'">
                                    <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 012-2v-8a2 2 0 01-2-2h-8a2 2 0 01-2 2v8a2 2 0 012 2z"/>
                                    </svg>
                                </template>
                            </button>
                        @endif

                        @if($bar->button_text && $bar->button_url)
                            <a href="{{ $bar->button_url }}" 
                               class="inline-flex items-center space-x-1 font-bold underline underline-offset-4 hover:opacity-80 transition shrink-0">
                                <span>{{ $bar->button_text }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        @endif
                    </div>

                    @if($bars->count() > 1)
                        <!-- Prev/Next Controls -->
                        <div class="hidden sm:flex items-center space-x-1 absolute left-3 inset-y-0 my-auto h-6">
                            <button @click="prev()" class="p-1 rounded-full hover:bg-white/20 opacity-75 hover:opacity-100 transition focus:outline-none" aria-label="Previous Announcement">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button @click="next()" class="p-1 rounded-full hover:bg-white/20 opacity-75 hover:opacity-100 transition focus:outline-none" aria-label="Next Announcement">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Dismiss Close Button -->
            <button @click="dismiss()" 
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 p-1 rounded-full hover:bg-black/10 transition opacity-70 hover:opacity-100 focus:outline-none" 
                    aria-label="Dismiss Announcement Bar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

        </div>
    @endif
</div>
