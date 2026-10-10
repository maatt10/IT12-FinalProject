@if ($paginator->hasPages() || $paginator->total() > 0)
    <div class="clean-pagination">
        <div class="pagination-info">
            Showing <strong>{{ $paginator->firstItem() ?? 0 }}</strong>–<strong>{{ $paginator->lastItem() ?? 0 }}</strong>
            of <strong>{{ $paginator->total() }}</strong>
            {{ $paginator->total() === 1 ? 'entry' : 'entries' }}
        </div>

        @if ($paginator->hasPages())
            <div class="pagination-controls">
                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <button type="button" class="page-btn" disabled>‹ Prev</button>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">‹ Prev</a>
                @endif

                <span class="page-indicator">
                    {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                </span>

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-btn">Next ›</a>
                @else
                    <button type="button" class="page-btn" disabled>Next ›</button>
                @endif
            </div>
        @endif
    </div>
@endif