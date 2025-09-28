@extends('layouts.app')

@section('content')
<h2>Thêm Category</h2>
<form action="{{ route('categories.store') }}" method="POST">
    @csrf
    <label>Tên:</label>
    <input type="text" name="name" required>
    <br>
    <label>Mô tả:</label>
    <input type="text" name="description">
    <button type="submit">Lưu</button>
</form>
<a href="{{ route('categories.index') }}">Quay lại</a>
@endsection