@extends('admin.layouts.app')
@section('title', ($tour->exists ? 'Sửa' : 'Thêm').' Tour')
@section('page_title', ($tour->exists ? 'Sửa' : 'Thêm').' Tour')
@section('content')
<form method="post" action="{{ $tour->exists ? route('admin.tours.update',$tour->tourID) : route('admin.tours.store') }}" enctype="multipart/form-data">
    @csrf
    @if($tour->exists) @method('PUT') @endif
    <div class="mb-3">
        <label>Tiêu đề</label>
        <input type="text" name="title" value="{{ old('title',$tour->title) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Giá người lớn (tùy chọn)</label>
        <input type="number" name="priceAdult" value="{{ old('priceAdult',$tour->priceAdult) }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Giá trẻ em (tùy chọn)</label>
        <input type="number" name="priceChild" value="{{ old('priceChild',$tour->priceChild) }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Trạng thái</label>
        <select name="status" class="form-control">
            @php($status = old('status',$tour->status))
            <option value="draft" {{ $status==='draft'?'selected':'' }}>Nháp</option>
            <option value="published" {{ $status==='published'?'selected':'' }}>Công khai</option>
            <option value="canceled" {{ $status==='canceled'?'selected':'' }}>Hủy</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Điểm khởi hành</label>
        <input type="text" name="departurePoint" value="{{ old('departurePoint',$tour->departurePoint) }}" class="form-control">
    </div>

    <div class="mb-3">
        <label>Ảnh (JSON)</label>
        <textarea name="images" class="form-control" rows="3" placeholder='[{"url":"https://...","description":"..."}]'>{{ old('images', is_string($tour->images)? $tour->images : json_encode($tour->images)) }}</textarea>
    </div>
    <div class="mb-3">
        <label>Itinerary (JSON)</label>
        <textarea name="itinerary" class="form-control" rows="3" placeholder='[{"day":1,"activity":"..."}]'>{{ old('itinerary', is_string($tour->itinerary)? $tour->itinerary : json_encode($tour->itinerary)) }}</textarea>
    </div>
    <div class="mb-3">
        <label>Hotels (JSON)</label>
        <textarea name="hotels" class="form-control" rows="3" placeholder='[{"name":"...","rating":5}]'>{{ old('hotels', is_string($tour->hotels)? $tour->hotels : json_encode($tour->hotels)) }}</textarea>
    </div>
    <div class="mb-3">
        <label>Mô tả</label>
        <textarea name="description" class="form-control" rows="4">{{ old('description',$tour->description) }}</textarea>
    </div>
    <button class="btn btn-success">Lưu</button>
</form>
@endsection