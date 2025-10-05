@extends('admin.layouts.app')
@section('title', 'Media')
@section('page_title', 'Thư viện ảnh')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Media</li>
@endsection
@section('page_actions')
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal"><i
            class="fa fa-upload me-1"></i> Upload</button>
@endsection
@section('content')
    <div class="row g-3">
        @forelse($media as $m)
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <div class="card h-100 shadow-sm position-relative">
                    <img src="{{ $m->url }}" class="card-img-top" alt="media"
                        style="height:120px;object-fit:cover;">
                    <div class="card-body p-2 small">
                        <div class="text-truncate" title="{{ $m->file_name ?? '' }}">{{ $m->file_name ?? '(ảnh)' }}</div>
                        <div class="text-muted text-truncate">{{ number_format($m->size ?? 0) }}B</div>
                    </div>
                    <div class="position-absolute top-0 end-0 p-1">
                        <button class="btn btn-light btn-sm border" title="Sao chép URL"
                            onclick="navigator.clipboard.writeText('{{ $m->url }}')"><i
                                class="fa fa-copy"></i></button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">@include('admin.partials.empty', [
                '__data' => ['empty_title' => 'Chưa có ảnh', 'empty_message' => 'Hãy upload để thêm ảnh mới'],
            ])</div>
        @endforelse
    </div>

    <!-- Pagination placeholder (nếu dùng paginator) -->
    @if (method_exists($media, 'links'))
        <div class="mt-3">{!! $media->links() !!}</div>
    @endif

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Upload ảnh</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Chọn ảnh</label>
                            <input type="file" name="images[]" class="form-control" multiple required>
                        </div>
                        <p class="small text-muted mb-0">Có thể chọn nhiều ảnh một lúc.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-primary"><i class="fa fa-upload me-1"></i> Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
