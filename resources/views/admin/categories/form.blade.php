@extends('admin.layouts.app')
@section('title', ($category->exists ? 'Sửa' : 'Thêm') . ' Danh mục')
@section('page_title', ($category->exists ? 'Sửa' : 'Thêm') . ' Danh mục')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
    <li class="breadcrumb-item active">{{ $category->exists ? 'Sửa' : 'Thêm' }}</li>
@endsection

@section('content')
    <div class="container-fluid">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light mb-3"><i class="bi bi-arrow-left"></i> Quay
            lại</a>

        <div class="card">
            <div class="card-header">
                <strong>Thông tin danh mục</strong>
            </div>
            <div class="card-body">
                <form method="post"
                    action="{{ $category->exists ? route('admin.categories.update', $category->categoryID) : route('admin.categories.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @if ($category->exists)
                        @method('PUT')
                    @endif

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" name="categoryName"
                                    value="{{ old('categoryName', $category->categoryName) }}"
                                    class="form-control form-bordered" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea name="description" rows="2" class="form-control form-bordered">{{ old('description', $category->description) }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Loại</label>
                                    @php($type = old('type', $category->type ?? 'domestic'))
                                    <select name="type" class="form-select form-bordered">
                                        <option value="domestic" @selected($type === 'domestic')>Trong nước</option>
                                        <option value="international" @selected($type === 'international')>Quốc tế</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Trạng thái</label>
                                    @php($status = old('status', $category->status ?? 'Active'))
                                    <select name="status" class="form-select form-bordered">
                                        <option value="Active" @selected($status === 'Active')>Active</option>
                                        <option value="Inactive" @selected($status === 'Inactive')>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Slug</label>
                                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                                        class="form-control form-bordered" placeholder="du-lich-da-nang">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Thứ tự</label>
                                    <input type="number" name="sort_order"
                                        value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                                        class="form-control form-bordered" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Ảnh danh mục</label>
                                <!-- Upload area like tour form (simplified for single image) -->
                                <div id="catUploadArea" class="modern-upload-area">
                                    <i class="bi bi-patch-plus"></i>
                                    <span>Chọn ảnh</span>
                                </div>
                                <input type="file" id="categoryImageInput" name="image" accept="image/*" hidden>
                                <div id="categoryImagePreview" class="single-image-preview">
                                    @if ($category->imageURL)
                                        <div class="preview-wrapper">
                                            <img id="categoryPreviewImg" src="/{{ $category->imageURL }}" alt="Ảnh danh mục"
                                                class="single-image">
                                            <button type="button" id="clearSelectedImage"
                                                class="btn btn-sm btn-outline-secondary">Bỏ chọn</button>
                                        </div>
                                    @else
                                        <div class="text-muted small mt-2">Chưa có ảnh</div>
                                    @endif
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-primary"><i class="bi bi-save"></i> Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style>
        /* Bordered, simple inputs */
        .form-bordered {
            border: 1.5px solid #e1e8ed !important;
            border-radius: 10px;
        }

        .form-bordered:focus {
            border-color: #0D2437 !important;
            box-shadow: 0 0 0 3px rgba(13, 36, 55, .08);
        }

        /* Consistent control sizing and labels */
        .form-control.form-bordered,
        .form-select.form-bordered {
            padding: 10px 14px;
            min-height: 42px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        /* Simple upload area borrowed from tour form (single image) */
        .modern-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .modern-upload-area:hover {
            border-color: #0D2437;
            background: #f1f5f9;
        }

        .single-image-preview {
            margin-top: 10px;
        }

        .single-image {
            width: 100%;
            max-width: 320px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            display: block;
        }

        .preview-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const area = document.getElementById('catUploadArea');
            const fileInput = document.getElementById('categoryImageInput');
            const previewWrap = document.getElementById('categoryImagePreview');
            const clearBtnId = 'clearSelectedImage';

            if (area && fileInput) {
                area.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', () => {
                    if (fileInput.files && fileInput.files[0]) {
                        const file = fileInput.files[0];
                        const url = URL.createObjectURL(file);
                        // Render/replace preview
                        previewWrap.innerHTML = '';
                        const div = document.createElement('div');
                        div.className = 'preview-wrapper';
                        const img = document.createElement('img');
                        img.className = 'single-image';
                        img.id = 'categoryPreviewImg';
                        img.src = url;
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.id = clearBtnId;
                        btn.className = 'btn btn-sm btn-outline-secondary';
                        btn.textContent = 'Bỏ chọn';
                        btn.addEventListener('click', () => {
                            fileInput.value = '';
                            URL.revokeObjectURL(url);
                            // Clear preview but keep a note for user
                            previewWrap.innerHTML =
                                '<div class="text-muted small mt-2">Chưa có ảnh</div>';
                        });
                        div.appendChild(img);
                        div.appendChild(btn);
                        previewWrap.appendChild(div);
                    }
                });
            }
        });
    </script>
@endpush
