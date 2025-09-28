@extends('layouts.app')

@section('content')
<h2>Category: {{ $category->name }}</h2>
@if(!empty($category->description))
<p class="muted">{{ $category->description }}</p>
@endif
<p><a href="{{ route('tours.create') }}" class="btn-primary">+ Thêm Tour</a></p>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
    @forelse($category->tours as $tour)
    <div class="card" style="padding:0">
        <div style="width:100%;height:150px;overflow:hidden;border-top-left-radius:8px;border-top-right-radius:8px;background:#f0f0f0;">
            @if($tour->image_path)
            <img src="{{ asset('storage/' . $tour->image_path) }}" alt="{{ $tour->name }}" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy">
            @else
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#888;">No Image</div>
            @endif
        </div>
        <div style="padding:12px;display:flex;flex-direction:column;gap:6px;">
            <h3 style="margin:0">{{ $tour->name }}</h3>
            <div style="font-size:14px;color:#333;">Giá: <strong>${{ $tour->price }}</strong> • {{ $tour->days }} ngày</div>
            <p style="margin:0;color:#444;">{{ $tour->description }}</p>
            <div style="display:flex;gap:8px;margin-top:8px;align-items:center;">
                <a href="{{ route('tours.edit', $tour->id) }}">Sửa</a>
                <form action="{{ route('tours.destroy', $tour->id) }}" method="POST" onsubmit="return confirm('Xóa tour này?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#ef4444;color:#fff;border:none;padding:6px 10px;border-radius:6px;cursor:pointer">Xóa</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <p>Chưa có tour nào trong danh mục này.</p>
    @endforelse
</div>

<p style="margin-top:16px"><a href="{{ route('categories.index') }}">↩ Quay lại danh sách Category</a></p>
@endsection