@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="pager">
        <div class="pager-info mono">
            @if ($paginator->total() ?? false)
                {{ number_format($paginator->firstItem()) }}–{{ number_format($paginator->lastItem()) }}
                <span style="opacity:.6;">of</span> {{ number_format($paginator->total()) }}
            @else
                Page {{ $paginator->currentPage() }}
            @endif
        </div>

        <div class="pager-links">
            @if ($paginator->onFirstPage())
                <span class="pager-btn is-disabled" aria-disabled="true">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pager-btn" rel="prev">Prev</a>
            @endif

            @isset($elements)
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="pager-gap" aria-hidden="true">{{ $element }}</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="pager-num is-active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pager-num" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endisset

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pager-btn" rel="next">Next</a>
            @else
                <span class="pager-btn is-disabled" aria-disabled="true">Next</span>
            @endif
        </div>
    </nav>
@endif
