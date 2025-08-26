@extends('admin.layouts.app')
@section('title','Media')
@section('page_title','Thư viện ảnh')
@section('content')
<form method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mb-4">@csrf
    <input type="file" name="images[]" multiple>
    <button class="btn btn-primary">Upload</button>
</form>
<div class="row">
    @forelse($media as $m)
    <div class="col-md-2 mb-3">
        <img src="{{ $m->url }}" alt="img" style="width:100%;height:90px;object-fit:cover;border:1px solid #ddd;">
    </div>
    @empty
    <p class="text-muted">Chưa có ảnh.</p>
    @endforelse
</div>
@endsection