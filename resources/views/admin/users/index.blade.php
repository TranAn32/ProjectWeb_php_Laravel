@extends('admin.layouts.app')
@section('title', 'Quản lý người dùng')
@section('page_title', 'Người dùng')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Người dùng</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between p-3 border-bottom">
                <form method="get" class="d-flex gap-2">
                    <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control"
                        placeholder="Tìm tên, email, SĐT">
                    <button class="btn btn-outline-secondary">Tìm</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:90px;">ID</th>
                            <th style="min-width:220px;">Tên</th>
                            <th style="min-width:220px;">Email</th>
                            <th style="min-width:140px;">SĐT</th>
                            <th style="min-width:120px;">Vai trò</th>
                            <th style="min-width:120px;">Trạng thái</th>
                            <th style="width:200px;" class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>{{ $u->user_id }}</td>
                                <td class="fw-medium">{{ $u->username }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->phone_number }}</td>
                                <td><span class="badge bg-secondary">{{ $u->role ?? 'user' }}</span></td>
                                <td>
                                    @php($isActive = strtolower((string) ($u->status ?? '')) === 'active')
                                    @if ($isActive)
                                        <span class="status-badge status-published">Active</span>
                                    @else
                                        <span class="status-badge status-draft">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.users.updateStatus', $u->user_id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status"
                                            value="{{ strtolower((string) ($u->status ?? '')) === 'active' ? 'Inactive' : 'Active' }}">
                                        @if ($isActive)
                                            <button class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Khóa (ban) người dùng này?')">
                                                <i class="bi bi-slash-circle"></i> Ban
                                            </button>
                                        @else
                                            <button class="btn btn-outline-success btn-sm"
                                                onclick="return confirm('Mở khóa người dùng này?')">
                                                <i class="bi bi-unlock"></i> Unban
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state text-center p-4">
                                        <i class="fa fa-users"></i>
                                        <div class="mt-2">Chưa có người dùng</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if (method_exists($users, 'links'))
            <div class="card-footer">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
