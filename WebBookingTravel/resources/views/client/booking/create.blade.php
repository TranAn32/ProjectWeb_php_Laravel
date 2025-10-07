@extends('client.layouts.app')
@section('title', 'Đặt tour')
@section('content')
    <div class="container py-4">
        <style>
            .form-check-input.hotel-radio {
                width: 1.2rem;
                height: 1.2rem;
                aspect-ratio: 1 / 1;
                border-radius: 50% !important;
                appearance: auto;
                -webkit-appearance: radio;
                -moz-appearance: auto;
                position: static;
                inset: auto;
                transform: none;
                background: #fff;
                border: 1px solid rgba(0, 0, 0, .25);
                margin: 0;
            }

            /* Prevent theme overrides making the radio wrapper look like a pill */
            .hotel-item .form-check {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0 !important;
                margin: 0 !important;
                width: auto;
                height: auto;
                border: 0 !important;
                background: transparent !important;
                border-radius: 0 !important;
            }

            .hotel-item .form-check::before,
            .hotel-item .form-check::after {
                content: none !important;
            }

            .hotel-item {
                transition: border-color .15s ease;
            }

            .hotel-item.selected {
                border-color: var(--bs-primary) !important;
            }

            .qty-group {
                width: 110px;
            }
        </style>
        <div class="mb-3">
            <a href="{{ url()->previous() ?: route('client.tours.index') }}" class="text-decoration-none small"><i
                    class="far fa-arrow-left me-1"></i> Quay lại</a>
        </div>

        <h1 class="h4 mb-3">Đặt tour</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

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
                                            style="width:120px;height:90px;object-fit:cover;border-radius:.75rem;" />
                                        <div>
                                            <div class="fw-semibold fs-5">{{ $tour->title }}</div>
                                            <div class="text-muted">Khởi hành: {{ $tour->departurePoint ?? '—' }}</div>
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

                            <div class="mt-3" style="min-height: 200px;">
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
