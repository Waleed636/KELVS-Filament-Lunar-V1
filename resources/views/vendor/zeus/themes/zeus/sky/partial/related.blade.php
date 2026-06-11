<a href="{{ route('post', $post->slug) }}"
   class="group relative block rounded-2xl overflow-hidden aspect-[4/3] bg-gray-100 hover:shadow-md transition duration-300">

    @if($post->image() !== null)
    <img alt="{{ $post->title }}" src="{{ $post->image() }}"
         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500 z-0"/>
    @else
    <div class="absolute inset-0 bg-[#f5f0eb] z-0"></div>
    @endif

    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent z-10"></div>

    <!-- Content -->
    <div class="absolute bottom-0 left-0 right-0 p-5 z-20">
        <h3 class="text-white font-bold text-lg leading-snug line-clamp-2 group-hover:underline">
            {{ $post->title ?? '' }}
        </h3>
        <div class="flex items-center gap-2 mt-2">
            <img src="{{ \Filament\Facades\Filament::getUserAvatarUrl($post->author) }}"
                 class="w-6 h-6 rounded-full object-cover border border-white/30" alt="">
            <p class="text-white/70 text-xs">{{ $post->author->name ?? '' }}</p>
            <span class="text-white/40 text-xs ml-auto">{{ optional($post->published_at)->diffForHumans() }}</span>
        </div>
    </div>
</a>
