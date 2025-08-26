@extends('admin.layouts.app')
@section('title','Promotions')
@section('page_title','Khuyến mãi')
@section('content')
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Giảm (%)</th>
            <th>Hiệu lực</th>
        </tr>
    </thead>
    <tbody>
        @forelse($promotions as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->name }}</td>
            <td>{{ $p->discount_percent }}</td>
            <td>{{ $p->start_date }} - {{ $p->end_date }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">Chưa có khuyến mãi</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $promotions->links() }}
@endsection