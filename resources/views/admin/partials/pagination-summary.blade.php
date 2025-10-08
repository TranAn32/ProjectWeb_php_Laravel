@if ($paginator->hasPages())
    @if (isset($paginator) && $paginator instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div class="d-flex justify-content-between align-items-center py-2"> @php($from = ($paginator->currentPage() - 1) * $paginator->perPage() + 1)

            <div class="text-muted small"> @php($to = min($paginator->currentPage() * $paginator->perPage(), $paginator->total()))

                Hiển thị {{ $paginator->firstItem() }} đến {{ $paginator->lastItem() }} <div
                    class="d-flex flex-wrap align-items-center justify-content-between gap-2 small text-muted mt-2">

                    trong tổng số {{ $paginator->total() }} kết quả <div>Hiển thị <strong>{{ $from }}</strong> -
                        <strong>{{ $to }}</strong> /

                    </div> <strong>{{ $paginator->total() }}</strong> bản ghi</div>

                <div>
                    <div class="ms-auto">{!! $paginator->links() !!}</div>

                    {{ $paginator->links('pagination::bootstrap-4') }}
                </div>

            </div>
    @endif

    </div>
@else
    <div class="py-2">
        <div class="text-muted small text-center">
            @if ($paginator->total() > 0)
                Hiển thị tất cả {{ $paginator->total() }} kết quả
            @else
                Không có kết quả nào
            @endif
        </div>
    </div>
@endif
