@extends('admin.layouts.app')
@section('title','Tours')
@section('page_title','Quản lý Tours')
@section('content')
<a href="{{ route('admin.tours.create') }}" class="btn btn-primary mb-3">+ Thêm tour</a>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Giá</th>
            <th>Loại</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($tours as $t)
        <tr>
            <td>{{ $t->tourID }}</td>
            <td>{{ $t->title }}</td>
            <td>{{ number_format($t->priceAdult) }}</td>
            <td>{{ $t->tourType }}</td>
            <td>
                <a href="{{ route('admin.tours.edit',$t->tourID) }}">Sửa</a> |
                <form action="{{ route('admin.tours.destroy',$t->tourID) }}" method="post" style="display:inline-block;" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE') <button class="btn btn-link p-0">Xóa</button></form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">Chưa có tour</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $tours->links() }}
@endsection