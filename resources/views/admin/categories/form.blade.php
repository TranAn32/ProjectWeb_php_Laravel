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
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light mb-3"><i class="bi bi-arrow-left"></i> Quay lại</a>

        <div class="card">
            <div class="card-header">
                <strong>Thông tin danh mục</strong>
            </div>
            <div class="card-body">
                <form method="post" action="{{ $category->exists ? route('admin.categories.update', $category->categoryID) : route('admin.categories.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if ($category->exists)
                        @method('PUT')
                    @endif

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" name="categoryName" value="{{ old('categoryName', $category->categoryName) }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mô tả</label>
                                <textarea name="description" rows="6" class="form-control">{{ old('description', $category->description) }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Ảnh đại diện</label>
                                <input type="file" name="image" accept="image/*" class="form-control">
                                @if ($category->imageURL)
                                    <div class="mt-2">
                                        <img src="/{{ $category->imageURL }}" alt="" style="width:120px;height:120px;object-fit:cover;border-radius:8px;border:1px solid #eee;">
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Loại</label>
                                    @php($type = old('type', $category->type ?? 'domestic'))
                                    <select name="type" class="form-select">
                                        <option value="domestic" @selected($type==='domestic')>Trong nước</option>
                                        <option value="international" @selected($type==='international')>Quốc tế</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Trạng thái</label>
                                    @php($status = old('status', $category->status ?? 'Active'))
                                    <select name="status" class="form-select">
                                        <option value="Active" @selected($status==='Active')>Active</option>
                                        <option value="Inactive" @selected($status==='Inactive')>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Slug</label>
                                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="form-control" placeholder="du-lich-da-nang">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Thứ tự</label>
                                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="form-control" min="0">
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



