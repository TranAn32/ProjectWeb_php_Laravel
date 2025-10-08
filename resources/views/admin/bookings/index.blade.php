@php use Illuminate\Support\Str; @endphp
@extends('admin.layouts.app')

@section('title', 'Bookings')
@section('page_title', 'Bookings')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Bookings</li>
@endsection

@push('styles')
    <style>
        /* Booking index specific styles */
        .btn-icon {
            --btn-size: 32px;
            width: var(--btn-size);
            height: var(--btn-size);
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d7dce1;
            font-size: 13px;
        }

        .btn-icon:hover {
            background: #f1f4f7;
        }

        .btn-icon.text-danger:hover {
            background: #fdecea;
            border-color: #f5c1bb;
        }

        .btn-icon.text-success:hover {
            background: #e8f5e8;
            border-color: #b8e6b8;
        }

        .btn-icon.text-warning:hover {
            background: #fff3cd;
            border-color: #ffeaa7;
        }

        .booking-actions {
            display: flex;
            gap: 6px;
        }

        .booking-actions .btn-icon {
            --btn-size: 30px;
            font-size: 12px;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(13, 36, 55, 0.08);
            border: 1px solid rgba(13, 36, 55, 0.06);
            margin-bottom: 24px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .payment-badge {
            padding: 3px 6px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 500;
        }

        .payment-unpaid {
            background: #ffeaa7;
            color: #d63031;
        }

        .payment-paid {
            background: #a8e6cf;
            color: #00b894;
        }

        .payment-refunded {
            background: #ddd;
            color: #636e72;
        }
    </style>
@endpush

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="" class="row g-2 align-items-end small">
                <div class="col-sm-8 col-md-3 order-1">
                    <input
                        style="border: 1.5px solid #e1e8ed; border-radius: 10px; transition: all 0.2s ease; background: white; font-family: 'Inter', sans-serif;"
                        type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                        placeholder="Tìm theo tour, khách hàng...">
                </div>
                <div class="col-sm-4 col-md-2 order-2">
                    <label class="form-label mb-1">Trạng thái booking</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                
                <div class="col-sm-4 col-md-2 order-4">
                    <label class="form-label mb-1">Từ ngày</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4 col-md-2 order-5">
                    <label class="form-label mb-1">Đến ngày</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4 col-md-1 order-5">
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
                        <th style="width:68px;">ID</th>
                        <th style="width:90px;">Tour</th>
                        <th>Khách hàng</th>
                        <th style="width:120px;">Ngày đặt</th>
                        <th style="width:120px;">Ngày đi</th>
                        <th style="width:100px;">Số người</th>
                        <th style="width:120px;">Tổng tiền</th>
                        <th style="width:150px;">Điểm đón</th>
                        <th style="width:120px;">Điện thoại</th>
                        <th style="width:120px;">Trạng thái</th>
                        {{-- <th style="width:100px;">Thanh toán</th> --}}
                        <th style="width:140px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $imgSrc = $booking->tour->image_path ?? secure_asset('assets/images/destinations/dest1.jpg');
                        @endphp
                        <tr>
                            <td class="text-muted small">#{{ $booking->booking_id }}</td>
                            <td>
                                @if ($booking->tour)
                                    <img src="{{ $imgSrc }}" alt="{{ $booking->tour->title ?? '' }}" loading="lazy"
                                        style="width:72px;height:54px;object-fit:cover;border-radius:6px;border:1px solid #e2e6ea;"
                                        onerror="this.onerror=null;this.src='{{ secure_asset('assets/images/destinations/dest1.jpg') }}';">
                                @else
                                    <div class="text-muted small">Không có ảnh</div>
                                @endif
                            </td>
                            <td style="max-width:200px;">
                                @if ($booking->user)
                                    <div>
                                        <div class="fw-medium text-truncate">
                                            {{ $booking->user->userName ?? $booking->user->name }}</div>
                                        <small class="text-muted">{{ $booking->user->email }}</small>
                                        @if ($booking->tour)
                                            <div class="small text-primary">
                                                {{ Str::limit($booking->tour->title ?? '', 25) }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">Khách vãng lai</span>
                                @endif
                            </td>
                            <td>
                                @if ($booking->booking_date)
                                    @php
                                        $bookingDate = is_string($booking->booking_date)
                                            ? \Carbon\Carbon::parse($booking->booking_date)
                                            : $booking->booking_date;
                                    @endphp
                                    <div class="small">{{ $bookingDate->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $bookingDate->format('H:i') }}</small>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($booking->departure_date)
                                    <div class="small fw-medium">
                                        {{ \Carbon\Carbon::parse($booking->departure_date)->format('d/m/Y') }}</div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="small">
                                    <span class="badge bg-primary">{{ $booking->num_adults ?? 0 }} NL</span>
                                    @if (($booking->num_children ?? 0) > 0)
                                        <br><span class="badge bg-info">{{ $booking->num_children }} TE</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="fw-semibold">{{ number_format($booking->total_price ?? 0) }} đ</div>
                            </td>
                            <td style="max-width:150px;">
                                <div class="small text-truncate" title="{{ $booking->pickup_point ?? '' }}">
                                    {{ $booking->pickup_point ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="small">{{ $booking->phone_number ?? '-' }}</div>
                            </td>
                            <td>
                                @php($status = $booking->status ?? 'pending')
                                <span class="status-badge status-{{ $status }}">
                                    @switch($status)
                                        @case('confirmed')
                                            xác nhận
                                        @break

                                        @case('cancelled')
                                            Đã hủy
                                        @break

                                        @default
                                            Đang chờ
                                    @endswitch
                                </span>
                            </td>
                            {{-- <td>
                                @php($paymentStatus = $booking->payment_status ?? 'unpaid')
                                <span class="payment-badge payment-{{ $paymentStatus }}">
                                    @switch($paymentStatus)
                                        @case('paid')
                                            Đã thanh toán
                                        @break

                                        @case('refunded')
                                            Đã hoàn tiền
                                        @break

                                        @default
                                            Chưa thanh toán
                                    @endswitch
                                </span>
                            </td> --}}
                            <td>
                                <div class="booking-actions">
                                    <a href="{{ route('admin.bookings.show', $booking->booking_id) }}"
                                        class="btn btn-light btn-icon" data-bs-toggle="tooltip" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if ($booking->status === 'pending')
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->booking_id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-light btn-icon text-success"
                                                data-bs-toggle="tooltip" title="Xác nhận"
                                                onclick="return confirm('Xác nhận booking này?')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if ($booking->status !== 'cancelled')
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->booking_id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-light btn-icon text-warning"
                                                data-bs-toggle="tooltip" title="Hủy booking"
                                                onclick="return confirm('Hủy booking này?')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}"
                                        method="POST" class="d-inline" onsubmit="return confirm('Xóa booking này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-light btn-icon text-danger" data-bs-toggle="tooltip"
                                            title="Xóa">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-0">
                                    <div class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <div>Chưa có booking nào</div>
                                            <small>Hãy chờ khách hàng đặt tour</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Hiển thị {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }}
                        trong tổng số {{ $bookings->total() }} booking
                    </div>
                    @if ($bookings->hasPages())
                        {{ $bookings->withQueryString()->links() }}
                    @endif
                </div>
            </div>
        </div>
    @endsection
