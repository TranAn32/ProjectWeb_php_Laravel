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

            .form-control.is-invalid {
                border-color: #dc3545;
            }

            .invalid-feedback {
                display: none;
                color: #dc3545;
                font-size: 14px;
                margin-top: 5px;
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

            /* Customer info styling */
            .customer-info {
                background: white;
                border-radius: 8px;
                padding: 20px;
                border: 1px solid #dee2e6;
            }

            .customer-info .info-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #e9ecef;
            }

            .customer-info .info-item:last-child {
                border-bottom: none;
            }

            .customer-info .info-label {
                font-weight: 600;
                color: #495057;
            }

            .customer-info .info-value {
                color: #212529;
            }

            /* Pricing summary styling */
            .pricing-summary {
                background: white;
                border-radius: 8px;
                padding: 20px;
                border: 1px solid #dee2e6;
            }

            .pricing-summary .card-title {
                color: #212529;
                margin-bottom: 16px;
                font-weight: 700;
            }

            .pricing-list li {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
                font-size: 14px;
            }

            .pricing-list .price {
                font-weight: 600;
                color: #007bff;
            }

            .pricing-note {
                font-size: 12px;
                text-align: center;
                color: #6c757d;
            }

            /* Form section styling */
            .form-section {
                background: white;
                border-radius: 8px;
                padding: 20px;
                border: 1px solid #dee2e6;
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
                    <div class="card-body form-section">
                        <h5 class="mb-4">Thông tin đặt tour</h5>
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
                                    <input type="date" name="departure_date" class="form-control" id="departureDate"
                                        value="{{ old('departure_date') }}" required />
                                    <div class="invalid-feedback" id="departureDateError"></div>
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
                                    <input type="tel" name="phone_number" class="form-control" id="phoneNumber"
                                        value="{{ old('phone_number', auth('web')->user()->phoneNumber ?? '') }}"
                                        placeholder="Nhập số điện thoại..." required />
                                    <div class="invalid-feedback" id="phoneNumberError"></div>
                                    @error('phone_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Điểm đón <span class="text-danger">*</span></label>
                                    <input type="text" name="pickup_point" class="form-control"
                                        placeholder="Nhập địa chỉ..." required />
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
                    <div class="card-body customer-info">
                        <h5 class="card-title">Thông tin khách hàng</h5>
                        @auth('web')
                            @php $user = Auth::guard('web')->user(); @endphp
                            <div class="info-item">
                                <span class="info-label">Tên:</span>
                                <span class="info-value">{{ $user->userName ?? $user->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value">{{ $user->email }}</span>
                            </div>
                            @if ($user->phoneNumber ?? false)
                                <div class="info-item">
                                    <span class="info-label">SĐT:</span>
                                    <span class="info-value">{{ $user->phoneNumber }}</span>
                                </div>
                            @endif
                            @if ($user->address ?? false)
                                <div class="info-item">
                                    <span class="info-label">Địa chỉ:</span>
                                    <span class="info-value">{{ $user->address }}</span>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning">Bạn cần đăng nhập để đặt tour.</div>
                        @endauth
                    </div>
                </div>

                @if (isset($tour) && $tour)
                    <div class="card shadow-sm mt-3">
                        <div class="card-body pricing-summary">
                            <h5 class="card-title">Tóm tắt giá</h5>
                            @php
                                $adult = $tour->price ?? 0;
                                $child = ($tour->price ?? 0) * 0.8; // Trẻ em giảm 20%
                            @endphp
                            <ul class="list-unstyled pricing-list mb-3">
                                <li>
                                    <span>Người lớn: <span id="sumAdults">{{ old('num_adults', 1) }}</span> x
                                        {{ number_format($adult, 0, ',', '.') }}đ = <span id="totalAdults"
                                            class="price">{{ number_format($adult * old('num_adults', 1), 0, ',', '.') }}đ</span></span>
                                </li>
                                <li>
                                    <span>Trẻ em: <span id="sumChildren">{{ old('num_children', 0) }}</span> x
                                        {{ number_format($child, 0, ',', '.') }}đ = <span id="totalChildren"
                                            class="price">{{ number_format($child * old('num_children', 0), 0, ',', '.') }}đ</span></span>
                                </li>
                            </ul>
                            <div class="mt-3 text-start fw-bold">Tạm tính: <strong
                                    id="sidebarTotal">{{ number_format($adult, 0, ',', '.') }}đ</strong></div>

                            <button class="btn btn-light w-100 mt-3" style="background-color: #007bff; color: white;"
                                type="submit" form="bookingForm" @if (!isset($tour) || !$tour) disabled @endif>Đặt
                                ngay</button>
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
            const totalAdults = document.getElementById('totalAdults');
            const totalChildren = document.getElementById('totalChildren');
            const pricing = document.getElementById('pricingData');
            const adultPrice = Number(pricing?.dataset.adult || '0');
            const childPrice = Number(pricing?.dataset.child || '0');

            // Validation elements
            const departureDateInput = document.getElementById('departureDate');
            const phoneNumberInput = document.getElementById('phoneNumber');
            const departureDateError = document.getElementById('departureDateError');
            const phoneNumberError = document.getElementById('phoneNumberError');
            // Submit button might be outside the form (uses form="bookingForm").
            // Try global lookup first, then fallback to inside form.
            const submitBtn = document.querySelector('button[type="submit"][form="bookingForm"]') ||
                form.querySelector('button[type="submit"]');

            // Validation functions
            function validateDepartureDate() {
                const value = departureDateInput.value;
                const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
                if (!value) {
                    showError(departureDateInput, departureDateError, 'Vui lòng chọn ngày khởi hành.');
                    return false;
                }
                if (value <= today) {
                    showError(departureDateInput, departureDateError, 'Ngày khởi hành phải là ngày trong tương lai.');
                    return false;
                }
                hideError(departureDateInput, departureDateError);
                return true;
            }

            function validatePhoneNumber() {
                const value = phoneNumberInput.value.trim();
                const phoneRegex = /^0[3-9]\d{8}$/; // VN phone: bắt đầu 03-09, 10 chữ số
                if (!value) {
                    showError(phoneNumberInput, phoneNumberError, 'Vui lòng nhập số điện thoại.');
                    return false;
                }
                if (!phoneRegex.test(value)) {
                    showError(phoneNumberInput, phoneNumberError,
                        'Số điện thoại không đúng định dạng (10 chữ số, bắt đầu từ 03-09).');
                    return false;
                }
                hideError(phoneNumberInput, phoneNumberError);
                return true;
            }

            function showError(input, errorDiv, message) {
                input.classList.add('is-invalid');
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }

            function hideError(input, errorDiv) {
                input.classList.remove('is-invalid');
                errorDiv.style.display = 'none';
            }

            // Track validity
            let dateValid = true; // Assume valid initially
            let phoneValid = true;

            function updateSubmitButton() {
                if (submitBtn) {
                    submitBtn.disabled = !(dateValid && phoneValid);
                }
            }

            function calc() {
                const a = Math.max(0, parseInt(adultInput.value || '1', 10));
                const c = Math.max(0, parseInt(childInput.value || '0', 10));
                const total = a * adultPrice + c * childPrice;

                function fmt(n) {
                    return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
                }
                console.log('Calc: adults=' + a + ', children=' + c + ', total=' + total);
                if (sidebarTotal) sidebarTotal.textContent = fmt(total);
                if (sumAdults) sumAdults.textContent = a;
                if (sumChildren) sumChildren.textContent = c;
                if (totalAdults) totalAdults.textContent = fmt(a * adultPrice);
                if (totalChildren) totalChildren.textContent = fmt(c * childPrice);
            }

            // Initial calc (no validation on load) and sync displayed values
            calc();

            // Initial validation if fields have values (e.g., from old input)
            if (departureDateInput.value) {
                dateValid = validateDepartureDate();
            }
            if (phoneNumberInput.value) {
                phoneValid = validatePhoneNumber();
            }
            updateSubmitButton();

            // Event listeners for individual validation
            departureDateInput.addEventListener('input', function() {
                dateValid = validateDepartureDate();
                updateSubmitButton();
            });
            departureDateInput.addEventListener('blur', function() {
                dateValid = validateDepartureDate();
                updateSubmitButton();
            });
            phoneNumberInput.addEventListener('input', function() {
                phoneValid = validatePhoneNumber();
                updateSubmitButton();
            });
            phoneNumberInput.addEventListener('blur', function() {
                phoneValid = validatePhoneNumber();
                updateSubmitButton();
            });

            // Event listeners for price calculation
            adultInput.addEventListener('input', calc);
            adultInput.addEventListener('change', calc);
            childInput.addEventListener('input', calc);
            childInput.addEventListener('change', calc);

            // Prevent form submit if invalid
            form.addEventListener('submit', function(e) {
                dateValid = validateDepartureDate();
                phoneValid = validatePhoneNumber();
                updateSubmitButton();
                if (!dateValid || !phoneValid) {
                    e.preventDefault();
                    alert('Vui lòng sửa các lỗi trước khi gửi form.');
                }
            });
        })();
    </script>
@endsection
