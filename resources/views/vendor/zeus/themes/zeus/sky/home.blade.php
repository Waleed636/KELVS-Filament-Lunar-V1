<div class="bg-white">

    <!-- Blog Hero Banner -->
    <section class="bg-[#f5f0eb] py-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-gray-400 mb-4">KELVS Journal</span>
            <h1 class="text-4xl sm:text-5xl font-bold text-[#111111] mb-4">Skin Science &amp; Stories</h1>
            <p class="text-gray-500 max-w-xl mx-auto leading-relaxed">Expert-led guides, ingredient deep-dives, and skincare routines built for real results.</p>
        </div>
    </section>

    <!-- Sticky/Featured Posts -->
    @unless($stickies->isEmpty())
    <section class="py-12 px-4 border-b border-gray-100">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-xs font-bold uppercase tracking-[0.3em] text-gray-400 mb-8">Featured</h2>
            <div class="grid grid-cols-1 @if($stickies->count() == 2) md:grid-cols-2 @elseif($stickies->count() >= 3) md:grid-cols-3 @endif gap-6">
                @foreach($stickies as $post)
                    @include($skyTheme.'.partial.sticky')
                @endforeach
            </div>
        </div>
    </section>
    @endunless

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        @if($posts->isEmpty())
            <div class="max-w-2xl mx-auto">
                @if(request()->filled('search'))
                    <div class="flex items-center gap-2 mb-8 p-4 bg-[#f5f0eb] rounded-xl">
                        <p class="text-sm text-gray-600">
                            Showing results for: <span class="font-bold text-[#111111]">{{ request('search') }}</span>
                        </p>
                        <a href="{{ route('blogs') }}" title="Clear search"
                           class="ml-auto text-xs font-semibold text-gray-500 hover:text-[#111111] border border-gray-300 px-3 py-1 rounded-lg transition">
                            Clear
                        </a>
                    </div>
                @endif

                @include($skyTheme.'.partial.empty')
            </div>
        @else
            <div class="flex flex-col lg:flex-row gap-12">

                <!-- Posts Column -->
                <main class="flex-1 min-w-0">
                    @if(request()->filled('search'))
                        <div class="flex items-center gap-2 mb-8 p-4 bg-[#f5f0eb] rounded-xl">
                            <p class="text-sm text-gray-600">
                                Showing results for: <span class="font-bold text-[#111111]">{{ request('search') }}</span>
                            </p>
                            <a href="{{ route('blogs') }}" title="Clear search"
                               class="ml-auto text-xs font-semibold text-gray-500 hover:text-[#111111] border border-gray-300 px-3 py-1 rounded-lg transition">
                                Clear
                            </a>
                        </div>
                    @endif

                    <div class="space-y-6">
                        @each($skyTheme.'.partial.post', $posts, 'post')
                    </div>
                </main>

                <!-- Sidebar -->
                <aside class="w-full lg:w-72 flex-shrink-0">
                    @include($skyTheme.'.partial.sidebar')
                </aside>

            </div>
        @endif
    </div>

</div>
