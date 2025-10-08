@extends('admin.layouts.app')

@section('title', 'Bookings')
@section('page_title', 'Bookings')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Bookings</li>
    <li class="breadcrumb-item active">Chi tiết Booking #{{ $booking->booking_id }}</li>
@endsection
<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        color: #6c757d;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .back-link:hover {
        color: #495057;
        border-color: #adb5bd;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        min-width: 140px;
    }

    .info-value {
        color: #212529;
        text-align: right;
        flex: 1;
    }
</style>
@section('content')
    <div class="container-fluid py-4" style="padding-top: 0 !important;">
        <a href="{{ route('admin.bookings.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin Booking</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Thông tin cơ bản</h6>
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="info-label">Mã Booking:</span>
                                        <span class="info-value">#{{ $booking->booking_id }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Ngày đặt:</span>
                                        <span
                                            class="info-value">{{ $booking->created_at ? $booking->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Ngày khởi hành:</span>
                                        <span
                                            class="info-value">{{ \Carbon\Carbon::parse($booking->departure_date)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Số người lớn:</span>
                                        <span class="info-value">{{ $booking->num_adults }} người</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Số trẻ em:</span>
                                        <span class="info-value">{{ $booking->num_children }} trẻ</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Tổng tiền:</span>
                                        <span
                                            class="info-value text-success fw-bold">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Điểm đón:</span>
                                        <span class="info-value">{{ $booking->pickup_point ?? 'Chưa có thông tin' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Số điện thoại:</span>
                                        <span class="info-value">{{ $booking->phone_number ?? 'Chưa có thông tin' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Thông tin khách hàng</h6>
                                @if ($booking->user)
                                    <div class="info-list">
                                        <div class="info-item">
                                            <span class="info-label">Tên:</span>
                                            <span
                                                class="info-value">{{ $booking->user->userName ?? $booking->user->name }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Email:</span>
                                            <span class="info-value">{{ $booking->user->email }}</span>
                                        </div>
                                        @if ($booking->user->phoneNumber)
                                            <div class="info-item">
                                                <span class="info-label">Điện thoại (Profile):</span>
                                                <span class="info-value">{{ $booking->user->phoneNumber }}</span>
                                            </div>
                                        @endif
                                        @if ($booking->user->address)
                                            <div class="info-item">
                                                <span class="info-label">Địa chỉ:</span>
                                                <span class="info-value">{{ $booking->user->address }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-muted">Khách hàng đã bị xóa</p>
                                @endif
                            </div>
                        </div>

                        @if ($booking->special_request)
                            <div class="mt-4">
                                <h6 class="mb-3">Yêu cầu đặc biệt</h6>
                                <div class="alert alert-light">{{ $booking->special_request }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin Tour</h5>
                    </div>
                    <div class="card-body">
                        @if ($booking->tour)
                            @php
                                $imgSrc = $booking->tour->image_path ?: asset('assets/images/destinations/dest1.jpg');
                            @endphp
                            <img src="{{ $imgSrc }}" alt="{{ $booking->tour->title }}"
                                class="img-fluid rounded mb-3" style="width:100%;height:200px;object-fit:cover;">
                            <h6>{{ $booking->tour->title }}</h6>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $booking->tour->departurePoint ?? 'Chưa rõ' }}
                            </p>
                            @if ($booking->tour->days)
                                <p class="text-muted mb-2">
                                    <i class="fas fa-clock me-1"></i> {{ $booking->tour->days }} ngày
                                </p>
                            @endif
                            <div class="d-grid">
                                <a href="{{ route('admin.tours.edit', $booking->tour->tourID) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i> Chỉnh sửa tour
                                </a>
                            </div>
                        @else
                            <p class="text-muted">Tour đã bị xóa</p>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Cập nhật trạng thái</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking->booking_id) }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">Trạng thái hiện tại:</label>
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Chờ xử
                                        lý
                                    </option>
                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Đã
                                        xác nhận</option>
                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Đã
                                        hủy</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Cập nhật
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
