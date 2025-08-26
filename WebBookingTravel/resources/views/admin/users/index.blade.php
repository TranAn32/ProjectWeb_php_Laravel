@extends('admin.layouts.app')
@section('title','Users')
@section('page_title','Người dùng')
@section('content')
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Vai trò</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $u)
        <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->role ?? 'user' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">Chưa có người dùng</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $users->links() }}
@endsection