@extends('admin.layouts.app')
@section('title', 'Quản lý Slideshow')
@section('content')
    <div class="container-fluid py-3">
        <h1 class="h4 mb-3">Quản lý Slideshow</h1>

        {{-- Thông báo dùng toast-notification qua session success/error & $errors (đã include trong layout) --}}

        {{-- Upload area: một nút "Thêm ảnh" lớn, chọn nhiều file và tự submit khi chọn xong --}}
        <div class="card mb-4">
            <div class="card-body d-flex flex-wrap align-items-center gap-3">
                <form id="uploadForm" action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data"
                    class="d-flex align-items-center gap-3">
                    @csrf
                    <input id="fileInput" type="file" name="images[]" accept="image/*" multiple required hidden>
                    <button id="btnAddImages" type="button" class="btn btn-success">
                        <i class="fa fa-plus me-1"></i> Thêm ảnh
                    </button>
                    <span class="text-muted small">Hỗ trợ: jpg, jpeg, png, gif, webp, avif • Tối đa 5MB/ảnh</span>
                </form>
            </div>
        </div>

        {{-- Danh sách ảnh theo bảng, hàng cao và thumbnail lớn --}}
        <div class="card">
            <div class="card-body">
                @if (empty($slides))
                    <div class="alert alert-info mb-0">Chưa có ảnh trong slideshow.</div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:220px;">Ảnh</th>
                                    <th>Tên file</th>
                                    <th style="width:170px;">Cập nhật</th>
                                    <th style="min-width:260px;">Đổi tên</th>
                                    <th style="width:140px;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($slides as $s)
                                    @php
                                        $mtime = (int) ($s['mtime'] ?? time());
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $s['src'] }}" alt="{{ $s['title'] }}"
                                                    class="slide-thumb">
                                            </div>
                                        </td>
                                        <td class="text-break">{{ $s['file'] }}</td>
                                        <td>{{ date('Y-m-d H:i', $mtime) }}</td>
                                        <td>
                                            <form action="{{ route('admin.slides.update', $s['file']) }}" method="POST"
                                                class="d-flex gap-2">
                                                @csrf
                                                @method('PUT')
                                                @php $baseName = pathinfo($s['file'], PATHINFO_FILENAME); @endphp
                                                <input type="text" name="new_name" class="form-control form-control-sm"
                                                    value="{{ $baseName }}" placeholder="Tên mới (không cần đuôi)">
                                                <button class="btn btn-sm btn-primary" type="submit">Lưu</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.slides.destroy', $s['file']) }}" method="POST"
                                                onsubmit="return confirm('Xóa ảnh này?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit"><i
                                                        class="fa fa-trash"></i> Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .slide-thumb {
                width: 200px;
                height: 120px;
                object-fit: cover;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
            }

            .table td,
            .table th {
                vertical-align: middle;
            }

            /* Hover preview overlay */
            .img-hover-overlay {
                position: fixed;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(0, 0, 0, .45);
                z-index: 1060;
                pointer-events: none;
                /* only to display on hover */
                opacity: 0;
                animation: imgOverlayFadeIn .12s ease forwards;
            }

            .img-hover-overlay img {
                max-width: 85vw;
                max-height: 85vh;
                border-radius: 10px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, .4);
            }

            @keyframes imgOverlayFadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.getElementById('btnAddImages');
                const input = document.getElementById('fileInput');
                const form = document.getElementById('uploadForm');
                if (btn && input && form) {
                    btn.addEventListener('click', () => input.click());
                    input.addEventListener('change', () => {
                        if (input.files && input.files.length) {
                            form.submit();
                        }
                    });
                }

                // Hover preview popup for thumbnails
                let overlay = null;
                const showOverlay = (src) => {
                    hideOverlay();
                    overlay = document.createElement('div');
                    overlay.className = 'img-hover-overlay';
                    const img = new Image();
                    img.src = src;
                    overlay.appendChild(img);
                    document.body.appendChild(overlay);
                };
                const hideOverlay = () => {
                    if (overlay) {
                        overlay.remove();
                        overlay = null;
                    }
                };
                document.querySelectorAll('.slide-thumb').forEach(img => {
                    img.addEventListener('mouseenter', () => showOverlay(img.src));
                    img.addEventListener('mouseleave', hideOverlay);
                    img.addEventListener('click', hideOverlay);
                });
            });
        </script>
    @endpush
@endsection
