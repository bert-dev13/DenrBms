@if ($paginator->hasPages())
    <ul class="species-obs-pagination-list">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <li class="species-obs-pagination-item species-obs-pagination-item--disabled">
                <span class="species-obs-pagination-link" aria-disabled="true">&lsaquo; Previous</span>
            </li>
        @else
            <li class="species-obs-pagination-item">
                <a class="species-obs-pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">&lsaquo; Previous</a>
            </li>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="species-obs-pagination-item species-obs-pagination-item--disabled">
                    <span class="species-obs-pagination-link">{{ $element }}</span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="species-obs-pagination-item species-obs-pagination-item--active" aria-current="page">
                            <span class="species-obs-pagination-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="species-obs-pagination-item">
                            <a class="species-obs-pagination-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <li class="species-obs-pagination-item">
                <a class="species-obs-pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">Next &rsaquo;</a>
            </li>
        @else
            <li class="species-obs-pagination-item species-obs-pagination-item--disabled">
                <span class="species-obs-pagination-link" aria-disabled="true">Next &rsaquo;</span>
            </li>
        @endif
    </ul>
@endif
