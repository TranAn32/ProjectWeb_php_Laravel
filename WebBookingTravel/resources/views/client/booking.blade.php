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
        <a href="{{ url()->previous() ?: route('client.tours.index') }}" class="text-decoration-none small"><i class="far fa-arrow-left me-1"></i> Quay lại</a>
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
                        <input type="hidden" name="tourID" value="{{ $tour->tourID ?? '' }}" />

                        @if(isset($tour) && $tour)
                        <div class="mb-3">

                            <div class="d-flex gap-2 align-items-center">
                                @php
                                $img = $tour->image_path ?: asset('assets/images/destinations/dest1.jpg');
                                $isAbsolute = is_string($img) && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://') || str_starts_with($img, '//'));
                                $imgSrc = $isAbsolute ? $img : asset(ltrim(str_replace('\\','/',$img), '/'));
                                @endphp
                                <img src="{{ $imgSrc }}" alt="{{ $tour->title }}" style="width:120px;height:90px;object-fit:cover;border-radius:.75rem;" />
                                <div>
                                    <div class="fw-semibold fs-5">{{ $tour->title }}</div>
                                    <div class="text-muted">Khởi hành: {{ $tour->departurePoint ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info">Bạn chưa chọn tour. Vui lòng quay lại trang danh sách và bấm Đặt tour.</div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ngày khởi hành</label>
                                <input type="date" name="departureDate" class="form-control" value="{{ old('departureDate') }}" required />
                                @error('departureDate')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Người lớn</label>
                                <input type="number" name="numAdults" class="form-control" min="1" value="{{ old('numAdults', 1) }}" required />
                                @error('numAdults')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Trẻ em</label>
                                <input type="number" name="numChildren" class="form-control" min="0" value="{{ old('numChildren', 0) }}" />
                                @error('numChildren')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Yêu cầu đặc biệt</label>
                            <textarea name="specialRequest" class="form-control" rows="3" placeholder="Ví dụ: ăn chay, phòng gần cửa sổ...">{{ old('specialRequest') }}</textarea>
                            @error('specialRequest')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        @php
                        $hotelsJson = $tour->hotels ?? null;
                        if (is_string($hotelsJson)) {
                        $tmp = json_decode($hotelsJson, true);
                        if (json_last_error() === JSON_ERROR_NONE) { $hotelsJson = $tmp; }
                        }
                        $hotelItems = [];
                        if (is_array($hotelsJson)) {
                        if (isset($hotelsJson['items']) && is_array($hotelsJson['items'])) {
                        $hotelItems = $hotelsJson['items'];
                        } elseif (array_values($hotelsJson) === $hotelsJson) {
                        $hotelItems = $hotelsJson;
                        }
                        }
                        function extractHotelRoomPrices($h) {
                        $single = null; $double = null;
                        if (!is_array($h)) return ['single'=>null,'double'=>null];
                        $rooms = [];
                        if (!empty($h['rooms']) && is_array($h['rooms'])) {
                        $rooms = $h['rooms'];
                        } elseif (!empty($h['roomTypes']) && is_array($h['roomTypes'])) {
                        $rooms = $h['roomTypes'];
                        }
                        $candidates = [
                        'single'=>['single','don','phong_don','phòng đơn','singleRoom','single_room'],
                        'double'=>['double','doi','phong_doi','phòng đôi','doubleRoom','double_room']
                        ];
                        foreach ($candidates['single'] as $k) {
                        if (isset($rooms[$k])) {
                        $v = $rooms[$k];
                        $single = is_numeric($v) ? (float)$v : (is_string($v) ? (float)preg_replace('/[^0-9.]/','',$v) : null);
                        break;
                        }
                        }
                        foreach ($candidates['double'] as $k) {
                        if (isset($rooms[$k])) {
                        $v = $rooms[$k];
                        $double = is_numeric($v) ? (float)$v : (is_string($v) ? (float)preg_replace('/[^0-9.]/','',$v) : null);
                        break;
                        }
                        }
                        if (($single === null || $double === null) && array_values($rooms) === $rooms) {
                        if ($single === null && isset($rooms[0])) {
                        $v = $rooms[0];
                        $single = is_numeric($v) ? (float)$v : (is_string($v) ? (float)preg_replace('/[^0-9.]/','',$v) : null);
                        }
                        if ($double === null && isset($rooms[1])) {
                        $v = $rooms[1];
                        $double = is_numeric($v) ? (float)$v : (is_string($v) ? (float)preg_replace('/[^0-9.]/','',$v) : null);
                        }
                        }
                        return ['single'=>$single,'double'=>$double];
                        }
                        @endphp

                        @if(!empty($hotelItems))
                        <div class="mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label mb-0">Chọn khách sạn (tuỳ chọn)</label>
                                <span class="text-muted small">Giá phòng (tính theo mỗi người)</span>
                            </div>
                            <div class="vstack gap-2">
                                <div class="border rounded-3 p-2 hotel-item" id="hotel_none_item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check">
                                            <input class="form-check-input hotel-radio" type="radio" name="selectedHotelIndex" id="hotel_none" value="" data-hotel-name="" data-single-price="0" data-double-price="0">
                                        </div>
                                        <div class="flex-grow-1">
                                            <label for="hotel_none" class="fw-semibold d-block mb-0">Không chọn khách sạn</label>
                                            <div class="text-muted small">Bạn có thể bỏ chọn khách sạn và đặt tour không kèm phòng.</div>
                                        </div>
                                    </div>
                                </div>
                                @foreach($hotelItems as $idx => $h)
                                @php
                                $hName = is_array($h) ? ($h['name'] ?? ($h['title'] ?? 'Khách sạn')) : (string) $h;
                                $hStars = is_array($h) ? ($h['stars'] ?? $h['rating'] ?? null) : null;
                                $hAddr = is_array($h) ? ($h['address'] ?? null) : null;
                                $prices = extractHotelRoomPrices($h);
                                $singlePrice = $prices['single'];
                                $doublePrice = $prices['double'];
                                $hImg = null;
                                if (is_array($h)) {
                                $hImg = $h['image'] ?? $h['imageUrl'] ?? $h['image_url'] ?? $h['photo'] ?? null;
                                if (!$hImg && !empty($h['images']) && is_array($h['images'])) {
                                $first = $h['images'][0] ?? null;
                                $hImg = is_string($first) ? $first : (is_array($first) ? ($first['url'] ?? null) : null);
                                }
                                }
                                $imgSrc = null;
                                if ($hImg) {
                                $abs = is_string($hImg) && (str_starts_with($hImg,'http://') || str_starts_with($hImg,'https://') || str_starts_with($hImg,'//'));
                                $imgSrc = $abs ? $hImg : asset(ltrim(str_replace('\\','/',$hImg), '/'));
                                }
                                @endphp
                                <div class="border rounded-3 p-2 hotel-item" id="hotel_item_{{ $idx }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check">
                                            <input class="form-check-input hotel-radio" type="radio" name="selectedHotelIndex" id="hotel_{{ $idx }}" value="{{ $idx }}" data-hotel-name="{{ $hName }}" data-single-price="{{ $singlePrice ?? 0 }}" data-double-price="{{ $doublePrice ?? 0 }}">
                                        </div>
                                        @if($imgSrc)
                                        <img src="{{ $imgSrc }}" alt="{{ $hName }}" style="width:64px;height:64px;object-fit:cover;border-radius:.5rem;" />
                                        @endif
                                        <div class="flex-grow-1">
                                            <label for="hotel_{{ $idx }}" class="fw-semibold d-block mb-0">{{ $hName }} @if($hStars)<span class="text-warning small">({{ $hStars }}★)</span>@endif</label>
                                            @if($hAddr)<div class="text-muted small">{{ $hAddr }}</div>@endif
                                            <div class="small mt-1">Đơn giá: Đơn <strong>{{ $singlePrice ? number_format($singlePrice,0,',','.') . 'đ' : '—' }}</strong> • Đôi <strong>{{ $doublePrice ? number_format($doublePrice,0,',','.') . 'đ' : '—' }}</strong></div>
                                            <div class="hotel-room-options mt-2" data-for="hotel_{{ $idx }}" style="display:none;">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-6">
                                                        <label class="small mb-1">Phòng đơn</label>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="text-muted small">Đơn giá: {{ $singlePrice ? number_format($singlePrice,0,',','.') . 'đ' : '—' }}</span>
                                                            <div class="ms-auto d-flex align-items-center gap-2">
                                                                <span class="small">Số phòng</span>
                                                                <div class="input-group input-group-sm qty-group">
                                                                    <button class="btn btn-outline-secondary qty-minus" type="button" data-target="single">−</button>
                                                                    <input type="number" class="form-control text-center hotel-single-count" min="0" value="0">
                                                                    <button class="btn btn-outline-secondary qty-plus" type="button" data-target="single">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="small mb-1">Phòng đôi</label>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="text-muted small">Đơn giá: {{ $doublePrice ? number_format($doublePrice,0,',','.') . 'đ' : '—' }}</span>
                                                            <div class="ms-auto d-flex align-items-center gap-2">
                                                                <span class="small">Số phòng</span>
                                                                <div class="input-group input-group-sm qty-group">
                                                                    <button class="btn btn-outline-secondary qty-minus" type="button" data-target="double">−</button>
                                                                    <input type="number" class="form-control text-center hotel-double-count" min="0" value="0">
                                                                    <button class="btn btn-outline-secondary qty-plus" type="button" data-target="double">+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div id="pricingData" data-adult="{{ isset($tour) && $tour ? (int)($tour->priceAdult ?? 0) : 0 }}" data-child="{{ isset($tour) && $tour ? (int)($tour->priceChild ?? 0) : 0 }}" hidden></div>
                        <input type="hidden" name="selectedHotelName" id="selectedHotelName" />
                        <input type="hidden" name="hotelSingleRooms" id="hotelSingleRooms" value="0" />
                        <input type="hidden" name="hotelDoubleRooms" id="hotelDoubleRooms" value="0" />
                        <input type="hidden" name="hotelSinglePrice" id="hotelSinglePrice" value="0" />
                        <input type="hidden" name="hotelDoublePrice" id="hotelDoublePrice" value="0" />
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Thông tin khách hàng</h5>
                    @if(isset($user) && $user)
                    <div class="mb-2"><strong>Tên:</strong> {{ $user->userName }}</div>
                    <div class="mb-2"><strong>Email:</strong> {{ $user->email }}</div>
                    @if($user->phoneNumber)<div class="mb-2"><strong>SĐT:</strong> {{ $user->phoneNumber }}</div>@endif
                    @if($user->address)<div class="mb-2"><strong>Địa chỉ:</strong> {{ $user->address }}</div>@endif
                    @else
                    <div class="alert alert-warning">Bạn cần đăng nhập để đặt tour.</div>
                    @endif
                </div>
            </div>

            @if(isset($tour) && $tour)
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title">Tóm tắt giá</h5>
                    @php $adult = $tour->priceAdult ?? 0; $child = $tour->priceChild ?? 0; @endphp
                    <ul class="list-unstyled small mb-2">
                        <li>Người lớn: <span id="sumAdults">0</span> x {{ number_format($adult,0,',','.') }}đ</li>
                        <li>Trẻ em: <span id="sumChildren">0</span> x {{ number_format($child,0,',','.') }}đ</li>
                    </ul>
                    <div class="small" id="sidebarHotel" style="display:none;">
                        Khách sạn:
                        <div class="ps-2">
                            <div>Đơn: <span id="sidebarSingleCount">0</span> x <span id="sidebarSingleUnit">0</span>đ</div>
                            <div>Đôi: <span id="sidebarDoubleCount">0</span> x <span id="sidebarDoubleUnit">0</span>đ</div>
                        </div>
                        <div>Tổng KS: <strong id="sidebarHotelTotal">0đ</strong></div>
                    </div>
                    <div class="fs-6 mt-1">Tạm tính: <strong id="sidebarTotal">0đ</strong></div>
                    <div class="text-muted small">(Chưa bao gồm phụ phí nếu có)</div>
                    <button class="btn btn-primary w-100 mt-3" type="submit" form="bookingForm" @if(!isset($tour) || !$tour) disabled @endif>Đặt ngay</button>
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
        const adultInput = form.querySelector('input[name="numAdults"]');
        const childInput = form.querySelector('input[name="numChildren"]');
        const sidebarTotal = document.getElementById('sidebarTotal');
        const sumAdults = document.getElementById('sumAdults');
        const sumChildren = document.getElementById('sumChildren');
        const pricing = document.getElementById('pricingData');
        const adultPrice = Number(pricing?.dataset.adult || '0');
        const childPrice = Number(pricing?.dataset.child || '0');

        // Hotel selection
        const hotelRadios = Array.from(document.querySelectorAll('.hotel-radio'));
        const selectedHotelName = document.getElementById('selectedHotelName');
        const sidebarHotel = document.getElementById('sidebarHotel');
        const sidebarHotelTotal = document.getElementById('sidebarHotelTotal');
        const sidebarSingleCount = document.getElementById('sidebarSingleCount');
        const sidebarDoubleCount = document.getElementById('sidebarDoubleCount');
        const sidebarSingleUnit = document.getElementById('sidebarSingleUnit');
        const sidebarDoubleUnit = document.getElementById('sidebarDoubleUnit');
        const hotelSingleRooms = document.getElementById('hotelSingleRooms');
        const hotelDoubleRooms = document.getElementById('hotelDoubleRooms');
        const hotelSinglePrice = document.getElementById('hotelSinglePrice');
        const hotelDoublePrice = document.getElementById('hotelDoublePrice');
        const hotelItems = Array.from(document.querySelectorAll('.hotel-item'));
        let singleUnit = 0;
        let doubleUnit = 0;

        function calc() {
            const a = Math.max(0, parseInt(adultInput.value || '0', 10));
            const c = Math.max(0, parseInt(childInput.value || '0', 10));
            const tourSubtotal = a * adultPrice + c * childPrice;
            const sCount = Math.max(0, parseInt(hotelSingleRooms.value || '0', 10));
            const dCount = Math.max(0, parseInt(hotelDoubleRooms.value || '0', 10));
            const hotelSubtotal = sCount * singleUnit + dCount * doubleUnit;
            const total = tourSubtotal + hotelSubtotal;

            function fmt(n) {
                return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
            }
            if (sidebarTotal) sidebarTotal.textContent = fmt(total);
            if (sumAdults) sumAdults.textContent = a;
            if (sumChildren) sumChildren.textContent = c;
            if (sidebarHotel) {
                if (singleUnit > 0 || doubleUnit > 0) {
                    sidebarHotel.style.display = '';
                    sidebarSingleCount.textContent = sCount;
                    sidebarDoubleCount.textContent = dCount;
                    sidebarSingleUnit.textContent = new Intl.NumberFormat('vi-VN').format(singleUnit);
                    sidebarDoubleUnit.textContent = new Intl.NumberFormat('vi-VN').format(doubleUnit);
                    if (sidebarHotelTotal) sidebarHotelTotal.textContent = fmt(hotelSubtotal);
                } else {
                    sidebarHotel.style.display = 'none';
                }
            }
        }

        hotelRadios.forEach(r => {
            r.addEventListener('change', () => {
                selectedHotelName.value = r.dataset.hotelName || '';
                singleUnit = Number(r.dataset.singlePrice || '0');
                doubleUnit = Number(r.dataset.doublePrice || '0');
                el.style.display = (singleUnit > 0 || doubleUnit > 0) ? '' : 'none';
                // Prepare inputs and hook qty buttons
                const sInput = el.querySelector('.hotel-single-count');
                const dInput = el.querySelector('.hotel-double-count');
                if (sInput) {
                    sInput.value = '0';
                    sInput.oninput = () => {
                        hotelSingleRooms.value = sInput.value || '0';
                        calc();
                    };
                }
                if (dInput) {
                    dInput.value = '0';
                    dInput.oninput = () => {
                        hotelDoubleRooms.value = dInput.value || '0';
                        calc();
                    };
                }
                const minusBtns = el.querySelectorAll('.qty-minus');
                const plusBtns = el.querySelectorAll('.qty-plus');
                minusBtns.forEach(btn => btn.onclick = () => {
                    if (btn.dataset.target === 'single' && sInput) {
                        sInput.stepDown();
                        sInput.dispatchEvent(new Event('input'));
                    }
                    if (btn.dataset.target === 'double' && dInput) {
                        dInput.stepDown();
                        dInput.dispatchEvent(new Event('input'));
                    }
                });
                plusBtns.forEach(btn => btn.onclick = () => {
                    if (btn.dataset.target === 'single' && sInput) {
                        sInput.stepUp();
                        sInput.dispatchEvent(new Event('input'));
                    }
                    if (btn.dataset.target === 'double' && dInput) {
                        dInput.stepUp();
                        dInput.dispatchEvent(new Event('input'));
                    }
                });
                hotelDoublePrice.value = doubleUnit ? String(doubleUnit) : '0';
                el.style.display = 'none';
                const containers = Array.from(document.querySelectorAll('.hotel-room-options'));
                containers.forEach(el => {
                    // Highlight selected card
                    hotelItems.forEach(it => it.classList.remove('selected', 'border-primary'));
                    const host = document.getElementById(r.id === 'hotel_none' ? 'hotel_none_item' : ('hotel_item_' + r.value));
                    if (host) host.classList.add('selected', 'border-primary');
                    if (el.dataset.for === r.id) {
                        el.style.display = '';
                        const sInput = el.querySelector('.hotel-single-count');
                        const dInput = el.querySelector('.hotel-double-count');
                        if (sInput) {
                            sidebarHotel.style.display = (singleUnit > 0 || doubleUnit > 0) ? '' : 'none';
                            sInput.oninput = () => {
                                hotelSingleRooms.value = sInput.value || '0';
                                calc();
                            };
                        }
                        if (dInput) {
                            dInput.value = '0';
                            dInput.oninput = () => {
                                hotelDoubleRooms.value = dInput.value || '0';
                                calc();
                            };
                        }
                    } else {
                        el.style.display = 'none';
                    }
                });
                // Reset counts
                hotelSingleRooms.value = '0';
                hotelDoubleRooms.value = '0';
                // Update sidebar units immediately
                if (sidebarHotel) {
                    sidebarHotel.style.display = '';
                    sidebarSingleUnit.textContent = new Intl.NumberFormat('vi-VN').format(singleUnit);
                    sidebarDoubleUnit.textContent = new Intl.NumberFormat('vi-VN').format(doubleUnit);
                }
                calc();
            });
        });

        adultInput.addEventListener('input', calc);
        childInput.addEventListener('input', calc);
        calc();
    })();
</script>
@endsection