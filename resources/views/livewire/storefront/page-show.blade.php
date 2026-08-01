@php
    $pageTitle = is_array($page->title)
        ? ($page->title[app()->getLocale()] ?? $page->title['en'] ?? reset($page->title))
        : $page->title;

    $pageContent = is_array($page->content)
        ? ($page->content[app()->getLocale()] ?? $page->content['en'] ?? reset($page->content))
        : $page->content;

    $pageDesc = is_array($page->description)
        ? ($page->description[app()->getLocale()] ?? $page->description['en'] ?? reset($page->description) ?? '')
        : ($page->description ?? '');

    // Calculate reading time estimate
    $wordCount = str_word_count(strip_tags($pageContent));
    $readingTime = max(1, ceil($wordCount / 200));
@endphp

<div class="bg-[#fafafa] min-h-screen py-10 lg:py-16 text-[#111111]" x-data="{ searchQuery: '' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs & Trust Badge Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-gray-200/80">
            <nav class="flex items-center text-xs font-medium text-gray-500 space-x-2" aria-label="Breadcrumb">
                <a href="/" class="hover:text-[#111111] transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Home
                </a>
                <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 5l7 7-7 7" /></svg>
                <span class="text-gray-400">Policies & Terms</span>
                <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 5l7 7-7 7" /></svg>
                <span class="text-[#111111] font-bold tracking-wide">{{ $pageTitle }}</span>
            </nav>

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200/60 w-fit">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Official Store Policy & Standard
            </div>
        </div>

        <!-- Main Layout: Sidebar Navigation + Policy Document -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
            
            <!-- Left Sticky Sidebar (Policy Navigation) -->
            <aside class="lg:col-span-4 xl:col-span-3">
                <div class="bg-white border border-gray-200/90 rounded-2xl p-5 shadow-xs sticky top-24 space-y-6">
                    
                    <div>
                        <h3 class="text-xs uppercase font-black tracking-widest text-[#111111] mb-3 flex items-center justify-between">
                            <span>Policy Documents</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 bg-gray-100 rounded-full text-gray-600">{{ count($allPages) }}</span>
                        </h3>

                        <!-- Search Filter inside Sidebar -->
                        <div class="relative mb-3">
                            <input type="text"
                                   x-model="searchQuery"
                                   placeholder="Filter policies..."
                                   class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#111111] focus:bg-white text-gray-800 placeholder-gray-400 transition" />
                            <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <!-- Policy Links List -->
                        <ul class="space-y-1">
                            @foreach($allPages as $item)
                                @php
                                    $itemTitle = is_array($item->title)
                                        ? ($item->title[app()->getLocale()] ?? $item->title['en'] ?? reset($item->title))
                                        : $item->title;
                                    $isActive = ($item->slug === $slug);
                                @endphp
                                <li x-show="searchQuery === '' || '{{ strtolower($itemTitle) }}'.includes(searchQuery.toLowerCase())">
                                    <a href="/{{ $item->slug }}"
                                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ $isActive ? 'bg-[#111111] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-[#111111]' }}">
                                        <span class="truncate">{{ $itemTitle }}</span>
                                        @if($isActive)
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </aside>

            <!-- Right Main Area: Policy Document Content -->
            <main class="lg:col-span-8 xl:col-span-9">
                <article class="bg-white border border-gray-200/90 rounded-2xl p-6 sm:p-10 shadow-xs space-y-8">
                    
                    <!-- Document Header Section -->
                    <header class="border-b border-gray-100 pb-8">
                        <div class="flex flex-wrap items-center gap-3 mb-3 text-xs text-gray-500">
                            <span class="px-2.5 py-1 rounded-md bg-stone-100 font-bold text-[#111111]">
                                KELVS Legal Standard
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $readingTime }} min read
                            </span>
                            @if(!empty($page->updated_at))
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-400">Updated: {{ \Carbon\Carbon::parse($page->updated_at)->format('F j, Y') }}</span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-extrabold text-[#111111] tracking-tight">
                            {{ $pageTitle }}
                        </h1>

                        @if(!empty($pageDesc))
                            <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed max-w-3xl">
                                {{ $pageDesc }}
                            </p>
                        @endif
                    </header>

                    <!-- Document Body (Rich Text) -->
                    <div class="prose prose-stone max-w-none 
                                prose-headings:font-bold prose-headings:text-[#111111] 
                                prose-h2:text-xl prose-h2:mt-8 prose-h2:mb-4 prose-h2:pb-2 prose-h2:border-b prose-h2:border-gray-100
                                prose-h3:text-lg prose-h3:mt-6 prose-h3:mb-3
                                prose-p:text-gray-600 prose-p:leading-relaxed prose-p:text-sm sm:prose-p:text-base
                                prose-li:text-gray-600 prose-li:text-sm sm:prose-li:text-base
                                prose-strong:text-[#111111] prose-strong:font-bold
                                prose-blockquote:border-l-4 prose-blockquote:border-emerald-500 prose-blockquote:bg-emerald-50/50 prose-blockquote:p-4 prose-blockquote:rounded-r-lg prose-blockquote:not-italic">
                        {!! $pageContent !!}
                    </div>

                    <!-- Footer Feedback & Help Action -->
                    <div class="pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Official KELVS policy documentation. Valid for all online purchases.</span>
                        </div>

                        <button onclick="window.scrollTo({ top: 0, behavior: 'smooth' })" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 font-semibold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            Back to Top
                        </button>
                    </div>

                </article>
            </main>

        </div>
    </div>
</div>
