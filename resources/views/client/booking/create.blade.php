@extends('client.layouts.app')
@section('title', 'Đặt tour')
@section('content')
    <div class="container py-4">
        <style>
            .form-control {
                width: 100%;
                padding: 12px 16px;
                font-size: 15px;
                border: 1.5px solid #e1e8ed;
                border-radius: 10px;
                transition: all 0.2s ease;
                background: white;
                font-family: 'Inter', sans-serif;
            }

            .qty-group {
                width: 110px;
            }

            .back-link {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 5px 10px;
                background: white;
                border: 1px solid #dee2e6;
                border-radius: 6px;
                color: #6c757d;
                text-decoration: none;
                font-size: 14px;

            }

            .back-link:hover {
                color: #495057;
                border-color: #adb5bd;
            }
        </style>
        <div class="mb-3">
            <a href="{{ url()->previous() ?: route('client.tours.index') }}" class="back-link"><i
                    class="far fa-arrow-left me-1"></i> Quay lại</a>
        </div>

        <h1 class="h4 mb-3">Đặt tour</h1>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('client.booking.store') }}" method="POST" id="bookingForm">
                            @csrf
                            <input type="hidden" name="tour_id" value="{{ $tour->tourID ?? '' }}" />

                            @if (isset($tour) && $tour)
                                <div class="mb-3">
                                    <div class="d-flex gap-2 align-items-center">
                                        @php
                                            $imgSrc =
                                                $tour->image_path ?: asset('assets/images/destinations/dest1.jpg');
                                        @endphp
                                        <img src="{{ $imgSrc }}" alt="{{ $tour->title }}"
                                            style="width:120px;height:90px;object-fit:cover;border-radius:.75rem; " />
                                        <div>
                                            <div style="font-weight: 650; " class="fw-semibold fs-5">{{ $tour->title }}
                                            </div>
                                            <div style="margin-top: 20px;" class="text-muted">Khởi hành:
                                                {{ $tour->departurePoint ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">Bạn chưa chọn tour. Vui lòng quay lại trang danh sách và bấm
                                    Đặt tour.</div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ngày khởi hành</label>
                                    <input type="date" name="departure_date" class="form-control"
                                        value="{{ old('departure_date') }}" required />
                                    @error('departure_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Người lớn</label>
                                    <input type="number" name="num_adults" class="form-control" min="1"
                                        value="{{ old('num_adults', 1) }}" required />
                                    @error('num_adults')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Trẻ em</label>
                                    <input type="number" name="num_children" class="form-control" min="0"
                                        value="{{ old('num_children', 0) }}" />
                                    @error('num_children')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone_number" class="form-control"
                                        value="{{ old('phone_number', auth('web')->user()->phoneNumber ?? '') }}"
                                        placeholder="Ví dụ: 0987654321" required />
                                    @error('phone_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Điểm đón <span class="text-danger">*</span></label>
                                    <input type="text" name="pickup_point" class="form-control"
                                        placeholder="Ví dụ: 123 Nguyễn Huệ, Q1, TP.HCM" required />
                                    @error('pickup_point')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Yêu cầu đặc biệt</label>
                                <textarea name="special_request" class="form-control" rows="3" placeholder="Ví dụ: ăn chay, phòng gần cửa sổ...">{{ old('special_request') }}</textarea>
                                @error('special_request')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="pricingData" data-adult="{{ isset($tour) && $tour ? (int) ($tour->price ?? 0) : 0 }}"
                                data-child="{{ isset($tour) && $tour ? (int) (($tour->price ?? 0) * 0.8) : 0 }}" hidden>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin khách hàng</h5>
                        @auth('web')
                            @php $user = Auth::guard('web')->user(); @endphp
                            <div class="mb-2"><strong>Tên:</strong> {{ $user->userName ?? $user->name }}</div>
                            <div class="mb-2"><strong>Email:</strong> {{ $user->email }}</div>
                            @if ($user->phoneNumber ?? false)
                                <div class="mb-2"><strong>SĐT:</strong> {{ $user->phoneNumber }}</div>
                            @endif
                            @if ($user->address ?? false)
                                <div class="mb-2"><strong>Địa chỉ:</strong> {{ $user->address }}</div>
                            @endif
                        @else
                            <div class="alert alert-warning">Bạn cần đăng nhập để đặt tour.</div>
                        @endauth
                    </div>
                </div>

                @if (isset($tour) && $tour)
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Tóm tắt giá</h5>
                            @php
                                $adult = $tour->price ?? 0;
                                $child = ($tour->price ?? 0) * 0.8; // Trẻ em giảm 20%
                            @endphp
                            <ul class="list-unstyled small mb-2">
                                <li>Người lớn: <span id="sumAdults">1</span> x {{ number_format($adult, 0, ',', '.') }}đ
                                </li>
                                <li>Trẻ em: <span id="sumChildren">0</span> x {{ number_format($child, 0, ',', '.') }}đ
                                </li>
                            </ul>
                            <div class="fs-6 mt-1">Tạm tính: <strong
                                    id="sidebarTotal">{{ number_format($adult, 0, ',', '.') }}đ</strong></div>
                            <div class="text-muted small">(Chưa bao gồm phụ phí nếu có)</div>
                            <button class="btn btn-primary w-100 mt-3" type="submit" form="bookingForm"
                                @if (!isset($tour) || !$tour) disabled @endif>Đặt ngay</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('bookingForm');
            if (!form) return;
            const adultInput = form.querySelector('input[name="num_adults"]');
            const childInput = form.querySelector('input[name="num_children"]');
            const sidebarTotal = document.getElementById('sidebarTotal');
            const sumAdults = document.getElementById('sumAdults');
            const sumChildren = document.getElementById('sumChildren');
            const pricing = document.getElementById('pricingData');
            const adultPrice = Number(pricing?.dataset.adult || '0');
            const childPrice = Number(pricing?.dataset.child || '0');

            function calc() {
                const a = Math.max(0, parseInt(adultInput.value || '1', 10));
                const c = Math.max(0, parseInt(childInput.value || '0', 10));
                const total = a * adultPrice + c * childPrice;

                function fmt(n) {
                    return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
                }
                if (sidebarTotal) sidebarTotal.textContent = fmt(total);
                if (sumAdults) sumAdults.textContent = a;
                if (sumChildren) sumChildren.textContent = c;
            }

            adultInput.addEventListener('input', calc);
            childInput.addEventListener('input', calc);
            calc();
        })();
    </script>
@endsection
