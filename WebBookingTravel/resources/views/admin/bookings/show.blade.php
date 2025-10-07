@extends('admin.layouts.app')
@section('title', 'Chi tiết Booking #' . $booking->booking_id)
@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Chi tiết Booking #{{ $booking->booking_id }}</h1>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin Booking</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin cơ bản</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Mã Booking:</strong></td>
                                        <td>#{{ $booking->booking_id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày đặt:</strong></td>
                                        <td>{{ $booking->created_at ? $booking->created_at->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày khởi hành:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($booking->departure_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số người lớn:</strong></td>
                                        <td>{{ $booking->num_adults }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số trẻ em:</strong></td>
                                        <td>{{ $booking->num_children }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tổng tiền:</strong></td>
                                        <td><strong
                                                class="text-primary">{{ number_format($booking->total_price, 0, ',', '.') }}đ</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Thông tin khách hàng</h6>
                                @if ($booking->user)
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Tên:</strong></td>
                                            <td>{{ $booking->user->userName ?? $booking->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $booking->user->email }}</td>
                                        </tr>
                                        @if ($booking->user->phoneNumber)
                                            <tr>
                                                <td><strong>Điện thoại:</strong></td>
                                                <td>{{ $booking->user->phoneNumber }}</td>
                                            </tr>
                                        @endif
                                        @if ($booking->user->address)
                                            <tr>
                                                <td><strong>Địa chỉ:</strong></td>
                                                <td>{{ $booking->user->address }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                @else
                                    <p class="text-muted">Khách hàng đã bị xóa</p>
                                @endif
                            </div>
                        </div>

                        @if ($booking->special_request)
                            <div class="mt-3">
                                <h6>Yêu cầu đặc biệt</h6>
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
                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Chờ xử lý
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
