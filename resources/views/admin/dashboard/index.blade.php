@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Tổng quan hệ thống')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Tours Statistics Table -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thống kê Tours</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-center">Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Tổng Tours</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-dark">{{ $tourStats['total'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">100%</td>
                                </tr>
                                <tr>
                                    <td>Đã xuất bản</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $tourStats['published'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $tourStats['total'] > 0 ? round(($tourStats['published'] / $tourStats['total']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bản nháp</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $tourStats['draft'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $tourStats['total'] > 0 ? round(($tourStats['draft'] / $tourStats['total']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                <tr>
                                    <td>Đã hủy</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $tourStats['cancelled'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $tourStats['total'] > 0 ? round(($tourStats['cancelled'] / $tourStats['total']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.tours.index') }}" class="btn btn-outline-dark btn-sm">Quản lý Tours</a>
                </div>
            </div>
        </div>

        <!-- Bookings Statistics Table -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thống kê Bookings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-center">Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Tổng Bookings</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-dark">{{ $bookingStats['total'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">100%</td>
                                </tr>
                                <tr>
                                    <td>Đã xác nhận</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $bookingStats['confirmed'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $bookingStats['total'] > 0 ? round(($bookingStats['confirmed'] / $bookingStats['total']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                <tr>
                                    <td>Chờ xử lý</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $bookingStats['pending'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $bookingStats['total'] > 0 ? round(($bookingStats['pending'] / $bookingStats['total']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                <tr>
                                    <td>Đã hủy</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $bookingStats['cancelled'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $bookingStats['total'] > 0 ? round(($bookingStats['cancelled'] / $bookingStats['total']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-dark btn-sm">Quản lý Bookings</a>
                </div>
            </div>
        </div>
    </div>

@endsection
