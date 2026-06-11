@unless($recent->isEmpty())
<div class="bg-white border border-gray-100 rounded-2xl p-5">
    <h4 class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400 mb-4">Recent Posts</h4>
    <ul class="space-y-3">
        @foreach($recent as $post)
        <li>
            <a href="{{ route('post', $post->slug) }}"
               class="flex gap-3 group items-start py-2 border-b border-gray-50 last:border-0">
                @if($post->image() !== null)
                <img alt="{{ $post->title }}" src="{{ $post->image() }}"
                     class="w-12 h-12 rounded-lg object-cover flex-shrink-0 group-hover:opacity-80 transition"/>
                @else
                <div class="w-12 h-12 rounded-lg bg-[#f5f0eb] flex-shrink-0 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-medium text-[#111111] group-hover:opacity-75 transition line-clamp-2 leading-snug">
                        {{ $post->title ?? '' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ optional($post->published_at)->format('d M Y') }}</p>
                </div>
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endunless
