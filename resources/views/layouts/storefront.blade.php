<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.dataLayer = window.dataLayer || [];

        // Guard to ensure listeners are only added once across wire:navigate transitions
        if (!window.dataLayerListenersRegistered) {
            window.dataLayerListenersRegistered = true;

            // ── DataLayer: Global Ecommerce Event Bridge ─────────────────────────
            // Handles all events dispatched via Livewire's dispatch('track-ecommerce-event')
            window.addEventListener('track-ecommerce-event', function(event) {
                let payload = event.detail;
                if (Array.isArray(payload)) {
                    payload = payload[0];
                } else if (payload && typeof payload === 'object' && '0' in payload) {
                    payload = payload['0'];
                }
                if (!payload || !payload.eventName) return;

                // Purchase deduplication: prevent double-counting on page refresh
                if (payload.eventName === 'purchase' && payload.ecommerceData && payload.ecommerceData.transaction_id) {
                    const dedupKey = 'fired_purchase_' + payload.ecommerceData.transaction_id;
                    if (sessionStorage.getItem(dedupKey)) return;
                    sessionStorage.setItem(dedupKey, '1');
                }

                // Always clear ecommerce object before pushing to prevent data bleed
                window.dataLayer.push({ ecommerce: null });

                const pushPayload = {
                    event:    payload.eventName,
                    event_id: payload.eventId,
                    ecommerce: payload.ecommerceData
                };

                // Forward hashed user_data (Enhanced Conversions) if present
                if (payload.userData && Object.keys(payload.userData).length > 0) {
                    pushPayload.user_data = payload.userData;
                }

                window.dataLayer.push(pushPayload);
            });

            // ── DataLayer: select_item — product card click listener ─────────────
            // Fires when any element with data-track-select-item is clicked
            document.addEventListener('click', function(e) {
                // Ignore clicks on Add to Cart buttons
                if (e.target.closest('button') || e.target.closest('[wire\\:click]')) return;

                let el = e.target.closest('[data-track-select-item]');
                if (!el) {
                    const card = e.target.closest('.group');
                    if (card) {
                        el = card.querySelector('[data-track-select-item]');
                    }
                }
                if (!el) return;

                window.dataLayer.push({ ecommerce: null });
                window.dataLayer.push({
                    event: 'select_item',
                    ecommerce: {
                        item_list_id:   el.dataset.listId   || '',
                        item_list_name: el.dataset.listName || '',
                        items: [{
                            item_id:        el.dataset.itemId    || '',
                            item_name:      el.dataset.itemName  || '',
                            item_brand:     'KELVS',
                            item_category:  el.dataset.itemCategory || 'Skincare',
                            item_variant:   el.dataset.itemVariant  || '',
                            item_list_name: el.dataset.listName || '',
                            price:          parseFloat(el.dataset.itemPrice) || 0,
                            index:          parseInt(el.dataset.itemIndex)   || 0,
                            quantity:       1
                        }]
                    }
                });
            });

            // Initialize flag to track initial page load vs dynamic navigation
            window.isInitialLoad = true;

            // ── DataLayer: Initial Page View (deferred to DOMContentLoaded or run immediately if ready) ──
            function pushInitialPageView() {
                window.dataLayer.push({
                    event: 'page_view',
                    is_virtual: false,
                    page_path: window.location.pathname + window.location.search,
                    page_title: document.title,
                    page_referrer: document.referrer || ''
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', pushInitialPageView);
            } else {
                pushInitialPageView();
            }

            // ── DataLayer: Dynamic Page Views for wire:navigate ──────────────────
            document.addEventListener('livewire:navigated', function() {
                if (window.isInitialLoad) {
                    window.isInitialLoad = false;
                    return;
                }
                window.dataLayer.push({
                    event: 'page_view',
                    is_virtual: true,
                    page_path: window.location.pathname + window.location.search,
                    page_title: document.title,
                    page_referrer: document.referrer || ''
                });
            });
        }
    </script>


    @php
        $siteName = 'KELVS';

        // ── Resolve page-level SEO values with site-level fallbacks ──────────
        $pageSeoTitle       = $seoTitle       ?? null;
        $pageSeoDescription = $seoDescription ?? null;
        $pageSeoKeywords    = $seoKeywords    ?? null;
        $pageCanonicalUrl   = $canonicalUrl   ?? request()->url();
        $pageProductUrl     = $productUrl     ?? request()->url();
        $pageProductName    = $productName    ?? null;
        $pageSeoImage       = $seoImage       ?? null;

        // Auto-extract SEO meta details if viewing a blog post from Lara-Zeus Sky
        if (isset($post) && $post instanceof \LaraZeus\Sky\Models\Post) {
            $pageSeoTitle = $post->title . ' | Journal | ' . $siteName;
            
            // Clean description and constrain to 160 characters
            $rawDesc = $post->description ?? strip_tags($post->getContent());
            $pageSeoDescription = mb_strimwidth(strip_tags((string) $rawDesc), 0, 160, '…');
            
            // Extract categories & tags as keywords
            $tagsList = $post->tags->pluck('name')->toArray();
            if (!empty($tagsList)) {
                $pageSeoKeywords = implode(', ', $tagsList);
            }
            
            // Extract post image
            if (empty($pageSeoImage)) {
                $pageSeoImage = $post->image();
            }
        }

        $resolvedTitle = $pageSeoTitle
            ?? (config('app.name') === 'Laravel' ? 'KELVS' : config('app.name', 'KELVS')) . ' — Science-Led Skincare';

        $resolvedDescription = $pageSeoDescription
            ?? 'KELVS offers dermatologist-inspired skincare formulas. Shop cleansers, serums, moisturisers and SPF made for real results.';

        $isProductPage = isset($seoTitle) && !isset($post);

        // Resolve social preview image
        if (empty($pageSeoImage)) {
            $pageSeoImage = asset('images/hero_lifestyle.png');
        } else {
            if (!filter_var($pageSeoImage, FILTER_VALIDATE_URL)) {
                $pageSeoImage = url($pageSeoImage);
            }
        }
    @endphp

    {{-- ── Primary SEO Tags ─────────────────────────────────────────────── --}}
    <title>{{ $resolvedTitle }}</title>
    <meta name="description" content="{{ $resolvedDescription }}">
    @if($pageSeoKeywords)
        <meta name="keywords" content="{{ $pageSeoKeywords }}">
    @endif
    <link rel="canonical" href="{{ $pageCanonicalUrl }}">
    <meta name="robots" content="{{ $seoRobots ?? 'index, follow' }}">

    {{-- ── Open Graph (Facebook / WhatsApp / LinkedIn) ─────────────────── --}}
    <meta property="og:type"        content="{{ $isProductPage ? 'product' : 'website' }}">
    <meta property="og:title"       content="{{ $resolvedTitle }}">
    <meta property="og:description" content="{{ $resolvedDescription }}">
    <meta property="og:image"       content="{{ $pageSeoImage }}">
    <meta property="og:url"         content="{{ $pageProductUrl }}">
    <meta property="og:site_name"   content="{{ $siteName }}">
    @if($isProductPage)
        <meta property="og:availability" content="instock">
    @endif

    {{-- ── Twitter Card ─────────────────────────────────────────────────── --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $resolvedTitle }}">
    <meta name="twitter:description" content="{{ $resolvedDescription }}">
    <meta name="twitter:image"       content="{{ $pageSeoImage }}">

    {{-- ── Structured Data: Product (JSON-LD) ─────────────────────────── --}}
    @if($isProductPage && $pageProductName)
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": {!! json_encode($pageProductName) !!},
        "description": {!! json_encode($resolvedDescription) !!},
        "url": "{{ $pageProductUrl }}",
        "image": "{{ $pageSeoImage }}",
        "sku": "{{ $productSku ?? 'N/A' }}",
        "brand": {
            "@type": "Brand",
            "name": "{{ $siteName }}"
        },
        @if(isset($averageRating) && $averageRating > 0)
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $averageRating }}",
            "reviewCount": "{{ $reviewCount ?? 0 }}"
        },
        @endif
        "offers": {
            "@type": "Offer",
            "url": "{{ $pageProductUrl }}",
            "priceCurrency": "PKR",
            "price": "{{ number_format($productPrice ?? 0, 2, '.', '') }}",
            "availability": "https://schema.org/InStock",
            "seller": {
                "@type": "Organization",
                "name": "{{ $siteName }}"
            }
        }
    }
    </script>
    @endif

    {{-- ── Structured Data: Blog Posting (JSON-LD) ────────────────────── --}}
    @if(isset($post) && $post instanceof \LaraZeus\Sky\Models\Post)
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": {!! json_encode($post->title) !!},
        "description": {!! json_encode($resolvedDescription) !!},
        "image": "{{ $pageSeoImage }}",
        "datePublished": "{{ optional($post->published_at)->toIso8601String() }}",
        "dateModified": "{{ optional($post->updated_at)->toIso8601String() }}",
        "author": {
            "@type": "Person",
            "name": "{{ $post->author->name ?? $siteName }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ $siteName }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ url('/favicon.ico') }}"
            }
        },
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ request()->url() }}"
        }
    }
    </script>
    @endif

    {{-- ── Structured Data: Organization & WebSite (JSON-LD) ──────────── --}}
    @if(request()->is('/'))
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ $siteName }}",
        "url": "{{ url('/') }}",
        "logo": "{{ url('/favicon.ico') }}",
        "sameAs": [
            "https://www.facebook.com/kelvsskin",
            "https://www.instagram.com/kelvsskin"
        ]
    }
    </script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ $siteName }}",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/shop') }}?search={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    @endif

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/k2.webp">

    {{-- Preload the Largest Contentful Paint (LCP) Image --}}
    @if(isset($lcpImageUrl))
        <link rel="preload" as="image" href="{{ $lcpImageUrl }}" fetchpriority="high">
    @endif

    <!-- Mobile Manifest & PWA Tags -->
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#F5F5F4">


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
            <a href="/" @click="mobileMenuOpen = false" class="flex items-center space-x-2.5">
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
            <a href="/" @click="mobileMenuOpen = false" class="{{ request()->is('/') || request()->is('') ? 'text-[#111111] border-l-2 border-[#111111] pl-3' : 'text-gray-500 hover:text-[#111111] pl-3' }} transition">Home</a>
            <a href="/about" @click="mobileMenuOpen = false" class="{{ request()->is('about') ? 'text-[#111111] border-l-2 border-[#111111] pl-3' : 'text-gray-500 hover:text-[#111111] pl-3' }} transition">About</a>
            <a href="/blog" @click="mobileMenuOpen = false" class="{{ request()->is('blog*') ? 'text-[#111111] border-l-2 border-[#111111] pl-3' : 'text-gray-500 hover:text-[#111111] pl-3' }} transition">Blog</a>
            
            <div class="pt-6 border-t border-gray-100">
                <a href="/shop" @click="mobileMenuOpen = false" class="block w-full text-center py-3 bg-[#111111] hover:bg-[#222222] text-white text-xs font-bold rounded-[4px] tracking-wide transition duration-200">
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
    <header class="sticky top-0 z-30 w-full border-b border-gray-100 bg-white/95 backdrop-blur-md">
        <!-- Promotional / Utility Announcement Bar -->
        <livewire:storefront.promotional-bar />
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2.5 group">
                <span class="text-2xl font-bold tracking-widest text-[#111111] group-hover:opacity-80 transition duration-300">
                    KELVS
                </span>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold tracking-wide uppercase">
                <a href="/" class="{{ request()->is('/') || request()->is('') ? 'text-[#111111]' : 'text-gray-500 hover:text-[#111111]' }} transition">Home</a>
                <a href="/about" class="{{ request()->is('about') ? 'text-[#111111]' : 'text-gray-500 hover:text-[#111111]' }} transition">About</a>
                <a href="/blog" class="{{ request()->is('blog*') ? 'text-[#111111]' : 'text-gray-500 hover:text-[#111111]' }} transition">Blog</a>
                <a href="/shop" class="px-4 py-1.5 bg-[#111111] hover:bg-[#222222] text-white text-xs font-bold rounded-[4px] tracking-wide transition duration-200">Shop</a>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                <!-- Branding -->
                <div>
                    <span class="text-lg font-extrabold tracking-widest text-[#111111]">
                        KELVS
                    </span>
                    <p class="mt-4 text-gray-600 max-w-sm leading-relaxed text-xs">
                        Science-led skincare for real results. Minimal formulas, maximum performance. Made to simplify your daily routine with high-quality, dermatologically inspired ingredients.
                    </p>
                </div>
                
                <!-- Links Column 1 -->
                <div>
                    <h3 class="text-xs uppercase font-bold text-[#111111] tracking-widest mb-4">Shop</h3>
                    <ul class="space-y-2.5">
                        <li><a href="/shop" class="hover:text-[#111111] transition">All Products</a></li>
                        <li><a href="/cart" class="hover:text-[#111111] transition">View Cart</a></li>
                    </ul>
                </div>

                <!-- Links Column 2 -->
                <div>
                    <h3 class="text-xs uppercase font-bold text-[#111111] tracking-widest mb-4">Content</h3>
                    <ul class="space-y-2.5">
                        <li><a href="/about" class="hover:text-[#111111] transition">About KELVS</a></li>
                        <li><a href="/blog" class="hover:text-[#111111] transition">Blog & Guides</a></li>
                    </ul>
                </div>

                <!-- Links Column 3: Customer Care & Policies -->
                <div>
                    <h3 class="text-xs uppercase font-bold text-[#111111] tracking-widest mb-4">Customer Care</h3>
                    @php
                        $footerPolicies = \App\Models\Post::where('post_type', 'page')
                            ->where('status', 'publish')
                            ->orderBy('title')
                            ->get(['id', 'title', 'slug']);

                        if ($footerPolicies->isEmpty()) {
                            $footerPolicies = collect([
                                (object)['title' => 'Return & Exchange Policy', 'slug' => 'return-policy'],
                                (object)['title' => 'Privacy Policy', 'slug' => 'privacy-policy'],
                                (object)['title' => 'Terms & Conditions', 'slug' => 'terms-and-conditions'],
                                (object)['title' => 'Shipping Policy', 'slug' => 'shipping-policy'],
                                (object)['title' => 'Refund Policy', 'slug' => 'refund-policy'],
                            ]);
                        }
                    @endphp
                    <ul class="space-y-2.5">
                        @foreach($footerPolicies as $fPolicy)
                            @php
                                $fTitle = is_array($fPolicy->title)
                                    ? ($fPolicy->title[app()->getLocale()] ?? $fPolicy->title['en'] ?? reset($fPolicy->title))
                                    : $fPolicy->title;
                            @endphp
                            <li>
                                <a href="/{{ $fPolicy->slug }}" class="hover:text-[#111111] transition">
                                    {{ $fTitle }}
                                </a>
                            </li>
                        @endforeach
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


    @if(session()->has('dataLayerEvent'))
        @php
            $sessionEvent = session('dataLayerEvent');
        @endphp
        <script>
            // Clear ecommerce before session-flashed event (e.g. add_to_cart after redirect)
            window.dataLayer.push({ ecommerce: null });

            // Purchase dedup check for session-flashed events too
            (function() {
                var payload = {
                    eventName:     '{{ $sessionEvent['eventName'] }}',
                    eventId:       '{{ $sessionEvent['eventId'] }}',
                    ecommerceData: @json($sessionEvent['ecommerceData']),
                    userData:      @json($sessionEvent['userData'] ?? [])
                };

                if (payload.eventName === 'purchase' && payload.ecommerceData && payload.ecommerceData.transaction_id) {
                    var dedupKey = 'fired_purchase_' + payload.ecommerceData.transaction_id;
                    if (sessionStorage.getItem(dedupKey)) return;
                    sessionStorage.setItem(dedupKey, '1');
                }

                var push = { event: payload.eventName, event_id: payload.eventId, ecommerce: payload.ecommerceData };
                if (payload.userData && Object.keys(payload.userData).length > 0) {
                    push.user_data = payload.userData;
                }
                window.dataLayer.push(push);
            })();
        </script>
    @endif

    <livewire:storefront.newsletter-popup />
    <livewire:storefront.buy-now-modal />

    {{-- ── Floating WhatsApp Button (Global) ────────────────────────────── --}}
    <style>
        /* Pulse ring — sibling, not child, so it never distorts the button */
        @keyframes wa-pulse {
            0%   { transform: scale(1);   opacity: 0.6; }
            70%  { transform: scale(1.6); opacity: 0;   }
            100% { transform: scale(1.6); opacity: 0;   }
        }
        .wa-pulse-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #25D366;
            animation: wa-pulse 2s ease-out infinite;
            pointer-events: none;
            will-change: transform, opacity;
        }
        .wa-btn-wrap {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }
        .wa-btn-link {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #25D366;
            box-shadow: 0 4px 20px rgba(37,211,102,0.45);
            transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s ease;
            text-decoration: none;
        }
        .wa-btn-link:hover {
            transform: scale(1.12);
            box-shadow: 0 8px 30px rgba(37,211,102,0.55);
        }
        .wa-btn-link svg {
            width: 28px;
            height: 28px;
            fill: #fff;
            position: relative;
            z-index: 1;
            display: block;
            flex-shrink: 0;
        }
        /* Tooltip */
        .wa-tooltip {
            background: #111;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.04em;
            padding: 6px 12px;
            border-radius: 6px;
            white-space: nowrap;
            box-shadow: 0 2px 12px rgba(0,0,0,0.18);
            opacity: 0;
            transform: translateX(6px);
            transition: opacity 0.2s ease, transform 0.2s ease;
            pointer-events: none;
        }
        .wa-tooltip::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -5px;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-left-color: #111;
        }
        .wa-btn-wrap:hover .wa-tooltip {
            opacity: 1;
            transform: translateX(0);
        }
        @media (max-width: 767px) {
            .wa-tooltip { display: none; }
        }
    </style>

    <div class="wa-btn-wrap" role="complementary" aria-label="WhatsApp support" wire:persist="whatsapp-widget">
        <span class="wa-tooltip">Chat with us on WhatsApp</span>

        <a href="https://wa.me/923124995545?text=Hi%20KELVS!%20I%20have%20a%20question%20about%20your%20products."
           target="_blank"
           rel="noopener noreferrer"
           class="wa-btn-link"
           onclick="window.dataLayer = window.dataLayer || []; window.dataLayer.push({ event: 'whatsapp_click' });"
           aria-label="Chat with KELVS on WhatsApp">

            {{-- Pulse ring (sibling to icon, outside the icon flow) --}}
            <span class="wa-pulse-ring" aria-hidden="true"></span>

            {{-- Official WhatsApp logo path --}}
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </a>
    </div>
</body>
</html>
