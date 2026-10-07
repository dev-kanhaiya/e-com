@if ($paginator->hasPages())
    <nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
        <div class="pagination-info">
            Showing <span>{{ $paginator->firstItem() }}</span> to <span>{{ $paginator->lastItem() }}</span> of <span>{{ $paginator->total() }}</span> results
        </div>

        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span>&lsaquo; Prev</span>
                </li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo; Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span>{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span>{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next">Next &rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span>Next &rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
