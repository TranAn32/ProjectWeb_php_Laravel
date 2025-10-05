@extends('admin.layouts.app')
@section('title', 'Bookings')
@section('page_title', 'Bookings')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Bookings</li>
@endsection
@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 small align-items-end">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label mb-1">Từ khóa</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Tour hoặc khách">
                </div>
                <div class="col-sm-4 col-md-2">
                    <label class="form-label mb-1">Ngày từ</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4 col-md-2">
                    <label class="form-label mb-1">Đến ngày</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4 col-md-2">
                    <label class="form-label mb-1">Sắp xếp</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="new" @selected(request('sort') === 'new')>Mới nhất</option>
                        <option value="old" @selected(request('sort') === 'old')>Cũ nhất</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá cao</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá thấp</option>
                    </select>
                </div>
                <div class="col-sm-4 col-md-2">
                    <button class="btn btn-secondary btn-sm w-100"><i class="fa fa-filter me-1"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>Tour</th>
                        <th>Khách</th>
                        <th style="width:100px;">Số lượng</th>
                        <th style="width:140px;">Tổng</th>
                        <th style="width:150px;">Ngày</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        <tr>
                            <td class="text-muted small">#{{ $b->id }}</td>
                            <td class="text-truncate" style="max-width:200px;">{{ $b->tour?->title }}</td>
                            <td>{{ $b->customer_name ?? $b->customer_email }}</td>
                            <td>{{ $b->quantity }}</td>
                            <td>{{ number_format($b->total_price) }}</td>
                            <td><span class="small text-muted">{{ $b->created_at?->format('d/m/Y H:i') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">@include('admin.partials.empty', [
                                '__data' => [
                                    'empty_title' => 'Chưa có booking',
                                    'empty_message' => 'Khi khách đặt tour sẽ xuất hiện ở đây',
                                ],
                            ])</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            @include('admin.partials.pagination-summary', ['paginator' => $bookings])
        </div>
    </div>
@endsection
