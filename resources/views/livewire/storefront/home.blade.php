<div class="relative overflow-hidden min-h-screen">
    
    <!-- Decorative background glow -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>
    <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-orange-600/10 rounded-full blur-3xl -z-10 pointer-events-none"></div>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32 flex flex-col items-center text-center">
        <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight">
            Elevate Your Style with <br>
            <span class="bg-gradient-to-r from-amber-400 via-orange-500 to-amber-500 bg-clip-text text-transparent">
                KELVS Premium Collection
            </span>
        </h1>
        <p class="mt-6 text-lg md:text-xl text-slate-400 max-w-2xl leading-relaxed">
            Experience e-commerce redefined. Discover handpicked, state-of-the-art products curated for the modern lifestyle, running on blazing-fast servers.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row gap-4">
            <a href="#products" class="px-8 py-3.5 bg-gradient-to-r from-amber-400 to-orange-500 hover:from-amber-300 hover:to-orange-400 text-slate-950 font-bold rounded-lg shadow-lg hover:shadow-amber-500/20 hover:scale-[1.02] transition duration-300">
                Shop the Collection
            </a>
            <a href="/sky" class="px-8 py-3.5 border border-slate-800 bg-slate-900/40 hover:bg-slate-900 text-slate-200 font-semibold rounded-lg hover:border-slate-700 transition">
                Read Our Story
            </a>
        </div>
    </section>

    <!-- Categories / Collections -->
    @if($collections->count() > 0)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-slate-900">
            <h2 class="text-2xl font-bold tracking-tight text-slate-100 mb-8">Browse Collections</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($collections as $collection)
                    <a href="/collections/{{ $collection->translate('slug') }}" class="group relative block aspect-video rounded-xl overflow-hidden bg-slate-900 border border-slate-800 shadow-md">
                        <!-- Glow overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent z-10"></div>
                        
                        <!-- Text -->
                        <div class="absolute bottom-4 left-4 z-20">
                            <h3 class="text-lg font-bold text-slate-200 group-hover:text-amber-400 transition">
                                {{ $collection->translate('name') }}
                            </h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Featured Products -->
    <section id="products" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-slate-900">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-100">Featured Products</h2>
                <p class="text-sm text-slate-500 mt-1">Explore our latest arrivals and bestsellers.</p>
            </div>
        </div>

        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($products as $product)
                    @php
                        $variant = $product->variants->first();
                        $price = $variant?->prices->first();
                        $formattedPrice = $price ? $price->price->formatted : 'N/A';
                    @endphp
                    <div class="group relative flex flex-col bg-slate-900/50 border border-slate-900 rounded-xl overflow-hidden hover:border-slate-800 hover:bg-slate-900/80 transition duration-300">
                        <!-- Thumbnail Container -->
                        <a href="/products/{{ $product->urls->first()?->slug }}" wire:navigate class="aspect-square w-full bg-slate-950 flex items-center justify-center relative overflow-hidden">
                            @if($product->thumbnail)
                                <img src="{{ $product->thumbnail->getUrl('medium') ?? $product->thumbnail->getUrl() }}" alt="{{ $product->attr('name') }}" class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
                            @else
                                <!-- Fallback SVG -->
                                <svg class="w-12 h-12 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            @endif
                        </a>

                        <!-- Details -->
                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-300 hover:text-amber-400 transition line-clamp-1">
                                    <a href="/products/{{ $product->urls->first()?->slug }}" wire:navigate>
                                        {{ $product->attr('name') }}
                                    </a>
                                </h3>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-lg font-bold text-slate-100">
                                        {{ $formattedPrice }}
                                    </span>
                                    @if($variant && $variant->stock < 5)
                                        <span class="text-[10px] uppercase tracking-wider font-semibold text-orange-400 bg-orange-950/30 px-2 py-0.5 rounded border border-orange-900/50">
                                            Low Stock
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- CTA -->
                            <div class="mt-6">
                                <a href="/products/{{ $product->urls->first()?->slug }}" wire:navigate class="block w-full py-2.5 text-center text-xs font-semibold text-slate-200 hover:text-slate-950 bg-slate-800 hover:bg-amber-400 rounded-lg border border-slate-700/50 hover:border-amber-400 transition duration-300">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-slate-900/20 rounded-xl border border-dashed border-slate-850">
                <svg class="w-12 h-12 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-slate-350 font-medium">No Products Found</h3>
                <p class="text-xs text-slate-500 mt-1">Visit your Seller Portal/Admin to upload products.</p>
            </div>
        @endif
    </section>

    <!-- Latest Blogs / CMS Content -->
    @if($posts->count() > 0)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-slate-900">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-100">Latest from our Blog</h2>
                    <p class="text-sm text-slate-500 mt-1">Read the latest guides, trends, and articles.</p>
                </div>
                <a href="/sky" class="text-sm font-semibold text-amber-400 hover:text-amber-300 transition">
                    View All &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article class="flex flex-col bg-slate-900/30 border border-slate-900 rounded-xl overflow-hidden hover:border-slate-850 transition">
                        <!-- Cover Image if any -->
                        @if($post->hasMedia('posts'))
                            <div class="aspect-video w-full overflow-hidden bg-slate-950">
                                <img src="{{ $post->getFirstMediaUrl('posts') }}" alt="{{ $post->title }}" class="object-cover w-full h-full">
                            </div>
                        @endif

                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-500">
                                    {{ $post->published_at?->format('M d, Y') ?? $post->created_at->format('M d, Y') }}
                                </span>
                                <h3 class="mt-2 text-lg font-bold text-slate-200 hover:text-amber-400 transition">
                                    <a href="/sky/post/{{ $post->slug }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="mt-3 text-xs text-slate-400 leading-relaxed line-clamp-3">
                                    {{ strip_tags($post->description ?? $post->content) }}
                                </p>
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-slate-900">
                                <a href="/sky/post/{{ $post->slug }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300 transition">
                                    Read Article &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

</div>
