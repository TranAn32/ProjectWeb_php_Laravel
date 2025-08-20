@extends('layouts.app')

@section('content')
<h2>Sửa Category</h2>
<form action="{{ route('categories.update', $category->id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Tên:</label>
    <input type="text" name="name" value="{{ $category->name }}" required>
    <button type="submit">Cập nhật</button>
</form>
<a href="{{ route('categories.index') }}">Quay lại</a>
@endsection
