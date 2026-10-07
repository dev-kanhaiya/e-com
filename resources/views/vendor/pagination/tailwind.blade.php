@if ($paginator->hasPages())
    <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0 mt-8" aria-label="Pagination">
        
        {{-- Previous Page --}}
        <div class="-mt-px flex w-0 flex-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-400 cursor-not-allowed">
                    ← Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                    ← Previous
                </a>
            @endif
        </div>

        {{-- Page Numbers --}}
        <div class="hidden md:flex">
            @foreach ($elements as $element)

                @if (is_string($element))
                    <span class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center border-t-2 border-blue-600 px-4 pt-4 text-sm font-medium text-blue-600">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif

            @endforeach
        </div>

        {{-- Next Page --}}
        <div class="-mt-px flex w-0 flex-1 justify-end">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                    Next →
                </a>
            @else
                <span class="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-400 cursor-not-allowed">
                    Next →
                </span>
            @endif
        </div>

    </nav>

    {{-- Result Count --}}
    <p class="mt-3 text-center text-sm text-gray-500">
        Showing
        <span class="font-medium">{{ $paginator->firstItem() }}</span>
        to
        <span class="font-medium">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-medium">{{ $paginator->total() }}</span>
        results
    </p>
@endif