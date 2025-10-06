@extends('admin.layouts.app')
@section('title', 'Tours')
@section('page_title', 'Tours')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Tours</li>
@endsection

@push('styles')
    <style>
        /* Tour index specific styles */
        .btn-icon {
            --btn-size: 32px;
            width: var(--btn-size);
            height: var(--btn-size);
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d7dce1;
            font-size: 13px;
        }

        .btn-icon:hover {
            background: #f1f4f7;
        }

        .btn-icon.text-danger:hover {
            background: #fdecea;
            border-color: #f5c1bb;
        }

        .tour-title-cell {
            position: relative;
        }

        .tour-title-wrapper {
            position: relative;
            padding: 6px 0;
        }

        .tour-title-text {
            transition: all .18s ease;
            font-weight: 600;
        }

        .tour-actions-hover {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 4px;
            display: flex;
            gap: 6px;
            opacity: 0;
            transform: translateY(6px);
            transition: all .2s ease;
            top: 20px;
            /* chỉnh con số này để tạo khoảng cách */
            left: 0;
            right: 0;
            bottom: auto;
        }

        .tour-title-cell:hover .tour-title-text {
            transform: translateY(-10px);
        }

        .tour-title-cell:hover .tour-actions-hover {
            opacity: 1;
            transform: translateY(0);
        }

        .tour-actions-hover .btn-icon {
            --btn-size: 30px;
            font-size: 12px;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(13, 36, 55, 0.08);
            border: 1px solid rgba(13, 36, 55, 0.06);
            margin-bottom: 24px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-body .btn {

            align-items: center;
            /* dọc */
        }
    </style>
@endpush
@section('page_actions')
    {{-- Nút tạo mới đã chuyển xuống form lọc để dễ thao tác --}}
@endsection
@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="" class="row g-2 align-items-end small">
                <div style="padding-bottom: 12px;" class="col-sm-4 col-md-2 order-0">
                    <button style="width: 110px !important; font-weight: 600; font-size: 15px; padding: 12px 18px; border-radius: 10px;"
                        type="button" onclick="window.location='{{ route('admin.tours.create') }}'"
                        class="btn btn-primary btn-sm w-50 d-flex align-items-center justify-content-center">
                        Thêm mới
                    </button>
                </div>
                <div class="col-sm-8 col-md-3 order-1">
                    <input
                        style=" border: 1.5px solid #e1e8ed;
        border-radius: 10px;
        transition: all 0.2s ease;
        background: white;
        font-family: 'Inter', sans-serif;"
                        type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Tìm tiêu đề...">
                </div>
                <div class="col-sm-4 col-md-2 order-2">
                    <label class="form-label mb-1">Trạng thái</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        @foreach (['draft' => 'Nháp', 'published' => 'Công khai', 'canceled' => 'Hủy'] as $k => $v)
                            <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 col-md-2 order-3">
                    <label class="form-label mb-1">Sắp xếp</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="new" @selected(request('sort') === 'new')>Mới nhất</option>
                        <option value="old" @selected(request('sort') === 'old')>Cũ nhất</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá tăng</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá giảm</option>
                    </select>
                </div>
                {{-- <div class="col-sm-4 col-md-2 order-4">
                    <label class="form-label mb-1">Hiển thị</label>
                    <select name="per_page" class="form-select form-select-sm">
                        @foreach ([10, 25, 50] as $n)
                            <option value="{{ $n }}" @selected(request('per_page', $perPage ?? 10) == $n)> {{ $n }} </option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="col-sm-4 col-md-2 order-5">
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
                        <th style="width:68px;">ID</th>
                        <th style="width:90px;">Ảnh</th>
                        <th>Tiêu đề</th>
                        <th style="width:140px;" class="d-none d-lg-table-cell">Điểm đón</th>
                        <th style="width:140px;" class="d-none d-lg-table-cell">Điểm đến</th>
                        <th style="width:140px;">Giá</th>
                        <th style="width:120px;">Trạng thái</th>
                        <th style="width:120px;" class="d-none d-md-table-cell">Cập nhật</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($tours as $t)
                        @php
                            // image_path accessor now rens absolute/asset()'d URL when possible
$imgSrc = $t->image_path ?? asset('assets/images/destinations/dest1.jpg');
                        @endphp
                        <tr>
                            <td class="text-muted small">#{{ $t->tourID }}</td>
                            <td>
                                <img src="{{ $imgSrc }}" alt="{{ $t->title }}" loading="lazy"
                                    style="width:72px;height:54px;object-fit:cover;border-radius:6px;border:1px solid #e2e6ea;"
                                    onerror="this.onerror=null;this.src='{{ asset('assets/images/destinations/dest1.jpg') }}';">
                            </td>
                            <td class="tour-title-cell" style="max-width:320px;">
                                <div class="tour-title-wrapper" title="{{ $t->title }}">
                                    <div class="tour-title-text text-truncate">{{ $t->title }}</div>
                                    <div class="tour-actions-hover">
                                        <a href="{{ route('admin.tours.edit', $t->tourID) }}"
                                            class="btn btn-light btn-icon" data-bs-toggle="tooltip" title="Sửa">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.tours.destroy', $t->tourID) }}" method="post"
                                            onsubmit="return confirm('Xóa tour này?')" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-light btn-icon text-danger" data-bs-toggle="tooltip"
                                                title="Xóa">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="small">{{ $t->pickupPoint ?: '-' }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="small">{{ $t->departurePoint ?: '-' }}</span>
                            </td>
                            <td class="text-nowrap">
                                @php($adult = $t->priceAdult)
                                @php($child = $t->priceChild)
                                @if (!is_null($adult) || !is_null($child))
                                    @if (!is_null($adult))
                                        <div class="fw-semibold">{{ number_format($adult, 0, ',', '.') }}<small
                                                class="text-muted"> đ/NL</small></div>
                                    @endif
                                    @if (!is_null($child))
                                        <div class="small">{{ number_format($child, 0, ',', '.') }} <span
                                                class="text-muted">đ/TE</span></div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php($st = $t->status)
                                <span class="status-badge status-{{ $st ?? 'draft' }}">
                                    @switch($st)
                                        @case('published')
                                            published
                                        @break

                                        @case('canceled')
                                            private
                                        @break

                                        @default
                                            Nháp
                                    @endswitch
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                @php($updated = $t->updated_at)
                                <span class="small text-muted">
                                    @if ($updated instanceof \Carbon\CarbonInterface)
                                        {{ $updated->format('d/m/Y') }}
                                    @elseif(is_string($updated))
                                        {{ \Illuminate\Support\Str::of($updated)->limit(10, '') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>

                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-0">
                                    @include('admin.partials.empty', [
                                        '__data' => [
                                            'empty_title' => 'Chưa có tour',
                                            'empty_message' => 'Hãy thêm tour mới để bắt đầu',
                                        ],
                                    ])
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                @include('admin.partials.pagination-summary', ['paginator' => $tours])
            </div>
        </div>
    @endsection
