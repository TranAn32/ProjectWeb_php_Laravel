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
        <label>Giá người lớn</label>
        <input type="number" name="priceAdult" value="{{ old('priceAdult',$tour->priceAdult) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Loại tour</label>
        <select name="tourType" class="form-control" required>
            <option value="domestic" @selected(old('tourType',$tour->tourType)=='domestic')>Trong nước</option>
            <option value="international" @selected(old('tourType',$tour->tourType)=='international')>Nước ngoài</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Điểm khởi hành</label>
        <input type="text" name="departurePoint" value="{{ old('departurePoint',$tour->departurePoint) }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Điểm đến</label>
        <input type="text" name="destinationPoint" value="{{ old('destinationPoint',$tour->destinationPoint) }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Mô tả</label>
        <textarea name="description" class="form-control" rows="4">{{ old('description',$tour->description) }}</textarea>
    </div>
    <button class="btn btn-success">Lưu</button>
</form>
@endsection