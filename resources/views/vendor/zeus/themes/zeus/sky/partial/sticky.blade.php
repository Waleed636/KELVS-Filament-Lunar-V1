@php
    $image = $post->image();
    $hasImage = false;
    $imageUrl = '';
    
    // Check if the image exists and resolve its path
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

    // Curated deep-toned luxury gradients matching KELVS skincare brand identity
    $gradients = [
        'from-[#141517] via-[#2c1d15] to-[#402e23]', // Deep warm coffee/espresso
        'from-[#1b1918] via-[#2d2724] to-[#4c3e38]', // Cocoa & dark charcoal
        'from-[#121315] via-[#232422] to-[#3a3530]', // Dark earth & slate
        'from-[#1a1412] via-[#2d1f1b] to-[#473027]', // Copper & dark auburn
    ];
    $gradientClass = $gradients[$post->id % count($gradients)];
@endphp

<div class="w-full flex">
    <a href="{{ route('post', $post->slug) }}" 
       class="group relative w-full h-[20rem] sm:h-[24rem] rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col justify-end p-6 border border-gray-100/10">
        
        <!-- Background Asset (Image or Gradient Fallback) -->
        @if($hasImage)
            <img alt="{{ $post->title }}" src="{{ $imageUrl }}" 
                 class="absolute inset-0 w-full h-full object-cover z-0 transition-transform duration-500 group-hover:scale-105"/>
            <!-- Dark Gradient Overlay for Typography Contrast -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent z-10"></div>
        @else
            <!-- Luxury Dynamic Fallback Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br {{ $gradientClass }} z-0"></div>
            <!-- Subtle Overlay Texture for depth -->
            <div class="absolute inset-0 bg-black/20 z-10"></div>
        @endif

        <!-- Content Area -->
        <div class="relative z-20 w-full flex flex-col">
            <!-- Category Tag (if exists) -->
            @unless($post->tags->isEmpty())
                <div class="mb-3 flex flex-wrap gap-1.5">
                    @foreach($post->tags->where('type','category') as $category)
                        <span class="text-[9px] font-bold uppercase tracking-widest bg-white/20 backdrop-blur-md text-white border border-white/10 px-2 py-0.5 rounded">
                            {{ $category->name }}
                        </span>
                    @endforeach
                </div>
            @endunless

            <!-- Title -->
            <h2 class="text-xl sm:text-2xl font-bold text-white leading-snug group-hover:text-gray-100 transition-colors">
                {!! $post->title !!}
            </h2>

            <!-- Excerpt/Description (if exists) -->
            @if($post->description)
                <p class="text-xs sm:text-sm text-gray-300/90 leading-relaxed mt-2 line-clamp-2">
                    {{ strip_tags($post->description) }}
                </p>
            @endif

            <!-- Author & Date Profile Row -->
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-white/10">
                <img src="{{ \Filament\Facades\Filament::getUserAvatarUrl($post->author) }}" 
                     alt="avatar" 
                     class="h-8 w-8 rounded-full border border-white/20 object-cover"/>
                <div>
                    <p class="text-xs font-semibold text-white/90">{{ $post->author->name ?? 'Waleed' }}</p>
                    <p class="text-[10px] font-medium text-gray-400">{{ optional($post->published_at)->diffForHumans() ?? '' }}</p>
                </div>
                <span class="ml-auto text-xs font-bold text-white/80 group-hover:translate-x-1 transition-transform duration-300">
                    Read →
                </span>
            </div>
        </div>
    </a>
</div>
