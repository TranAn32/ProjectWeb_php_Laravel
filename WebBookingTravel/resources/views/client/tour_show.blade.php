@extends('client.layouts.app')
@section('title', $tour->title ?? 'Tour Detail')
@section('content')
<div class="container py-4">
    <h1>{{ $tour->title }}</h1>
    <p><strong>Category:</strong> {{ $tour->category->categoryName ?? 'N/A' }}</p>
    <p><strong>Price:</strong> {{ number_format($tour->priceAdult ?? 0,0,',','.') }} VND</p>
    <div class="row g-3 mb-3">
        @php $imgs = $tour->images; if (is_string($imgs)) $imgs = json_decode($imgs,true); @endphp
        @if(is_array($imgs))
        @foreach($imgs as $img)
        @php $src = is_array($img) ? ($img['url'] ?? null) : (is_string($img) ? $img : null); @endphp
        @if($src)
        <div class="col-md-3"><img src="{{ $src }}" class="img-fluid rounded" alt="image"></div>
        @endif
        @endforeach
        @endif
    </div>
    <a href="{{ route('client.tours.index') }}" class="btn btn-secondary">Back to list</a>
</div>
@endsection