@if (isset($paginator) && $paginator instanceof \Illuminate\Contracts\Pagination\Paginator)
    @php($from = ($paginator->currentPage() - 1) * $paginator->perPage() + 1)
    @php($to = min($paginator->currentPage() * $paginator->perPage(), $paginator->total()))
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 small text-muted mt-2">
        <div>Hiển thị <strong>{{ $from }}</strong> - <strong>{{ $to }}</strong> /
            <strong>{{ $paginator->total() }}</strong> bản ghi</div>
        <div class="ms-auto">{!! $paginator->links() !!}</div>
    </div>
@endif
