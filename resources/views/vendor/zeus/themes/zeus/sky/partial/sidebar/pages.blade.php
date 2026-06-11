@unless($pages->isEmpty())
<div class="bg-white border border-gray-100 rounded-2xl p-5">
    <h4 class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400 mb-4">Pages</h4>
    <ul class="space-y-1">
        @foreach($pages as $post)
        <li>
            <a href="{{ route('page', $post->slug) }}"
               class="flex items-center text-sm text-gray-600 hover:text-[#111111] py-2 border-b border-gray-50 last:border-0 transition group">
                <svg class="w-3.5 h-3.5 mr-2 text-gray-400 group-hover:text-[#111111] transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {!! $post->title !!}
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endunless
