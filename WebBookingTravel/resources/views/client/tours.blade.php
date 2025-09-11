@extends('client.layouts.app')
@section('content')
<h1>Danh sách Tour</h1>
<div class="grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
    @foreach($tours as $tour)
    <div class="card" style="border:1px solid #ddd;padding:12px;border-radius:8px;">
        <div style="height:140px;overflow:hidden;margin-bottom:8px;">
            @php $img = $tour->image_path; @endphp
            @if($img)
            <img src="{{ \Illuminate\Support\Str::startsWith($img,['http','https']) ? $img : asset('storage/'.$img) }}" alt="{{ $tour->title }}" style="width:100%;height:100%;object-fit:cover;" />
            @else
            <div style="background:#f3f3f3;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:12px;">No Image</div>
            @endif
        </div>
        <strong>{{ $tour->title }}</strong>
        <div>Giá NL: {{ number_format($tour->priceAdult) }}đ</div>
        <div>Danh mục: {{ $tour->category->categoryName ?? '-' }}</div>
    </div>
    @endforeach
</div>
<div style="margin-top:16px;">{{ $tours->links() }}</div>
@endsection