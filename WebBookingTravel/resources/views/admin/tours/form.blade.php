@extends('admin.layouts.app')
@section('title', ($tour->exists ? 'Sửa' : 'Thêm') . ' Tour')
@section('page_title', ($tour->exists ? 'Sửa' : 'Thêm') . ' Tour')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.tours.index') }}">Tours</a></li>
    <li class="breadcrumb-item active">{{ $tour->exists ? 'Sửa' : 'Thêm' }}</li>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    /* Global Styles */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: #f5f7fa;
    }
    
    /* Modern Card Styling */
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(13, 36, 55, 0.08);
        border: 1px solid rgba(13, 36, 55, 0.06);
        margin-bottom: 24px;
        overflow: hidden;
        transition: all 0.3s ease;
        
    }
    
    .modern-card:hover {
        box-shadow: 0 4px 12px rgba(13, 36, 55, 0.12);
    }
    
    /* Card Header */
    .modern-card-header {
        background: linear-gradient(135deg, #0D2437 0%, #1a3a52 100%);
        color: white;
        padding: 20px 28px;
        border-bottom: none;
    }
    
    .modern-card-header h5 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        letter-spacing: -0.3px;
    }
    
    .modern-card-header .badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 6px;
    }
    
    /* Card Body */
    .modern-card-body {
        padding: 32px 28px;
    }
    
    /* Form Labels */
    .modern-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #495057;
        margin-bottom: 8px;
        display: block;
    }
    
    /* Form Controls */
    .modern-input,
    .modern-select,
    .modern-textarea {
        width: 100%;
        padding: 12px 16px;
        font-size: 15px;
        border: 1.5px solid #e1e8ed;
        border-radius: 10px;
        transition: all 0.2s ease;
        background: white;
        font-family: 'Inter', sans-serif;
    }
    
    /* .modern-input:focus,
    .modern-select:focus,
    .modern-textarea:focus {
        outline: none;
        border-color: #2b353d;
        box-shadow: 0 0 0 4px rgba(13, 36, 55, 0.08);
    } */
    
    .modern-textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .modern-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%230D2437' d='M8 11L3 6h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 40px;
        cursor: pointer;
    }
    
    /* Image Upload Area */
    .modern-upload-area {
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 20px;
        width: 12px
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modern-upload-area:hover {
        border-color: #0D2437;
        background: #f1f5f9;
    }
    
    .upload-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.6;
    }
    
    .upload-text {
        font-size: 15px;
        font-weight: 500;
        color: #334155;
        margin-bottom: 6px;
    }
    
    .upload-hint {
        font-size: 13px;
        color: #94a3b8;
    }
    
    /* Image Preview Grid */
    .image-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
        margin-top: 16px;
    }
    
    .image-preview-item {
        position: relative;
        aspect-ratio: 4/3;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid #e1e8ed;
        background: #f8fafc;
        transition: all 0.2s ease;
    }
    
    .image-preview-item:hover {
        border-color: #0D2437;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 36, 55, 0.15);
    }
    
    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .cover-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #0D2437;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Hotel Cards */
    .hotel-card {
        background: #f8fafc;
        border: 1.5px solid #e1e8ed;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    
    .hotel-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #0D2437;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e1e8ed;
    }
    
    /* Buttons */
    .modern-btn {
        padding: 12px 28px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 10px;
        border: none;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .modern-btn-primary {
        background: #0D2437;
        color: white;
    }
    
    .modern-btn-primary:hover {
        background: #1a3a52;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 36, 55, 0.25);
    }
    
    .modern-btn-secondary {
        background: #e1e8ed;
        color: #334155;
    }
    
    .modern-btn-secondary:hover {
        background: #cbd5e0;
    }
    
    .modern-btn-lg {
        padding: 16px 48px;
        font-size: 16px;
    }
    
    /* Alert Boxes */
    .modern-alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
        border-left: 4px solid;
    }
    
    .modern-alert-info {
        background: #e8f4f8;
        border-color: #0D2437;
        color: #0D2437;
    }
    
    .modern-alert-warning {
        background: #fff8e1;
        border-color: #ffa726;
        color: #e65100;
    }
    
    /* Grid Layouts */
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    /* Back Button */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: white;
        border: 1.5px solid #e1e8ed;
        border-radius: 10px;
        color: #334155;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s ease;
        margin-bottom: 20px;
    }
    
    .back-btn:hover {
        border-color: #0D2437;
        color: #0D2437;
        transform: translateX(-4px);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .form-grid-2,
        .form-grid-3 {
            grid-template-columns: 1fr;
        }
        
        .modern-card-body {
            padding: 24px 20px;
        }
        
        .modern-card-header {
            padding: 16px 20px;
        }
        
        .image-preview-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        }
    }
    
    /* Required Asterisk */
    .text-danger {
        color: #dc3545;
        margin-left: 4px;
    }
    
    /* Tooltip Hint */
    .hint-icon {
        display: inline-block;
        width: 16px;
        height: 16px;
        background: #94a3b8;
        color: white;
        border-radius: 50%;
        text-align: center;
        line-height: 16px;
        font-size: 11px;
        margin-left: 6px;
        cursor: help;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <a href="{{ route('admin.tours.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i>
        <span>Quay lại</span>
    </a>

    <form method="post"
        action="{{ $tour->exists ? route('admin.tours.update', $tour->tourID) : route('admin.tours.store') }}"
        enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
        @if ($tour->exists)
            @method('PUT')
        @endif
        
        <div class="modern-card">
            <div class="modern-card-body">
                <div class="row g-4">
                     <label class="modern-label" style="font-weight: 700"> Thông tin cơ bản </label>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="modern-label">Tên tour <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $tour->title) }}"
                                class="modern-input" required >
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="modern-label">Điểm khởi hành</label>
                                <input type="text" name="departurePoint"
                                    value="{{ old('departurePoint', $tour->departurePoint) }}"
                                    class="modern-input">
                            </div>
                            <div class="form-group">
                                <label class="modern-label">Điểm đến</label>
                                <input type="text" name="destinationPoint"
                                    value="{{ old('destinationPoint', $tour->destinationPoint) }}"
                                    class="modern-input" >
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="modern-label">Giá người lớn (VNĐ)</label>
                                <input type="number" min="0" name="priceAdult"
                                    value="{{ old('priceAdult', $tour->priceAdult) }}" class="modern-input"
                                    >
                            </div>
                            <div class="form-group">
                                <label class="modern-label">Giá trẻ em (VNĐ)</label>
                                <input type="number" min="0" name="priceChild"
                                    value="{{ old('priceChild', $tour->priceChild) }}" class="modern-input"
                                   >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="modern-label">Trạng thái</label>
                            @php($status = old('status', $tour->status))
                            <select name="status" class="modern-select">
                                <option value="draft" @selected($status === 'draft')>Nháp</option>
                                <option value="published" @selected($status === 'published')>Công khai</option>
                                <option value="canceled" @selected($status === 'canceled')>Hủy</option>
                            </select>
                        </div>
                    </div>

                    
                    <div class="col-lg-4">
                        <label class="modern-label">Hình ảnh tour <span class="text-danger">*</span></label>
                        <div class="modern-upload-area" onclick="document.getElementById('galleryInput').click()" style="width: 250px;">
                            <i class="bi bi-patch-plus"></i> Thêm ảnh
                        </div>
                        <input type="file" id="galleryInput" name="images[]" accept="image/*" multiple
                            style="display: none;" @if(!$tour->exists) required @endif>
                        <div id="imagePreviewGrid" class="image-preview-grid"></div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="modern-label">Mô tả tour</label>
                            <textarea name="description" class="modern-textarea" rows="8"
                               >{{ old('description', $tour->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modern-card">
            <div class="modern-card-body">
                <div class="form-group">
                    <label class="modern-label" style="font-weight: 700">Lịch trình tour</label>
                    <textarea id="itineraryText" name="itinerary_text" class="modern-textarea" rows="8"
                        >{{ old('itinerary_text', '') }}</textarea>
                </div>
            </div>
        </div>

        
        <div class="modern-card">
            <div class="modern-card-body" id="hotelsFixed">
                 <label class="modern-label" style="font-weight: 700">  Khách sạn  </label>
                <div class="hotel-card" data-slot="1">
                    <div class="hotel-card-title">Khách sạn 1</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="modern-label">Tên khách sạn <span class="text-danger">*</span></label>
                                <input type="text" class="modern-input hotel-name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="modern-label">Hạng sao <span class="text-danger">*</span></label>
                                <select class="modern-select hotel-rating" required>
                                    <option value="">Chọn hạng sao</option>
                                    <option value="1">⭐ 1 sao</option>
                                    <option value="2">⭐⭐ 2 sao</option>
                                    <option value="3">⭐⭐⭐ 3 sao</option>
                                    <option value="4">⭐⭐⭐⭐ 4 sao</option>
                                    <option value="5">⭐⭐⭐⭐⭐ 5 sao</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="modern-label">Giá phòng đơn (VNĐ)</label>
                                <input type="number" class="modern-input hotel-single-price">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="modern-label">Giá phòng đôi (VNĐ)</label>
                                <input type="number" class="modern-input hotel-double-price">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="modern-label">Hình ảnh khách sạn</label>
                            <div class="modern-upload-area hotel-image-preview" onclick="this.nextElementSibling.click()" style="width: 150px;">
                                 <i class="bi bi-patch-plus"></i> Thêm ảnh
                            </div>
                            <input type="file" class="hotel-image" accept="image/*" name="hotel_images[]" style="display: none;">
                        </div>
                    </div>
                </div>

                <div class="hotel-card" data-slot="2">
                    <div class="row g-3">
                        <div class="hotel-card-title">Khách sạn 2</div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="modern-label">Tên khách sạn <span class="text-danger">*</span></label>
                                <input type="text" class="modern-input hotel-name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="modern-label">Hạng sao <span class="text-danger">*</span></label>
                                <select class="modern-select hotel-rating" required>
                                    <option value="">Chọn hạng sao</option>
                                    <option value="1">⭐ 1 sao</option>
                                    <option value="2">⭐⭐ 2 sao</option>
                                    <option value="3">⭐⭐⭐ 3 sao</option>
                                    <option value="4">⭐⭐⭐⭐ 4 sao</option>
                                    <option value="5">⭐⭐⭐⭐⭐ 5 sao</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="modern-label">Giá phòng đơn (VNĐ)</label>
                                <input type="number" class="modern-input hotel-single-price">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="modern-label">Giá phòng đôi (VNĐ)</label>
                                <input type="number" class="modern-input hotel-double-price" >
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="modern-label">Hình ảnh khách sạn</label>
                            <div class="modern-upload-area hotel-image-preview" onclick="this.nextElementSibling.click()" style="width: 150px;">
                                 <i class="bi bi-patch-plus"></i> Thêm ảnh
                            </div>
                            <input type="file" class="hotel-image" accept="image/*" name="hotel_images[]" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

      
        <div class="modern-card">
            <div class="modern-card-body text-center">
                <button type="submit" class="modern-btn modern-btn-primary modern-btn-lg">
                    <i class="bi bi-save"></i>
                    <span>Lưu Tour</span>
                </button>
            </div>
        </div>

        <input type="hidden" name="itinerary" id="itineraryInput" />
        <input type="hidden" name="hotels" id="hotelsInput" />
    </form>
</div>

@push('scripts')
<script>
    // ... existing code ...
    
    function safeJsonParse(val, fallback) {
        if (val === undefined || val === null) return fallback;
        if (typeof val === 'object') return val;
        if (typeof val === 'string') {
            const str = val.trim();
            if (!str) return fallback;
            try { return JSON.parse(str); } catch { return fallback; }
        }
        return fallback;
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeHotelsSection();
    });

    if (document.readyState !== 'loading') {
        setTimeout(initializeHotelsSection, 100);
    }

    const existingImages = safeJsonParse(@json(old('images', is_string($tour->images) ? $tour->images : json_encode($tour->images))), []);
    const existingItinerary = safeJsonParse(@json(old('itinerary', is_string($tour->itinerary) ? $tour->itinerary : json_encode($tour->itinerary))), []);
    const existingHotels = safeJsonParse(@json(old('hotels', is_string($tour->hotels) ? $tour->hotels : json_encode($tour->hotels))), []);

    const itineraryInput = document.getElementById('itineraryInput');
    const hotelsInput = document.getElementById('hotelsInput');
    const galleryInput = document.getElementById('galleryInput');
    const imagePreviewGrid = document.getElementById('imagePreviewGrid');
    const itineraryText = document.getElementById('itineraryText');
    const hotelsFixed = document.getElementById('hotelsFixed');

    // <CHANGE> Updated image preview to use modern grid layout
    galleryInput?.addEventListener('change', e => {
        const files = Array.from(e.target.files || []);
        imagePreviewGrid.innerHTML = '';
        
        if (!files.length) return;

        files.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = ev => {
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                div.innerHTML = `
                    <img src="${ev.target.result}" alt="Preview">
                    ${idx === 0 ? '<span class="cover-badge">Ảnh bìa</span>' : ''}
                `;
                imagePreviewGrid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    if (existingItinerary.length > 0) {
        const itineraryLines = existingItinerary.map(item =>
            `Ngày ${item.day}: ${item.activity}`
        ).join('\n');
        if (itineraryText) itineraryText.value = itineraryLines;
    }

    function initializeHotelsSection() {
        const slots = hotelsFixed?.querySelectorAll('.hotel-card');
        if (!slots) return;

        const seed = Array.isArray(existingHotels) ? existingHotels.slice(0, 2) : [];
        slots.forEach((slotEl, idx) => {
            const data = seed[idx];
            const nameInput = slotEl.querySelector('.hotel-name');
            const ratingSelect = slotEl.querySelector('.hotel-rating');
            const singlePrice = slotEl.querySelector('.hotel-single-price');
            const doublePrice = slotEl.querySelector('.hotel-double-price');
            const preview = slotEl.querySelector('.hotel-image-preview');

            if (data && data.name) {
                nameInput.value = data.name || '';
                if (data.rating) ratingSelect.value = String(data.rating);
                if (data.rooms?.single) singlePrice.value = data.rooms.single;
                if (data.rooms?.double) doublePrice.value = data.rooms.double;
                if (data.image) {
                    preview.innerHTML = `<img src="${data.image}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`;
                }
            }
        });
    }

    // <CHANGE> Updated hotel image preview with modern styling
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('hotel-image')) {
            const file = e.target.files[0];
            const preview = e.target.previousElementSibling;

            if (file && preview) {
                const reader = new FileReader();
                reader.onload = ev => {
                    preview.innerHTML = `<img src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`;
                };
                reader.readAsDataURL(file);
            }
        }
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', (e) => {
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

        const slots = document.querySelectorAll('#hotelsFixed .hotel-card');
        const hotels = [];
        slots.forEach(slot => {
            const name = slot.querySelector('.hotel-name').value.trim();
            const rating = parseInt(slot.querySelector('.hotel-rating').value) || null;
            const singlePrice = parseInt(slot.querySelector('.hotel-single-price').value) || null;
            const doublePrice = parseInt(slot.querySelector('.hotel-double-price').value) || null;
            if (!name) return;
            const hotel = { name, rating, rooms: {} };
            if (singlePrice) hotel.rooms.single = singlePrice;
            if (doublePrice) hotel.rooms.double = doublePrice;
            hotels.push(hotel);
        });

        if (hotels.length !== 2) {
            hotelsInput.value = '';
            alert('Vui lòng nhập đầy đủ thông tin cho 2 khách sạn.');
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        hotelsInput.value = JSON.stringify(hotels);
    });

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
@endpush
@endsection