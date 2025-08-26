@extends('admin.layouts.app')
@section('title','Admin Login')
@section('page_title','Đăng nhập Admin')
@section('content')
<div style="max-width:420px;margin:40px auto;">
    <form method="post" action="{{ route('admin.login.post') }}" class="panel">
        @csrf
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label><input type="checkbox" name="remember"> Ghi nhớ</label>
        </div>
        @if($errors->any())
        <div class="text-muted" style="color:#c00;">{{ $errors->first() }}</div>
        @endif
        <button class="btn btn-primary w-100">Đăng nhập</button>
    </form>
</div>
@endsection