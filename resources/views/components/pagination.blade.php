@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-2 mt-8 mb-4">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 md:px-4 py-2 text-gray-300 bg-white/50 border border-gray-100 rounded-xl cursor-default text-sm font-bold flex items-center gap-2">
                <span>🔙</span> السابق
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" 
               class="px-3 md:px-4 py-2 text-blue-700 bg-white border border-blue-100 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm hover:shadow-blue-200 text-sm font-bold flex items-center gap-2 group">
                <span class="group-hover:translate-x-1 transition-transform">🔙</span> السابق
            </a>
        @endif

        {{-- Page Numbers --}}
        <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-white/60 backdrop-blur-md border border-blue-50 rounded-2xl shadow-inner mx-2">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-2 py-1 text-blue-300 font-bold opacity-50">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-600 to-indigo-600 text-white border-2 border-white rounded-xl font-black shadow-lg shadow-blue-200">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="w-10 h-10 flex items-center justify-center bg-white text-blue-700 border border-blue-50 rounded-xl hover:bg-blue-50 transition font-bold shadow-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Mobile Indicator --}}
        <div class="sm:hidden px-4 py-2 bg-blue-50 text-blue-700 font-black rounded-xl border border-blue-100 text-xs">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" 
               class="px-3 md:px-4 py-2 text-blue-700 bg-white border border-blue-100 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm hover:shadow-blue-200 text-sm font-bold flex items-center gap-2 group">
                التالي <span class="group-hover:-translate-x-1 transition-transform">🔜</span>
            </a>
        @else
            <span class="px-3 md:px-4 py-2 text-gray-300 bg-white/50 border border-gray-100 rounded-xl cursor-default text-sm font-bold flex items-center gap-2">
                التالي <span>🔜</span>
            </span>
        @endif
    </nav>
@endif
