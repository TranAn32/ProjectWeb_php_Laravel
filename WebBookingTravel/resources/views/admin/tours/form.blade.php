@extends('admin.layouts.app')
@section('title', ($tour->exists ? 'Sửa' : 'Thêm') . ' Tour')
@section('page_title', ($tour->exists ? 'Sửa' : 'Thêm') . ' Tour')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.tours.index') }}">Tours</a></li>
    <li class="breadcrumb-item active">{{ $tour->exists ? 'Sửa' : 'Thêm' }}</li>
@endsection
@section('page_actions')
    <a href="{{ route('admin.tours.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left me-1"></i>
        Quay lại</a>
@endsection
@section('content')
    <form method="post"
        action="{{ $tour->exists ? route('admin.tours.update', $tour->tourID) : route('admin.tours.store') }}"
        enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
        @if ($tour->exists)
            @method('PUT')
        @endif
        <div class="card shadow-sm" id="singleScreenCard">
            <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between py-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0">{{ $tour->exists ? 'Sửa Tour' : 'Thêm Tour Mới' }}</h5>
                    @if ($tour->exists)
                        <span class="badge bg-secondary">#{{ $tour->tourID }}</span>
                    @endif
                </div>
               
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    <!-- Thông tin cơ bản - cột trái -->
                    <div class="col-md-4">
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label-xs">Tên tour <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{{ old('title', $tour->title) }}"
                                    class="form-control form-control-sm" required placeholder="Nhập tên tour">
                            </div>
                            <div class="col-6">
                                <label class="form-label-xs">Khởi hành</label>
                                <input type="text" name="departurePoint"
                                    value="{{ old('departurePoint', $tour->departurePoint) }}"
                                    class="form-control form-control-sm" placeholder="Hà Nội">
                            </div>
                            <div class="col-6">
                                <label class="form-label-xs">Điểm đến</label>
                                <input type="text" name="destinationPoint"
                                    value="{{ old('destinationPoint', $tour->destinationPoint) }}"
                                    class="form-control form-control-sm" placeholder="Hạ Long">
                            </div>
                            <div class="col-6">
                                <label class="form-label-xs">Giá NL</label>
                                <input type="number" min="0" name="priceAdult"
                                    value="{{ old('priceAdult', $tour->priceAdult) }}" class="form-control form-control-sm"
                                    placeholder="2600000">
                            </div>
                            <div class="col-6">
                                <label class="form-label-xs">Giá TE</label>
                                <input type="number" min="0" name="priceChild"
                                    value="{{ old('priceChild', $tour->priceChild) }}" class="form-control form-control-sm"
                                    placeholder="1600000">
                            </div>
                            <div class="col-12">
                                <label class="form-label-xs">Trạng thái</label>
                                @php($status = old('status', $tour->status))
                                <select name="status" class="form-select form-select-sm">
                                    <option value="draft" @selected($status === 'draft')>Nháp</option>
                                    <option value="published" @selected($status === 'published')>Công khai</option>
                                    <option value="canceled" @selected($status === 'canceled')>Hủy</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Ảnh tour - cột giữa -->
                    <div class="col-md-4">
                        <label class="form-label-xs">Ảnh tour <span class="text-danger">*</span></label>
                        <div id="galleryStrip" class="border rounded p-2 mb-2 bg-light overflow-auto" style="height:160px;">
                            <div class="text-muted small text-center" id="galleryPlaceholder" style="line-height:140px;">
                                Chưa chọn ảnh</div>
                        </div>
                        <input type="file" id="galleryInput" name="images[]" accept="image/*" multiple
                            class="btn btn-outline-primary btn-sm w-100" style="font-size:11px;" required>
                    </div>

                    <!-- Mô tả và actions - cột phải -->
                    <div class="col-md-4">
                        <label class="form-label-xs">Mô tả tour</label>
                        <textarea name="description" rows="6" class="form-control form-control-sm mb-2"
                            placeholder="Mô tả ngắn gọn về tour...">{{ old('description', $tour->description) }}</textarea>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary btn-sm flex-fill">Hủy</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Box Lịch trình -->
        <div class="card shadow-sm mt-3">
            <div class="card-header d-flex align-items-center justify-content-between py-2">
                <h6 class="mb-0"><i class="fa fa-route me-2"></i>Lịch trình tour</h6>
            </div>
            <div class="card-body p-3">
                <label class="form-label-xs mb-2">Nhập lịch trình chi tiết (mỗi dòng là 1 ngày)</label>
                <textarea id="itineraryText" name="itinerary_text" class="form-control" rows="6"
                    placeholder="Ngày 1: Hà Nội - Hạ Long - Tham quan hang động
