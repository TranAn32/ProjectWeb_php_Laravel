@extends('admin.layouts.app')
@section('title', 'Reports')
@section('page_title', 'Báo cáo')
@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Báo cáo</li>
@endsection
@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 small align-items-end">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label mb-1">Từ ngày</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label mb-1">Đến ngày</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4 col-md-2">
                    <label class="form-label mb-1">Loại</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="revenue" @selected(request('type') === 'revenue')>Doanh thu</option>
                        <option value="bookings" @selected(request('type') === 'bookings')>Bookings</option>
                        <option value="users" @selected(request('type') === 'users')>Người dùng</option>
                    </select>
                </div>
                <div class="col-sm-4 col-md-2">
                    <button class="btn btn-secondary btn-sm w-100"><i class="fa fa-chart-line me-1"></i> Xem</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="kpi-card h-100">
                <h6>Doanh thu</h6>
                <div class="kpi-value">{{ $metrics['revenue'] ?? '0' }}</div>
                <div class="kpi-trend text-success"><i class="fa fa-arrow-up me-1"></i>+4%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card h-100">
                <h6>Bookings</h6>
                <div class="kpi-value">{{ $metrics['bookings'] ?? '0' }}</div>
                <div class="kpi-trend text-danger"><i class="fa fa-arrow-down me-1"></i>-1%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card h-100">
                <h6>Người dùng mới</h6>
                <div class="kpi-value">{{ $metrics['new_users'] ?? '0' }}</div>
                <div class="kpi-trend text-success"><i class="fa fa-arrow-up me-1"></i>+9%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card h-100">
                <h6>Tỷ lệ hủy</h6>
                <div class="kpi-value">{{ $metrics['cancel_rate'] ?? '0%' }}</div>
                <div class="kpi-trend text-muted"><i class="fa fa-minus me-1"></i>0%</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Biểu đồ @switch(request('type'))
                    @case('bookings')
                        Bookings
                    @break

                    @case('users')
                        Người dùng mới
                    @break

                    @default
                        Doanh thu
                @endswitch
            </h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary active">Ngày</button>
                <button class="btn btn-outline-secondary">Tuần</button>
                <button class="btn btn-outline-secondary">Tháng</button>
            </div>
        </div>
        <div class="card-body">
            <div
                class="ratio ratio-16x9 bg-light rounded d-flex align-items-center justify-content-center text-muted small">
                (Placeholder Chart)</div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Bảng dữ liệu</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ngày</th>
                        <th>Giá trị</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 7; $i++)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ now()->subDays($i)->format('d/m') }}</td>
                            <td>{{ rand(10, 99) }}</td>
                            <td><span class="text-muted small">Sample row</span></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white small text-muted">Hiển thị dữ liệu mẫu (placeholder)</div>
    </div>
@endsection
