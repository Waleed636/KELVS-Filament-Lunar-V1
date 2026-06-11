<div>
    <a href="/cart" wire:navigate class="relative p-2 text-[#111111] hover:opacity-75 transition group block">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        @if($cartCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-[10px] font-bold leading-none text-white bg-[#111111] rounded-full transform translate-x-1/3 -translate-y-1/3 shadow-sm">
                {{ $cartCount }}
            </span>
        @endif
    </a>
</div>
