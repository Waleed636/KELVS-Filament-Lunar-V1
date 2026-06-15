@php
    $image = $post->image();
    $hasImage = false;
    $imageUrl = '';
    
    if ($image) {
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            $hasImage = true;
            $imageUrl = $image;
        } elseif (file_exists(public_path($image))) {
            $hasImage = true;
            $imageUrl = asset($image);
        } elseif (file_exists(public_path('storage/' . $image))) {
            $hasImage = true;
            $imageUrl = asset('storage/' . $image);
        }
    }
@endphp

<article class="group bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-200 hover:shadow-md transition duration-300">

    @if($hasImage)
    <a href="{{ route('post', $post->slug) }}">
        <img alt="{{ $post->title }}" src="{{ $imageUrl }}"
             class="w-full h-48 object-cover group-hover:scale-105 transition duration-500"/>
    </a>
    @endif

    <div class="p-6">
        <!-- Meta row -->
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-gray-400 font-medium">
                {{ optional($post->published_at)->format('d M Y') ?? '' }}
            </span>
            <div class="flex gap-1.5">
                @unless($post->tags->isEmpty())
                    @foreach($post->tags->where('type','category') as $category)
                        <a href="{{ route('tags',['category', $category->slug]) }}"
                           class="text-[10px] font-bold uppercase tracking-wider bg-[#f5f0eb] text-[#111111] px-2.5 py-1 rounded-full hover:bg-[#e8dcd2] transition">
                            {{ $category->name }}
                        </a>
                    @endforeach
                @endunless
            </div>
        </div>

        <!-- Title -->
        <a href="{{ route('post', $post->slug) }}"
           class="block text-xl font-bold text-[#111111] hover:opacity-75 transition leading-snug mb-2">
            {!! $post->title !!}
        </a>

        <!-- Description -->
        @if($post->description !== null)
        <p class="text-sm text-gray-500 leading-relaxed line-clamp-2 mb-4">
            {!! $post->description !!}
        </p>
        @endif

        <!-- Author + CTA -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
            <div class="flex items-center gap-2">
                <img src="{{ \Filament\Facades\Filament::getUserAvatarUrl($post->author) }}"
                     alt="avatar"
                     class="w-7 h-7 rounded-full object-cover">
                <span class="text-xs text-gray-500 font-medium">{{ $post->author->name ?? '' }}</span>
            </div>
            <a href="{{ route('post', $post->slug) }}"
               class="text-xs font-semibold text-[#111111] hover:opacity-70 transition flex items-center gap-1">
                Read →
            </a>
        </div>
    </div>
</article>
