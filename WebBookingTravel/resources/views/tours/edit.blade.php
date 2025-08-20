@extends('layouts.app')

@section('content')
<h2>Sửa Tour</h2>
<form action="{{ route('tours.update', $tour->id) }}" method="POST" enctype="multipart/form-data" class="card">
    @csrf
    @method('PUT')
    <label>Tên:</label>
    <input type="text" name="name" value="{{ $tour->name }}" required><br>
    <label>Giá:</label>
    <input type="number" step="0.01" name="price" value="{{ $tour->price }}" required><br>
    <label>Số ngày:</label>
    <input type="number" name="days" value="{{ $tour->days }}" required><br>
    <label>Mô tả:</label>
    <textarea name="description" required>{{ $tour->description }}</textarea><br>
    <label>Category:</label>
    <select name="category_id" required>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @if($category->id == $tour->category_id) selected @endif>{{ $category->name }}</option>
        @endforeach
    </select><br>

    @if($tour->image_path)
        <div style="margin:10px 0;">
            <img src="{{ asset('storage/' . $tour->image_path) }}" alt="{{ $tour->name }}" width="200">
        </div>
    @endif

    <label>Ảnh mới (tuỳ chọn)</label>
    <input type="file" name="image" accept="image/png,image/jpeg"><br>

    <button type="submit" class="btn-primary">Cập nhật</button>
</form>
<a href="{{ route('categories.show', $tour->category_id) }}">Quay lại</a>
@endsection
