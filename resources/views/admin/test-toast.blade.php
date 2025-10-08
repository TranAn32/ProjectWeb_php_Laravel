@extends('admin.layouts.app')
@section('title', 'Demo Toast Notifications')
@section('page_title', 'Demo Toast Notifications')

@section('content')
    {{-- Include Toast Notification Component --}}
    @include('components.toast-notification')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Test Toast Notifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success btn-block mb-3"
                                    onclick="showSuccessToast('Thành công!', 'Đây là thông báo thành công')">
                                    <i class="fas fa-check"></i> Success Toast
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-danger btn-block mb-3"
                                    onclick="showErrorToast('Lỗi!', 'Đây là thông báo lỗi')">
                                    <i class="fas fa-times"></i> Error Toast
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-warning btn-block mb-3"
                                    onclick="showWarningToast('Cảnh báo!', 'Đây là thông báo cảnh báo')">
                                    <i class="fas fa-exclamation-triangle"></i> Warning Toast
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-info btn-block mb-3"
                                    onclick="showInfoToast('Thông tin!', 'Đây là thông báo thông tin')">
                                    <i class="fas fa-info-circle"></i> Info Toast
                                </button>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary btn-block mb-3"
                                    onclick="showMultipleToasts()">
                                    <i class="fas fa-layer-group"></i> Multiple Toasts
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-secondary btn-block mb-3" onclick="showLongToast()">
                                    <i class="fas fa-clock"></i> Long Duration Toast
                                </button>
                            </div>
                        </div>

                        <hr>

                        <h6>Laravel Session Messages Test:</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route('admin.dashboard') }}?test=success"
                                    class="btn btn-outline-success btn-block">
                                    Test Success Session
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.dashboard') }}?test=error"
                                    class="btn btn-outline-danger btn-block">
                                    Test Error Session
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.dashboard') }}?test=warning"
                                    class="btn btn-outline-warning btn-block">
                                    Test Warning Session
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.dashboard') }}?test=info" class="btn btn-outline-info btn-block">
                                    Test Info Session
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showMultipleToasts() {
                showSuccessToast('Toast 1', 'Thông báo đầu tiên');
                setTimeout(() => {
                    showWarningToast('Toast 2', 'Thông báo thứ hai');
                }, 500);
                setTimeout(() => {
                    showInfoToast('Toast 3', 'Thông báo thứ ba');
                }, 1000);
            }

            function showLongToast() {
                showToast('info', 'Thông báo dài', 'Toast này sẽ hiển thị trong 10 giây', 10000);
            }
        </script>
    @endpush
@endsection
