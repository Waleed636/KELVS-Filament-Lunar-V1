<x-filament-panels::page>
    {{-- Search & Controls Card --}}
    <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 space-y-4">
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-sparkles" class="w-5 h-5 text-amber-500" />
                Live Google Search & Question Research
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Directly scrape genuine search queries and high-intent customer questions from Google Autocomplete for Pakistan or global markets.
            </p>
        </div>

        <form wire:submit.prevent="search" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                {{-- Quick Product Selector --}}
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Select Store Product (Optional)
                    </label>
                    <select 
                        wire:model.live="selectedProductId"
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                        <option value="">-- Choose Product to Auto-Fill --</option>
                        @foreach($products as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Keyword Input --}}
                <div class="md:col-span-5">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Search Keyword / Ingredient <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="keyword" 
                        placeholder="e.g. vitamin c serum, niacinamide, cleanser" 
                        required
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    />
                </div>

                {{-- Country Selector --}}
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Target Country
                    </label>
                    <select 
                        wire:model="country"
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                        <option value="pk">🇵🇰 Pakistan (Google.com.pk)</option>
                        <option value="us">🇺🇸 Global / United States</option>
                        <option value="gb">🇬🇧 United Kingdom</option>
                        <option value="ae">🇦🇪 UAE / Middle East</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                <span class="text-xs text-gray-400">
                    Queries Google live API with What, How, Why, Can, Best, Price & Intent modifiers.
                </span>

                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white text-xs font-semibold uppercase tracking-wider shadow-sm transition disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="search" class="flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-o-magnifying-glass" class="w-4 h-4" />
                        Discover Questions
                    </span>
                    <span wire:loading wire:target="search" class="flex items-center gap-1.5">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Fetching from Google...
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- Results Container --}}
    @if($totalCount > 0)
        <div class="space-y-4">
            {{-- Results Header --}}
            <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-amber-50/50 dark:bg-gray-800/60 rounded-xl border border-amber-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 bg-amber-500 text-white rounded-full text-xs font-bold shadow-sm">
                        {{ $totalCount }} Questions
                    </span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Discovered for <strong class="text-gray-900 dark:text-white">"{{ $searchedKeyword }}"</strong> ({{ strtoupper($country) }})
                    </span>
                    @if($selectedProductId && isset($products[$selectedProductId]))
                        <span class="text-xs text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-950/50 px-2 py-0.5 rounded border border-amber-300 dark:border-amber-800">
                            Linked: {{ $products[$selectedProductId] }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        wire:click="exportTxt"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm transition"
                    >
                        <x-filament::icon icon="heroicon-o-arrow-down-tray" class="w-4 h-4 text-gray-500" />
                        Export .TXT
                    </button>
                </div>
            </div>

            {{-- Category Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($results as $category => $items)
                    @if(!empty($items))
                        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col">
                            {{-- Category Title --}}
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    {{ $category }}
                                </h3>
                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 bg-gray-200/70 dark:bg-gray-700 px-2 py-0.5 rounded-full">
                                    {{ count($items) }} queries
                                </span>
                            </div>

                            {{-- Category Items List --}}
                            <div class="p-3 divide-y divide-gray-100 dark:divide-gray-800/60 max-h-96 overflow-y-auto space-y-1">
                                @foreach($items as $query)
                                    <div 
                                        x-data="{ copied: false }"
                                        class="py-2 px-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 flex items-center justify-between gap-3 text-xs text-gray-800 dark:text-gray-200 group transition"
                                    >
                                        <span class="font-normal leading-relaxed truncate select-all">
                                            {{ $query }}
                                        </span>

                                        <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100 shrink-0">
                                            {{-- Copy button --}}
                                            <button 
                                                type="button" 
                                                @click="
                                                    navigator.clipboard.writeText('{{ addslashes($query) }}');
                                                    copied = true;
                                                    setTimeout(() => copied = false, 2000);
                                                "
                                                title="Copy to clipboard"
                                                class="p-1 text-gray-400 hover:text-amber-600 transition"
                                            >
                                                <span x-show="!copied">
                                                    <x-filament::icon icon="heroicon-o-clipboard-document" class="w-4 h-4" />
                                                </span>
                                                <span x-show="copied" class="text-green-500 font-bold text-[10px]" style="display: none;">
                                                    ✓
                                                </span>
                                            </button>

                                            {{-- Open in Google --}}
                                            <a 
                                                href="https://www.google.com/search?q={{ urlencode($query) }}" 
                                                target="_blank" 
                                                title="View search on Google"
                                                class="p-1 text-gray-400 hover:text-blue-500 transition"
                                            >
                                                <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                                            </a>

                                            {{-- Add directly as product FAQ if product is selected --}}
                                            @if($selectedProductId)
                                                <button 
                                                    type="button" 
                                                    wire:click="addAsProductFaq('{{ addslashes($query) }}')"
                                                    title="Add this directly to Product FAQs"
                                                    class="p-1 text-gray-400 hover:text-emerald-500 transition"
                                                >
                                                    <x-filament::icon icon="heroicon-o-plus-circle" class="w-4 h-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @elseif(!empty($searchedKeyword))
        <div class="p-8 text-center bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 text-gray-500">
            <x-filament::icon icon="heroicon-o-face-frown" class="w-8 h-8 mx-auto text-gray-400 mb-2" />
            <p class="text-sm">No autocomplete queries found for "{{ $searchedKeyword }}". Try a broader seed keyword like "vitamin c" or "serum".</p>
        </div>
    @endif
</x-filament-panels::page>
