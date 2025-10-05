@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Tổng quan')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection
@section('content')
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card h-100">
                <h6>Tổng Tours</h6>
                <div class="d-flex align-items-end justify-content-between">
                    <div class="kpi-value">{{ $stats['tours'] ?? 0 }}</div>
                    <div class="kpi-trend text-success"><i class="fa fa-arrow-up me-1"></i>+5%</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card h-100">
                <h6>Bookings</h6>
                <div class="d-flex align-items-end justify-content-between">
                    <div class="kpi-value">{{ $stats['bookings'] ?? 0 }}</div>
                    <div class="kpi-trend text-danger"><i class="fa fa-arrow-down me-1"></i>-2%</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card h-100">
                <h6>Người dùng</h6>
                <div class="d-flex align-items-end justify-content-between">
                    <div class="kpi-value">{{ $stats['users'] ?? 0 }}</div>
                    <div class="kpi-trend text-success"><i class="fa fa-arrow-up me-1"></i>+3%</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card h-100">
                <h6>Tỷ lệ Hủy</h6>
                <div class="d-flex align-items-end justify-content-between">
                    <div class="kpi-value">{{ $stats['cancel_rate'] ?? '0%' }}</div>
                    <div class="kpi-trend text-muted"><i class="fa fa-minus me-1"></i>0%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Hiệu suất Doanh thu</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-secondary active">7N</button>
                        <button class="btn btn-outline-secondary">30N</button>
                        <button class="btn btn-outline-secondary">90N</button>
                    </div>
                </div>
                <div class="card-body">
                    <div
                        class="ratio ratio-21x9 bg-light rounded d-flex align-items-center justify-content-center text-muted small">
                        (Placeholder Chart)
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Bookings gần đây</h5>
                    <a href="{{ route('admin.bookings.index') }}" class="small">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tour</th>
                                <th>Khách</th>
                                <th>Tổng</th>
                                <th>Ngày</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($recentBookings ?? []) as $b)
                                <tr>
                                    <td>{{ $b->id }}</td>
                                    <td class="text-truncate" style="max-width:160px;">{{ $b->tour?->title }}</td>
                                    <td>{{ $b->customer_name ?? $b->customer_email }}</td>
                                    <td>{{ number_format($b->total_price) }}</td>
                                    <td><span class="text-muted small">{{ $b->created_at?->format('d/m') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Chưa có booking</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Người dùng mới</h5>
                </div>
                <ul class="list-group list-group-flush small">
                    @forelse(($recentUsers ?? []) as $u)
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar bg-secondary">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                                <div>
                                    <div class="fw-semibold">{{ $u->name }}</div>
                                    <div class="text-muted">{{ $u->email }}</div>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark">{{ $u->role ?? 'user' }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">Chưa có người dùng</li>
                    @endforelse
                </ul>
            </div>
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Hoạt động hệ thống</h5>
                </div>
                <div class="card-body small text-muted" style="max-height:220px; overflow:auto;">
                    <p class="mb-1"><i class="fa fa-check-circle text-success me-1"></i> Hệ thống hoạt động ổn định.</p>
                    <p class="mb-1"><i class="fa fa-database text-primary me-1"></i> Sao lưu gần nhất: hôm nay 02:00.</p>
                    <p class="mb-1"><i class="fa fa-shield-halved text-warning me-1"></i> Không có cảnh báo bảo mật.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
