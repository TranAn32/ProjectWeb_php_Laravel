@extends('admin.layouts.app')
@section('title', 'Danh mục Tour')
@section('page_title', 'Danh mục Tour')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Danh mục</li>
@endsection

@section('page_actions')
    {{-- Moved the "Thêm danh mục" button to the bottom footer --}}
@endsection

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <div class="d-flex justify-content-start p-3 border-bottom">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Thêm danh mục
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 category-table">
                    <thead>
                        <tr>
                            <th style="width: 90px">ID</th>
                            <th style="min-width: 220px;">Hình</th>
                            <th style="min-width: 240px;">Tên danh mục</th>
                            <th style="width: 140px;">Loại</th>
                            <th style="width: 140px;">Trạng thái</th>
                            <th style="width: 170px" class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->categoryID }}</td>
                                <td>
                                    @if ($category->imageURL)
                                        <img src="/{{ $category->imageURL }}" alt="{{ $category->categoryName }}"
                                            class="category-thumb">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $category->categoryName }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $category->type }}</span>
                                </td>
                                <td>
                                    @if ($category->status === 'Active')
                                        <span class="status-badge status-published">Active</span>
                                    @else
                                        <span class="status-badge status-draft">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.categories.edit', $category->categoryID) }}"
                                        class="btn btn-outline-primary btn-sm" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->categoryID) }}"
                                        method="POST" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" title="Xóa"><i
                                                class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fa fa-folder-open"></i>
                                        <div class="mt-2">Chưa có danh mục nào</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if (method_exists($categories, 'links'))
            <div class="card-footer">{{ $categories->links() }}</div>
        @endif
    </div>
@endsection


@push('styles')
    <style>
        /* Larger rows and thumbnails for category list */
        .category-table thead th {
            font-weight: 600;
        }

        .category-table td,
        .category-table th {
            padding: 1rem 1rem;
        }

        .category-thumb {
            width: 160px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
            cursor: default;
        }

        /* Removed hover overlay and hover effects per request */
    </style>
@endpush
