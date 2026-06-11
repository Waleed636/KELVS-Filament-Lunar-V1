@unless($tags->isEmpty())
<div class="bg-white border border-gray-100 rounded-2xl p-5">
    <h4 class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400 mb-4">Categories</h4>
    <ul class="space-y-1">
        @foreach($tags as $tag)
        <li>
            <a href="{{ route('tags',['category', $tag->slug]) }}"
               class="flex items-center justify-between text-sm text-gray-600 hover:text-[#111111] py-2 border-b border-gray-50 last:border-0 transition group">
                <span class="group-hover:translate-x-0.5 transition-transform">{{ $tag->name }}</span>
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                    {{ $tag->posts_published_count }}
                </span>
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endunless
