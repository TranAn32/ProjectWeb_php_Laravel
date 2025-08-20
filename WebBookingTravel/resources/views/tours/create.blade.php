@extends('layouts.app')

@section('content')
<h2>Thêm Tour</h2>

@if ($errors->any())
    <div class="alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('tours.store') }}" method="POST" enctype="multipart/form-data" class="card">
    @csrf
    <label>Tên</label>
    <input type="text" name="name" value="{{ old('name') }}" required>

    <label>Giá</label>
    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required>

    <label>Số ngày</label>
    <input type="number" name="days" value="{{ old('days') }}" required>

    <label>Mô tả</label>
    <textarea name="description" required>{{ old('description') }}</textarea>

    <label>Danh mục</label>
    <select name="category_id" required>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id')==$category->id)>{{ $category->name }}</option>
        @endforeach
    </select>

    <label>Ảnh (jpg, jpeg, png, ≤ 2MB)</label>
    <input type="file" name="image" accept="image/png,image/jpeg">

    <button type="submit" class="btn-primary">Lưu</button>
</form>

<p><a href="{{ route('categories.index') }}">↩ Quay lại</a></p>
@endsection
