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
<body class="h-full bg-slate-950 text-slate-100 font-sans antialiased flex flex-col selection:bg-amber-500 selection:text-slate-950">

    <!-- Header / Navigation -->
    <header class="sticky top-0 z-40 w-full border-b border-slate-800 bg-slate-950/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" wire:navigate class="flex items-center space-x-2 group">
                <span class="text-2xl font-extrabold tracking-wider bg-gradient-to-r from-amber-400 via-orange-500 to-amber-600 bg-clip-text text-transparent group-hover:from-amber-300 group-hover:to-amber-500 transition duration-300">
                    KELVS
                </span>
                <span class="text-xs uppercase font-semibold text-slate-400 tracking-widest border border-slate-700 px-1.5 py-0.5 rounded bg-slate-900">
                    Store
                </span>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-8 text-sm font-medium">
                <a href="/" wire:navigate class="text-slate-300 hover:text-amber-400 transition">Home</a>
                <a href="/sky" wire:navigate class="text-slate-300 hover:text-amber-400 transition">Blog</a>
                <a href="/lunar" class="text-slate-300 hover:text-amber-400 transition">Seller Portal</a>
            </nav>

            <!-- User Actions -->
            <div class="flex items-center space-x-4">
                <!-- Admin link if staff -->
                @auth
                    <a href="/admin" class="text-xs text-slate-400 hover:text-amber-400 transition border border-slate-800 rounded px-2.5 py-1 bg-slate-900/50">
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
    <footer class="bg-slate-950 border-t border-slate-900 text-slate-500 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Branding -->
                <div class="md:col-span-2">
                    <span class="text-lg font-bold text-slate-200 tracking-wider">
                        KELVS STORE
                    </span>
                    <p class="mt-4 text-slate-400 max-w-sm leading-relaxed">
                        A modern, high-performance shopping experience powered by Laravel, Livewire, and Lunar PHP. Designed for speed, security, and elegance.
                    </p>
                </div>
                
                <!-- Links Column 1 -->
                <div>
                    <h3 class="text-xs uppercase font-semibold text-slate-300 tracking-widest mb-4">Shop</h3>
                    <ul class="space-y-2.5">
                        <li><a href="/" wire:navigate class="hover:text-amber-400 transition">All Products</a></li>
                        <li><a href="/cart" wire:navigate class="hover:text-amber-400 transition">View Cart</a></li>
                    </ul>
                </div>

                <!-- Links Column 2 -->
                <div>
                    <h3 class="text-xs uppercase font-semibold text-slate-300 tracking-widest mb-4">Content</h3>
                    <ul class="space-y-2.5">
                        <li><a href="/sky" wire:navigate class="hover:text-amber-400 transition">Blog / News</a></li>
                        <li><a href="/sky/faq" wire:navigate class="hover:text-amber-400 transition">FAQ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright info -->
            <div class="border-t border-slate-900 pt-8 flex flex-col md:flex-row items-center justify-between text-xs">
                <p>&copy; {{ date('Y') }} KELVS Store. All rights reserved.</p>
                <p class="mt-2 md:mt-0">Powered by Antigravity & Lunar PHP</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
