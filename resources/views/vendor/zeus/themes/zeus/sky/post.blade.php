<div class="bg-white">

    <!-- Post Hero -->
    <section class="bg-[#f5f0eb] py-14 px-4">
        <div class="max-w-3xl mx-auto">
            <a href="{{ route('blogs') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-[#111111] transition mb-6">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Journal
            </a>
            <h1 class="text-3xl sm:text-4xl font-bold text-[#111111] leading-tight mb-6 capitalize">
                {{ $post->title }}
            </h1>
            <div class="flex items-center gap-4">
                <img src="{{ \Filament\Facades\Filament::getUserAvatarUrl($post->author) }}"
                     alt="{{ $post->author->name ?? '' }}"
                     class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                <div>
                    <p class="text-sm font-semibold text-[#111111]">{{ $post->author->name ?? '' }}</p>
                    <p class="text-xs text-gray-500">{{ optional($post->published_at)->format('d M Y') ?? '' }}</p>
                </div>
                <span class="ml-auto text-xs text-gray-400">{{ optional($post->published_at)->diffForHumans() ?? '' }}</span>
            </div>
        </div>
    </section>

    <!-- Featured Image -->
    @if($post->image() !== null)
    <div class="max-w-4xl mx-auto px-4 -mt-0">
        <img alt="{{ $post->title }}" src="{{ $post->image() }}"
             class="w-full h-72 sm:h-96 object-cover rounded-2xl shadow-md mt-8"/>
    </div>
    @endif

    <!-- Post Content -->
    <div class="max-w-3xl mx-auto px-4 py-12">

        <!-- Tags -->
        @unless($post->tags->isEmpty())
        <div class="flex flex-wrap gap-2 mb-8">
            @foreach($post->tags->where('type','category') as $category)
                <a href="{{ route('tags',['category', $category->slug]) }}"
                   class="text-xs font-semibold uppercase tracking-wider bg-[#f5f0eb] text-[#111111] px-3 py-1.5 rounded-full hover:bg-[#e8dcd2] transition">
                    {{ $category->name }}
                </a>
            @endforeach
            @foreach($post->tags->where('type','tag') as $tag)
                <a href="{{ route('tags',['tag', $tag->slug]) }}"
                   class="text-xs font-semibold uppercase tracking-wider bg-gray-100 text-gray-600 px-3 py-1.5 rounded-full hover:bg-gray-200 transition">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>
        @endunless

        <!-- Description -->
        @if($post->description)
        <p class="text-xl text-gray-600 leading-relaxed mb-8 font-light border-l-4 border-[#e8dcd2] pl-5">
            {{ $post->description }}
        </p>
        @endif

        <!-- Body Content -->
        <div class="prose prose-lg max-w-none
            prose-headings:text-[#111111] prose-headings:font-bold
            prose-p:text-gray-600 prose-p:leading-relaxed
            prose-a:text-[#111111] prose-a:underline
            prose-strong:text-[#111111]
            prose-img:rounded-xl">
            {!! $post->getContent() !!}
        </div>
    </div>

    <!-- Related Posts -->
    @if($related->isNotEmpty())
    <div class="border-t border-gray-100 bg-gray-50/60 py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-xs font-bold uppercase tracking-[0.3em] text-gray-400 mb-8">You Might Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($related as $post)
                    @include($skyTheme.'.partial.related')
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
