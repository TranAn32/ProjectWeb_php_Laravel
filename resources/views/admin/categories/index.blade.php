@extends('admin.layouts.app')
@section('title', 'Danh mục Tour')
@section('page_title', 'Danh mục Tour')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Danh mục</li>
@endsection

@section('page_actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Thêm danh mục</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px">ID</th>
                            <th>Hình</th>
                            <th>Tên danh mục</th>
                            <th>Loại</th>
                            <th>Trạng thái</th>
                            <th style="width: 150px" class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->categoryID }}</td>
                                <td>
                                    @if ($category->imageURL)
                                        <img src="/{{ $category->imageURL }}" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $category->categoryName }}</td>
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
                                    <a href="{{ route('admin.categories.edit', $category->categoryID) }}" class="btn btn-light btn-icon" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->categoryID) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa danh mục này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-light btn-icon text-danger" title="Xóa"><i class="bi bi-trash3"></i></button>
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



