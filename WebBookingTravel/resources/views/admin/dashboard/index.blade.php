@extends('admin.layouts.app')
@section('title','Dashboard')
@section('page_title','Dashboard')
@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="panel">Tổng tour: {{ $stats['tours'] ?? 0 }}</div>
    </div>
    <div class="col-md-3">
        <div class="panel">Đơn đặt: {{ $stats['bookings'] ?? 0 }}</div>
    </div>
    <div class="col-md-3">
        {{-- Pruned promotions module --}}
    </div>
    <div class="col-md-3">
        <div class="panel">Người dùng: {{ $stats['users'] ?? 0 }}</div>
    </div>
</div>
<canvas id="salesChart" height="140"></canvas>
@push('scripts')
<script>
    // placeholder chart
</script>
@endpush
@endsection