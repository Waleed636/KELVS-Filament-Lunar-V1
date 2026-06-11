<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KELVS Store') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS & JS Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-white text-[#111111] font-sans antialiased flex flex-col selection:bg-[#e8dcd2] selection:text-[#111111]">

    <!-- Header / Navigation -->
    <header class="sticky top-0 z-40 w-full border-b border-gray-100 bg-white/95 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" wire:navigate class="flex items-center space-x-2.5 group">
                <span class="text-2xl font-bold tracking-widest text-[#111111] group-hover:opacity-80 transition duration-300">
                    KELVS
                </span>
                <span class="text-[9px] uppercase font-bold text-gray-500 tracking-widest border border-gray-200 px-1.5 py-0.5 rounded bg-gray-50">
                    Skin
                </span>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold tracking-wide uppercase">
                <a href="/" wire:navigate class="{{ request()->is('/') || request()->is('') ? 'text-[#111111]' : 'text-gray-500 hover:text-[#111111]' }} transition">Home</a>
                <a href="/about" wire:navigate class="{{ request()->is('about') ? 'text-[#111111]' : 'text-gray-500 hover:text-[#111111]' }} transition">About</a>
                <a href="/sky" wire:navigate class="{{ request()->is('sky*') ? 'text-[#111111]' : 'text-gray-500 hover:text-[#111111]' }} transition">Blog</a>
                <a href="/shop" wire:navigate class="px-4 py-1.5 bg-[#111111] hover:bg-[#222222] text-white text-xs font-bold rounded-[4px] tracking-wide transition duration-200">Shop</a>
            </nav>

            <!-- User Actions -->
            <div class="flex items-center space-x-4">
                <!-- Admin link if staff -->
                @auth
                    <a href="/admin" class="text-xs text-gray-600 hover:text-[#111111] transition border border-gray-200 rounded px-2.5 py-1 bg-gray-50 font-medium">
                        Admin Dashboard
                    </a>
                @endauth

                <!-- Cart Button -->
                <livewire:storefront.cart-badge />
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200/80 text-gray-500 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <!-- Branding -->
                <div class="md:col-span-2">
                    <span class="text-lg font-extrabold tracking-widest text-[#111111]">
                        KELVS SKIN
                    </span>
                    <p class="mt-4 text-gray-600 max-w-sm leading-relaxed">
                        Science-led skincare for real results. Minimal formulas, maximum performance. Made to simplify your daily routine with high-quality, dermatologically inspired ingredients.
                    </p>
                </div>
                
                <!-- Links Column 1 -->
                <div>
                    <h3 class="text-xs uppercase font-bold text-[#111111] tracking-widest mb-4">Shop</h3>
                    <ul class="space-y-2.5">
                        <li><a href="/shop" wire:navigate class="hover:text-[#111111] transition">All Products</a></li>
                        <li><a href="/cart" wire:navigate class="hover:text-[#111111] transition">View Cart</a></li>
                    </ul>
                </div>

                <!-- Links Column 2 -->
                <div>
                    <h3 class="text-xs uppercase font-bold text-[#111111] tracking-widest mb-4">Content</h3>
                    <ul class="space-y-2.5">
                        <li><a href="/about" wire:navigate class="hover:text-[#111111] transition">About KELVS</a></li>
                        <li><a href="/sky" wire:navigate class="hover:text-[#111111] transition">Blog & Guides</a></li>
                        <li><a href="/sky/faq" wire:navigate class="hover:text-[#111111] transition">FAQ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright info -->
            <div class="border-t border-gray-200/60 pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} KELVS Store. All rights reserved.</p>
                <p class="mt-2 md:mt-0 font-medium">Built for the Pakistani climate.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
