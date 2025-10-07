@php<div class="empty-state">

    $emptyTitle = $__data['empty_title'] ?? 'Không có dữ liệu';    <i class="fa fa-box-open"></i>

    $emptyMessage = $__data['empty_message'] ?? 'Chưa có dữ liệu để hiển thị';    <h5 class="mt-3">@yield('empty_title', 'Không có dữ liệu')</h5>

@endphp ?> <p class="text-muted mb-0">@yield('empty_message', 'Chúng tôi sẽ cập nhật thêm sớm.')</p>

@hasSection('empty_actions')
    <div class="text-center py-5">
        <div class="mt-3">@yield('empty_actions')</div>

        <div class="mb-3">
@endif

<i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i></div>

</div>
<h6 class="text-muted mb-2">{{ $emptyTitle }}</h6>
<p class="text-muted small mb-0">{{ $emptyMessage }}</p>
</div>
