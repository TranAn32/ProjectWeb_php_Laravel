@extends('admin.layouts.app')
@section('title', 'Users')
@section('page_title', 'Người dùng')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Người dùng</li>
@endsection
@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 small align-items-end">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label mb-1">Từ khóa</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Tên hoặc email">
                </div>
                <div class="col-sm-4 col-md-2">
                    <label class="form-label mb-1">Vai trò</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="user" @selected(request('role') === 'user')>User</option>
                    </select>
                </div>
                <div class="col-sm-4 col-md-2">
                    <label class="form-label mb-1">Trạng thái</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        <option value="Active" @selected(request('status') === 'Active')>Active</option>
                        <option value="Locked" @selected(request('status') === 'Locked')>Locked</option>
                    </select>
                </div>
                <div class="col-sm-4 col-md-2">
                    <label class="form-label mb-1">Sắp xếp</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="new" @selected(request('sort') === 'new')>Mới nhất</option>
                        <option value="old" @selected(request('sort') === 'old')>Cũ nhất</option>
                        <option value="name" @selected(request('sort') === 'name')>Tên (A-Z)</option>
                    </select>
                </div>
                <div class="col-sm-4 col-md-2">
                    <button class="btn btn-secondary btn-sm w-100"><i class="fa fa-filter me-1"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th style="width:120px;">Vai trò</th>
                        <th style="width:120px;">Trạng thái</th>
                        <th style="width:140px;">Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="text-muted small">#{{ $u->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $u->name }}</div>
                                <div class="small text-muted">{{ $u->email }}</div>
                            </td>
                            <td>
                                <span
                                    class="badge bg-{{ $u->role === 'admin' ? 'primary' : 'light text-dark' }}">{{ $u->role ?? 'user' }}</span>
                            </td>
                            <td>
                                @php($st = $u->status ?? 'Active')
                                <span
                                    class="badge bg-{{ $st === 'Active' ? 'success' : 'secondary' }}">{{ $st }}</span>
                            </td>
                            <td><span class="small text-muted">{{ $u->created_at?->format('d/m/Y') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">@include('admin.partials.empty', [
                                '__data' => [
                                    'empty_title' => 'Chưa có người dùng',
                                    'empty_message' => 'Khi người dùng đăng ký sẽ xuất hiện ở đây',
                                ],
                            ])</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            @include('admin.partials.pagination-summary', ['paginator' => $users])
        </div>
    </div>
@endsection
