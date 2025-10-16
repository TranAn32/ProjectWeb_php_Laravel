@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Tổng quan hệ thống')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    @if(($pendingCount ?? 0) > 0)
        <div class="alert alert-warning d-flex align-items-center justify-content-between" role="alert">
            <div>
                <i class="bi bi-bell-fill me-2"></i>
                Có <strong>{{ $pendingCount }}</strong> booking đang chờ xác nhận.
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-dark">Xem ngay</a>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="kpi-card">
                <h6>Tổng Tours</h6>
                <div class="kpi-value">{{ $totalTours ?? ($tourStats['total'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <h6>Tổng Danh mục</h6>
                <div class="kpi-value">{{ $totalCategories ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <h6>Bookings (Đã xác nhận)</h6>
                <div class="kpi-value">{{ $bookingStats['confirmed'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <h6>Tỷ lệ hủy 30 ngày</h6>
                <div class="kpi-value">{{ $cancellationRate ?? 0 }}%</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Doanh thu (Đã xác nhận) 6 tháng gần đây</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">Top Tours được đặt</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tour</th>
                                    <th class="text-end">Đơn</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($topTours ?? []) as $index => $t)
                                    @php($rank = $index + 1)
                                    @php($rankClass = $rank === 1 ? 'bg-warning text-dark' : ($rank === 2 ? 'bg-secondary' : ($rank === 3 ? 'bg-info' : 'bg-light text-dark')))
                                    <tr>
                                        <td><span class="badge {{ $rankClass }}" style="min-width:28px">{{ $rank }}</span></td>
                                        <td>{{ $t['title'] }}</td>
                                        <td class="text-end"><span class="badge bg-primary">{{ $t['count'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">Chưa có dữ liệu</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Xác nhận gần đây</h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Tour</th>
                                    <th class="text-end">Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($recentConfirmed ?? []) as $b)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Carbon::parse($b->bookingDate ?? $b->created_at)->format('d/m/Y') }}</td>
                                        <td>{{ $b->tour->title ?? ('Tour #'.$b->tour_id) }}</td>
                                        <td class="text-end"><span class="badge bg-success">{{ number_format((float)($b->totalPrice ?? $b->total_price ?? 0), 0, ',', '.') }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">Không có dữ liệu</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Hủy gần đây</h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Tour</th>
                                    <th class="text-end">Số người</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($recentCancelled ?? []) as $b)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Carbon::parse($b->bookingDate ?? $b->created_at)->format('d/m/Y') }}</td>
                                        <td>{{ $b->tour->title ?? ('Tour #'.$b->tour_id) }}</td>
                                        <td class="text-end"><span class="badge bg-danger">{{ (int)($b->numAdults ?? $b->num_adults ?? 0) + (int)($b->numChildren ?? $b->num_children ?? 0) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">Không có dữ liệu</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function(){
                var labels = @json(collect($revenueSeries ?? [])->pluck('label'));
                var revenue = @json(collect($revenueSeries ?? [])->pluck('value'));
                var statusLabels = @json(collect($monthlyStatus ?? [])->pluck('label'));
                var confirmedData = @json(collect($monthlyStatus ?? [])->pluck('confirmed'));
                var cancelledData = @json(collect($monthlyStatus ?? [])->pluck('cancelled'));
                var topTourNames = @json(collect($topTours ?? [])->pluck('title'));
                var topTourCounts = @json(collect($topTours ?? [])->pluck('count'));

                // Line chart - Revenue
                var revenueCtx = document.getElementById('revenueChart');
                if (revenueCtx) {
                    new Chart(revenueCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Doanh thu (VND)',
                                data: revenue,
                                borderColor: '#1f78b4',
                                backgroundColor: 'rgba(31,120,180,0.15)',
                                tension: 0.25,
                                fill: true,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { labels: { color: '#17354d' } }
                            },
                            scales: {
                                x: { ticks: { color: '#17354d' } },
                                y: { beginAtZero: true, ticks: { color: '#17354d' } }
                            }
                        }
                    });
                }

                // Bar chart - Top tours
                var topToursCtx = document.getElementById('topToursChart');
                if (topToursCtx) {
                    new Chart(topToursCtx, {
                        type: 'bar',
                        data: {
                            labels: topTourNames,
                            datasets: [{
                                label: 'Số booking',
                                data: topTourCounts,
                                backgroundColor: ['#e41a1c','#377eb8','#4daf4a','#984ea3','#ff7f00'],
                                borderColor: '#17354d'
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }

                // Doughnut - Booking status distribution
                var statusCtx = document.getElementById('statusChart');
                if (statusCtx) {
                    new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Xác nhận','Chờ','Hủy'],
                            datasets: [{
                                data: [{{ $bookingStats['confirmed'] ?? 0 }}, {{ $bookingStats['pending'] ?? 0 }}, {{ $bookingStats['cancelled'] ?? 0 }}],
                                backgroundColor: ['#4caf50','#ffc107','#f44336']
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }

                // Grouped bar - Monthly confirmed vs cancelled
                var monthlyCtx = document.getElementById('monthlyStatusChart');
                if (monthlyCtx) {
                    new Chart(monthlyCtx, {
                        type: 'bar',
                        data: {
                            labels: statusLabels,
                            datasets: [
                                { label: 'Xác nhận', data: confirmedData, backgroundColor: '#4caf50' },
                                { label: 'Hủy', data: cancelledData, backgroundColor: '#f44336' },
                            ]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }
            })();
        </script>
    @endpush

@endsection
