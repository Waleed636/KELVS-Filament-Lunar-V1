<div class="bg-white border border-gray-100 rounded-2xl p-5">
    <h4 class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400 mb-4">Search</h4>
    <form method="GET">
        <div class="relative">
            <input
                class="w-full text-sm text-[#111111] bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 pr-10 focus:outline-none focus:border-[#111111] focus:bg-white transition"
                type="text"
                name="search"
                id="search"
                placeholder="Search posts..."
                value="{{ request()->get('search') }}"
            >
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#111111] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </form>
</div>
