@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Điều hướng phân trang') }}">
        
        {{-- Mobile pagination --}}
        <div class="flex items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-xl">
                    {!! __('Trước') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-primary bg-primary/10 border border-primary/20 rounded-xl hover:bg-primary/20 transition-all">
                    {!! __('Trước') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-primary bg-primary/10 border border-primary/20 rounded-xl hover:bg-primary/20 transition-all">
                    {!! __('Sau') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-xl">
                    {!! __('Sau') !!}
                </span>
            @endif
        </div>

        {{-- Desktop pagination --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">
                    {!! __('Hiển thị') !!}
                    @if ($paginator->firstItem())
                        <span class="font-bold text-gray-900">{{ $paginator->firstItem() }}</span>
                        {!! __('đến') !!}
                        <span class="font-bold text-gray-900">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('trong tổng số') !!}
                    <span class="font-bold text-primary">{{ $paginator->total() }}</span>
                    {!! __('kết quả') !!}
                </p>
            </div>

            <div>
                <span class="inline-flex items-center gap-1.5 shadow-sm rounded-xl bg-white border border-gray-100 p-1.5">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('Trang trước') }}">
                            <span class="inline-flex items-center justify-center w-9 h-9 text-gray-300 cursor-not-allowed rounded-lg" aria-hidden="true">
                                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 text-gray-600 rounded-lg hover:bg-gray-100 hover:text-primary transition-all" aria-label="{{ __('Trang trước') }}">
                            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-400 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-white bg-primary rounded-lg shadow-md cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-semibold text-gray-600 rounded-lg hover:bg-primary/10 hover:text-primary transition-all" aria-label="{{ __('Đến trang :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 text-gray-600 rounded-lg hover:bg-gray-100 hover:text-primary transition-all" aria-label="{{ __('Trang sau') }}">
                            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('Trang sau') }}">
                            <span class="inline-flex items-center justify-center w-9 h-9 text-gray-300 cursor-not-allowed rounded-lg" aria-hidden="true">
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
