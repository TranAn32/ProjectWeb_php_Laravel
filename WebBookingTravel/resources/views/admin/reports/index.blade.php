@extends('admin.layouts.app')
@section('title','Reports')
@section('page_title','Báo cáo')
@section('content')
<h4>Thống kê doanh thu</h4>
<canvas id="reportRevenue" height="140"></canvas>
@push('scripts')
<script>
    // placeholder chart init (Chart.js load assumed elsewhere)
</script>
@endpush
@endsection