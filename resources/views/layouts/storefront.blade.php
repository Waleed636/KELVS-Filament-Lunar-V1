<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // ── Resolve page-level SEO values with site-level fallbacks ──────────
        $pageSeoTitle       = $seoTitle       ?? null;
        $pageSeoDescription = $seoDescription ?? null;
        $pageSeoKeywords    = $seoKeywords    ?? null;
        $pageCanonicalUrl   = $canonicalUrl   ?? request()->url();
        $pageProductUrl     = $productUrl     ?? request()->url();
        $pageProductName    = $productName    ?? null;

        $resolvedTitle = $pageSeoTitle
            ?? config('app.name', 'KELVS Skin') . ' — Science-Led Skincare';

        $resolvedDescription = $pageSeoDescription
            ?? 'KELVS Skin offers dermatologist-inspired skincare formulas. Shop cleansers, serums, moisturisers and SPF made for real results.';

        $siteName = 'KELVS Skin';
        $isProductPage = isset($seoTitle);
    @endphp

    {{-- ── Primary SEO Tags ─────────────────────────────────────────────── --}}
    <title>{{ $resolvedTitle }}</title>
    <meta name="description" content="{{ $resolvedDescription }}">
    @if($pageSeoKeywords)
        <meta name="keywords" content="{{ $pageSeoKeywords }}">
    @endif
    <link rel="canonical" href="{{ $pageCanonicalUrl }}">
    <meta name="robots" content="index, follow">

    {{-- ── Open Graph (Facebook / WhatsApp / LinkedIn) ─────────────────── --}}
    <meta property="og:type"        content="{{ $isProductPage ? 'product' : 'website' }}">
    <meta property="og:title"       content="{{ $resolvedTitle }}">
    <meta property="og:description" content="{{ $resolvedDescription }}">
    <meta property="og:url"         content="{{ $pageProductUrl }}">
    <meta property="og:site_name"   content="{{ $siteName }}">
    @if($isProductPage)
        <meta property="og:availability" content="instock">
    @endif

    {{-- ── Twitter Card ─────────────────────────────────────────────────── --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $resolvedTitle }}">
    <meta name="twitter:description" content="{{ $resolvedDescription }}">

    {{-- ── Structured Data: Product (JSON-LD) ─────────────────────────── --}}
    @if($isProductPage && $pageProductName)
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "{{ addslashes($pageProductName) }}",
        "description": "{{ addslashes($resolvedDescription) }}",
        "url": "{{ $pageProductUrl }}",
        "brand": {
            "@type": "Brand",
            "name": "{{ $siteName }}"
        },
        "offers": {
            "@type": "Offer",
            "url": "{{ $pageProductUrl }}",
            "priceCurrency": "PKR",
            "availability": "https://schema.org/InStock",
            "seller": {
                "@type": "Organization",
                "name": "{{ $siteName }}"
            }
        }
    }
    </script>
    @endif

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS & JS Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ mobileMenuOpen: false }" class="h-full bg-white text-[#111111] font-sans antialiased flex flex-col selection:bg-[#e8dcd2] selection:text-[#111111]">

    <!-- Mobile Drawer Overlay / Backdrop -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false"
         class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm md:hidden" 
         style="display: none;">
    </div>

    <!-- Mobile Drawer Panel -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-50 w-full max-w-xs bg-[#fbfbfa] shadow-2xl flex flex-col border-l border-gray-100 md:hidden"
         style="display: none;">
        
        <!-- Drawer Header -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-gray-100 bg-white">
            <a href="/" wire:navigate @click="mobileMenuOpen = false" class="flex items-center space-x-2.5">
                <span class="text-xl font-bold tracking-widest text-[#111111]">KELVS</span>
            </a>
            <button @click="mobileMenuOpen = false" class="p-2 -mr-2 text-gray-500 hover:text-[#111111] focus:outline-none" aria-label="Close Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Drawer Links -->
        <nav class="flex-grow px-6 py-8 flex flex-col space-y-6 text-sm font-semibold tracking-wide uppercase">
            <a href="/" wire:navigate @click="mobileMenuOpen = false" class="{{ request()->is('/') || request()->is('') ? 'text-[#111111] border-l-2 border-[#111111] pl-3' : 'text-gray-500 hover:text-[#111111] pl-3' }} transition">Home</a>
            <a href="/about" wire:navigate @click="mobileMenuOpen = false" class="{{ request()->is('about') ? 'text-[#111111] border-l-2 border-[#111111] pl-3' : 'text-gray-500 hover:text-[#111111] pl-3' }} transition">About</a>
            <a href="/sky" wire:navigate @click="mobileMenuOpen = false" class="{{ request()->is('sky*') ? 'text-[#111111] border-l-2 border-[#111111] pl-3' : 'text-gray-500 hover:text-[#111111] pl-3' }} transition">Blog</a>
            
            <div class="pt-6 border-t border-gray-100">
                <a href="/shop" wire:navigate @click="mobileMenuOpen = false" class="block w-full text-center py-3 bg-[#111111] hover:bg-[#222222] text-white text-xs font-bold rounded-[4px] tracking-wide transition duration-200">
                    Shop
                </a>
            </div>
        </nav>

        <!-- Drawer Footer -->
        <div class="p-6 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 text-center">
            <p>&copy; {{ date('Y') }} KELVS Store.</p>
            <p class="mt-1 font-medium">Built for the Pakistani climate.</p>
        </div>
    </div>

    <!-- Header / Navigation -->
    <header class="sticky top-0 z-40 w-full border-b border-gray-100 bg-white/95 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" wire:navigate class="flex items-center space-x-2.5 group">
                <span class="text-2xl font-bold tracking-widest text-[#111111] group-hover:opacity-80 transition duration-300">
                    KELVS
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

                <!-- Mobile Menu Hamburger Button -->
                <button @click="mobileMenuOpen = true" class="md:hidden p-2 -mr-2 text-[#111111] hover:opacity-80 focus:outline-none" aria-label="Open Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
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
                        KELVS
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
                        <!-- <li><a href="/sky/faq" wire:navigate class="hover:text-[#111111] transition">FAQ</a></li> -->
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
