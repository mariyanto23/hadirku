@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="min-w-0">
        <div class="flex items-center justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex min-h-10 items-center rounded-xl border border-slate-200 bg-white/70 px-3 py-2 text-sm font-bold text-slate-400 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-500">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex min-h-10 items-center rounded-xl border border-slate-200 bg-white/80 px-3 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200 dark:hover:bg-slate-800">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex min-h-10 items-center rounded-xl border border-slate-200 bg-white/80 px-3 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200 dark:hover:bg-slate-800">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex min-h-10 items-center rounded-xl border border-slate-200 bg-white/70 px-3 py-2 text-sm font-bold text-slate-400 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-500">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden min-w-0 items-center justify-between gap-4 sm:flex">
            <p class="min-w-0 text-sm font-semibold leading-5 text-slate-500 dark:text-slate-400">
                Showing
                @if ($paginator->firstItem())
                    <span class="font-extrabold text-slate-800 dark:text-slate-100">{{ $paginator->firstItem() }}</span>
                    to
                    <span class="font-extrabold text-slate-800 dark:text-slate-100">{{ $paginator->lastItem() }}</span>
                @else
                    <span class="font-extrabold text-slate-800 dark:text-slate-100">{{ $paginator->count() }}</span>
                @endif
                of
                <span class="font-extrabold text-slate-800 dark:text-slate-100">{{ $paginator->total() }}</span>
                results
            </p>

            <span class="inline-flex shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-white/80 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Previous">
                        <span class="inline-flex min-h-10 min-w-10 cursor-not-allowed items-center justify-center border-r border-slate-200 px-3 py-2 text-sm font-extrabold text-slate-300 dark:border-slate-700 dark:text-slate-600" aria-hidden="true">
                            &lt;
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex min-h-10 min-w-10 items-center justify-center border-r border-slate-200 px-3 py-2 text-sm font-extrabold text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-blue-500/10 dark:hover:text-blue-300" aria-label="Previous">
                        &lt;
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="inline-flex min-h-10 min-w-10 items-center justify-center border-r border-slate-200 px-3 py-2 text-sm font-extrabold text-slate-400 dark:border-slate-700 dark:text-slate-500">
                                {{ $element }}
                            </span>
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="inline-flex min-h-10 min-w-10 items-center justify-center border-r border-slate-200 bg-blue-600 px-3 py-2 text-sm font-extrabold text-white dark:border-slate-700">
                                        {{ $page }}
                                    </span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex min-h-10 min-w-10 items-center justify-center border-r border-slate-200 px-3 py-2 text-sm font-extrabold text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-blue-500/10 dark:hover:text-blue-300" aria-label="Go to page {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex min-h-10 min-w-10 items-center justify-center px-3 py-2 text-sm font-extrabold text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-blue-500/10 dark:hover:text-blue-300" aria-label="Next">
                        &gt;
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Next">
                        <span class="inline-flex min-h-10 min-w-10 cursor-not-allowed items-center justify-center px-3 py-2 text-sm font-extrabold text-slate-300 dark:text-slate-600" aria-hidden="true">
                            &gt;
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif
