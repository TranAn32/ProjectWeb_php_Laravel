@extends('admin.layouts.app')
@section('title','Bookings')
@section('page_title','Quản lý Bookings')
@section('content')
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tour</th>
            <th>Khách</th>
            <th>Số lượng</th>
            <th>Tổng</th>
            <th>Ngày</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bookings as $b)
        <tr>
            <td>{{ $b->id }}</td>
            <td>{{ $b->tour?->title }}</td>
            <td>{{ $b->customer_name ?? $b->customer_email }}</td>
            <td>{{ $b->quantity }}</td>
            <td>{{ number_format($b->total_price) }}</td>
            <td>{{ $b->created_at }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">Chưa có booking</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $bookings->links() }}
@endsection