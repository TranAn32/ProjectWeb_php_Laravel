<div class="empty-state">
    <i class="fa fa-box-open"></i>
    <h5 class="mt-3">@yield('empty_title', 'Không có dữ liệu')</h5>
    <p class="text-muted mb-0">@yield('empty_message', 'Chúng tôi sẽ cập nhật thêm sớm.')</p>
    @hasSection('empty_actions')
        <div class="mt-3">@yield('empty_actions')</div>
    @endif
</div>