Ngày 2: Chèo kayak - Làng chài - Trở về Hà Nội
Ngày 3: ...">{{ old('itinerary_text', '') }}</textarea>
                <div class="form-text mt-1">
                    <i class="fa fa-info-circle"></i> Mỗi dòng tương ứng với 1 ngày. Hệ thống sẽ tự động tách thành từng
                    ngày.
                </div>
            </div>
        </div>

        <!-- Box Khách sạn -->
        <div class="card shadow-sm mt-3">
            <div class="card-header d-flex align-items-center justify-content-between py-2">
                <h6 class="mb-0"><i class="fa fa-hotel me-2"></i>Thông tin khách sạn</h6>
            </div>
            <div class="card-body p-3">
                <div class="alert alert-info alert-sm mb-3">
                    <i class="fa fa-info-circle"></i> Thêm thông tin khách sạn cho tour. Ảnh sẽ được lưu tự động.
                </div>
                <div id="hotelsContainer"></div>
            </div>
        </div>

        <!-- Nút Lưu ở cuối -->
        <div class="card shadow-sm mt-3">
            <div class="card-body p-3 text-center">
                <button type="submit" class="btn btn-success btn-lg px-5">
                    <i class="fa fa-save me-2"></i>Lưu Tour
                </button>
            </div>
        </div>

        <input type="hidden" name="itinerary" id="itineraryInput" />
        <input type="hidden" name="hotels" id="hotelsInput" />
    </form>
    @push('scripts')
        <script>
            // Helpers to parse safe JSON
            function safeJsonParse(val, fallback) {
                if (!val || !val.trim()) return fallback;
                try {
                    return JSON.parse(val);
                } catch {
                    return fallback;
                }
            }

            // Đảm bảo DOM đã load hoàn toàn
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM fully loaded');
                initializeHotelsSection();
            });

            // Backup: khởi tạo ngay nếu DOM đã sẵn sàng
            if (document.readyState === 'loading') {
                console.log('DOM is still loading...');
            } else {
                console.log('DOM already loaded, initializing immediately');
                setTimeout(initializeHotelsSection, 100);
            }

            // Seed existing data from old() or model
            const existingImages = safeJsonParse(@json(old('images', is_string($tour->images) ? $tour->images : json_encode($tour->images))), []);
            const existingItinerary = safeJsonParse(@json(old('itinerary', is_string($tour->itinerary) ? $tour->itinerary : json_encode($tour->itinerary))), []);
            const existingHotels = safeJsonParse(@json(old('hotels', is_string($tour->hotels) ? $tour->hotels : json_encode($tour->hotels))), []);

            // Elements
            const itineraryInput = document.getElementById('itineraryInput');
            const hotelsInput = document.getElementById('hotelsInput');
            const galleryInput = document.getElementById('galleryInput');
            const galleryStrip = document.getElementById('galleryStrip');
            const galleryPlaceholder = document.getElementById('galleryPlaceholder');
            const itineraryText = document.getElementById('itineraryText');
            const hotelsContainer = document.getElementById('hotelsContainer');
            const addHotelBtn = document.getElementById('addHotelBtn');

            let hotelCounter = 0;

            // Debug: Check if elements exist
            console.log('Elements found:', {
                addHotelBtn: !!addHotelBtn,
                hotelsContainer: !!hotelsContainer,
                itineraryText: !!itineraryText
            });

            // Xử lý upload ảnh tour
            galleryInput?.addEventListener('change', e => {
                const files = Array.from(e.target.files || []);
                galleryStrip.innerHTML = '';
                if (!files.length) {
                    galleryStrip.appendChild(galleryPlaceholder);
                    return;
                }

                const grid = document.createElement('div');
                grid.className = 'd-flex flex-wrap gap-1';

                files.forEach((file, idx) => {
                    const thumb = document.createElement('div');
                    thumb.className = 'border rounded overflow-hidden bg-white';
                    thumb.style.width = '60px';
                    thumb.style.height = '60px';

                    const reader = new FileReader();
                    reader.onload = ev => {
                        thumb.innerHTML = `
                            <img src="${ev.target.result}" class="w-100 h-100" style="object-fit:cover;" alt="thumb">
                            ${idx === 0 ? '<div class="position-absolute top-0 start-0"><span class="badge bg-primary" style="font-size:8px;">Cover</span></div>' : ''}
                        `;
                        thumb.style.position = 'relative';
                    };
                    reader.readAsDataURL(file);
                    grid.appendChild(thumb);
                });

                galleryStrip.appendChild(grid);
            });

            // Khởi tạo lịch trình từ dữ liệu có sẵn
            if (existingItinerary.length > 0) {
                const itineraryLines = existingItinerary.map(item =>
                    `Ngày ${item.day}: ${item.activity}`
                ).join('\n');
                if (itineraryText) itineraryText.value = itineraryLines;
            }

            // Function để khởi tạo section khách sạn
            function initializeHotelsSection() {
                const container = document.getElementById('hotelsContainer');
                console.log('Hotels container found:', !!container);
                console.log('Existing hotels count:', existingHotels.length);

                if (container) {
                    // Clear container first
                    container.innerHTML = '';

                    if (existingHotels.length > 0) {
                        console.log('Loading existing hotels...');
                        existingHotels.forEach(hotel => {
                            const hotelForm = createHotelForm(hotel);
                            container.appendChild(hotelForm);
                        });
                    } else {
                        // Tạo ít nhất 1 form khách sạn trống khi không có dữ liệu
                        console.log('Creating empty hotel form...');
                        const hotelForm = createHotelForm();
                        container.appendChild(hotelForm);
                        console.log('Hotel form added to container');
                    }
                } else {
                    console.error('Hotels container not found!');
                }
            }

            // Tạo form khách sạn
            function createHotelForm(hotel = {}) {
                console.log('Creating hotel form with data:', hotel);
                const id = ++hotelCounter;
                const div = document.createElement('div');
                div.className = 'border rounded p-3 mb-3 hotel-form bg-light';
                div.dataset.hotelId = id;

                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Khách sạn #${id}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-hotel">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label-xs">Tên khách sạn</label>
                            <input type="text" class="form-control form-control-sm hotel-name" 
                                value="${hotel.name || ''}" placeholder="Halong Plaza">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-xs">Đánh giá</label>
                            <select class="form-select form-select-sm hotel-rating">
                                <option value="">--</option>
                                <option value="1" ${hotel.rating == 1 ? 'selected' : ''}>1 sao</option>
                                <option value="2" ${hotel.rating == 2 ? 'selected' : ''}>2 sao</option>
                                <option value="3" ${hotel.rating == 3 ? 'selected' : ''}>3 sao</option>
                                <option value="4" ${hotel.rating == 4 ? 'selected' : ''}>4 sao</option>
                                <option value="5" ${hotel.rating == 5 ? 'selected' : ''}>5 sao</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-xs">Ảnh khách sạn</label>
                            <input type="file" class="form-control form-control-sm hotel-image" 
                                accept="image/*" data-hotel-id="${id}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-xs">Preview</label>
                            <div class="hotel-image-preview border rounded d-flex align-items-center justify-content-center bg-light" 
                                style="height:32px;">
                                ${hotel.image ? `<img src="${hotel.image}" class="h-100" style="object-fit:cover;">` : '<small class="text-muted">Chưa có ảnh</small>'}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-xs">Giá phòng đơn (VNĐ)</label>
                            <input type="number" class="form-control form-control-sm hotel-single-price" 
                                value="${hotel.rooms?.single || ''}" placeholder="800000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-xs">Giá phòng đôi (VNĐ)</label>
                            <input type="number" class="form-control form-control-sm hotel-double-price" 
                                value="${hotel.rooms?.double || ''}" placeholder="1200000">
                        </div>
                    </div>
                `;

                return div;
            }

            // Thêm khách sạn - sử dụng event delegation
            document.addEventListener('click', (e) => {
                if (e.target.id === 'addHotelBtn' || e.target.closest('#addHotelBtn')) {
                    e.preventDefault();
                    console.log('Add hotel button clicked');
                    const container = document.getElementById('hotelsContainer');
                    if (container) {
                        const hotelForm = createHotelForm();
                        container.appendChild(hotelForm);
                    }
                }
            });

            // Xóa khách sạn - sử dụng event delegation
            document.addEventListener('click', (e) => {
                if (e.target.closest('.remove-hotel')) {
                    e.preventDefault();
                    console.log('Remove hotel button clicked');
                    e.target.closest('.hotel-form').remove();
                }
            });

            // Xử lý upload ảnh khách sạn - sử dụng event delegation
            document.addEventListener('change', (e) => {
                if (e.target.classList.contains('hotel-image')) {
                    const file = e.target.files[0];
                    const preview = e.target.closest('.hotel-form').querySelector('.hotel-image-preview');

                    if (file && preview) {
                        const reader = new FileReader();
                        reader.onload = ev => {
                            preview.innerHTML =
                                `<img src="${ev.target.result}" class="h-100 w-100" style="object-fit:cover;">`;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });


            // Before submit: serialize data into hidden inputs
            const form = document.querySelector('form');
            form.addEventListener('submit', () => {
                // Xử lý lịch trình từ textarea
                const itineraryTextArea = document.getElementById('itineraryText');
                if (itineraryTextArea && itineraryTextArea.value.trim()) {
                    const lines = itineraryTextArea.value.trim().split('\n').filter(line => line.trim());
                    const itinerary = lines.map((line, idx) => ({
                        day: idx + 1,
                        activity: line.trim()
                    }));
                    itineraryInput.value = JSON.stringify(itinerary);
                } else {
                    itineraryInput.value = '';
                }

                // Xử lý khách sạn từ các form
                const hotelForms = document.querySelectorAll('.hotel-form');
                const hotels = [];

                hotelForms.forEach(form => {
                    const name = form.querySelector('.hotel-name').value.trim();
                    const rating = parseInt(form.querySelector('.hotel-rating').value) || null;
                    const singlePrice = parseInt(form.querySelector('.hotel-single-price').value) || null;
                    const doublePrice = parseInt(form.querySelector('.hotel-double-price').value) || null;

                    if (name) {
                        const hotel = {
                            name: name,
                            rating: rating,
                            image: `assets/images/hotels/${name.toLowerCase().replace(/\s+/g, '-')}.jpg`,
                            rooms: {}
                        };

                        if (singlePrice) hotel.rooms.single = singlePrice;
                        if (doublePrice) hotel.rooms.double = doublePrice;

                        hotels.push(hotel);
                    }
                });

                hotelsInput.value = hotels.length ? JSON.stringify(hotels) : '';
            });

            // Simple Bootstrap validation
            (function() {
                const forms = document.querySelectorAll('.needs-validation');
                Array.from(forms).forEach(f => {
                    f.addEventListener('submit', e => {
                        if (!f.checkValidity()) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                        f.classList.add('was-validated');
                    }, false);
                });
            })();
        </script>
        @push('styles')
            <style>
                .form-label-xs {
                    font-size: 0.7rem;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 0.25rem;
                    color: #495057;
                }

                #singleScreenCard textarea {
                    resize: vertical;
                }

                #galleryStrip {
                    background: #f8f9fa !important;
                }

                #galleryStrip .border {
                    transition: box-shadow 0.15s ease;
                }

                #galleryStrip .border:hover {
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                }

                .hotel-form {
                    background: #f8f9fa;
                    transition: border-color 0.15s ease;
                }

                .hotel-form:hover {
                    border-color: #0d6efd !important;
                }

                .hotel-image-preview img {
                    border-radius: 2px;
                }

                /* Custom file input styling */
                input[type="file"].btn {
                    position: relative;
                    overflow: hidden;
                    cursor: pointer;
                    border: 1px dashed #0d6efd;
                    background: transparent;
                    color: #0d6efd;
                }

                input[type="file"].btn::-webkit-file-upload-button {
                    position: absolute;
                    left: -9999px;
                }

                input[type="file"].btn::before {
                    content: '📁 Chọn ảnh';
                    display: inline-block;
                    width: 100%;
                    text-align: center;
                }

                @media (max-width: 767.98px) {
                    .col-md-4 {
                        margin-bottom: 1rem;
                    }
                }
            </style>
        @endpush
    @endpush
@endsection
