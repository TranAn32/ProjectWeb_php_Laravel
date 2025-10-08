@extends('client.layouts.app')
@section('title', 'Tour đã đặt')
@section('content')

    <style>
        .form-control,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 1.5px solid #e1e8ed;
            border-radius: 10px;
            transition: all 0.2s ease;
            background: white;
            font-family: 'Inter', sans-serif;
            max-height: 45px;
            line-height: 25px;
            margin-bottom: 20px;
        }
    </style>
    <div class="container py-4" style="min-height: 90vh"  >
        <h1 class="h4 mb-3">Tour đã đặt</h1>

        <!-- Bộ lọc trạng thái -->
        <div class="card mb-4">
            <div class="card-body py-3" style="max-height: 70px;">
                <form method="GET" action="{{ route('client.bookings.index') }}" class="row g-3 align-items-center">
                    <div class="col-auto" style="min-width: 200px !important;">
                        <select name="status" id="status-filter" class="form-select form-select-sm"
                            style="min-width: 250px !important;">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý
                            </option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy
                            </option>
                        </select>
                    </div>
                    <div class="col-auto" style="margin: 0 !important; ">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Lọc
                        </button>
                    </div>
                    @if (request('status'))
                        <div class="col-auto" style="margin: 0 !important;">
                            <a href="{{ route('client.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times me-1"></i>Xóa lọc
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>


        @forelse($bookings as $b)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            @if ($b->tour)
                                @php
                                    $imgSrc = $b->tour->image_path ?: secure_asset('assets/images/destinations/dest1.jpg');
                                @endphp
                                <img src="{{ $imgSrc }}" alt="{{ $b->tour->title }}" class="img-fluid rounded"
                                    style="width:100%;height:120px;object-fit:cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                    style="height:120px;">
                                    <span class="text-muted">Không có ảnh</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <h6 class="card-title mb-1">
                                    @if ($b->tour)
                                        <a href="{{ route('client.tours.show', ['id' => $b->tour->tourID]) }}"
                                            class="text-decoration-none">{{ $b->tour->title }}</a>
                                    @else
                                        <span class="text-muted">(Tour không còn)</span>
                                    @endif
                                </h6>
                                <small class="text-muted">Mã đặt: #{{ $b->booking_id }}</small>
                            </div>
                            <div class="row text-sm">
                                <div class="col-6">
                                    <small><i class="far fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($b->departure_date)->format('d/m/Y') }}</small>
                                </div>
                                <div class="col-6">
                                    <small><i class="far fa-users me-1"></i> {{ $b->num_adults }} người lớn,
                                        {{ $b->num_children }} trẻ em</small>
                                </div>
                                <div class="col-6">
                                    <small><i class="fas fa-map-marker-alt me-1"></i> {{ $b->pickup_point }}</small>
                                </div>
                                <div class="col-6">
                                    <small><i class="fas fa-phone me-1"></i> {{ $b->phone_number }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <div class="mb-2">
                                <strong
                                    class="text-primary">{{ number_format((float) $b->total_price, 0, ',', '.') }}đ</strong>
                            </div>
                            <div>
                                @php
                                    $statusClass = match ($b->status) {
                                        'confirmed' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                    $statusText = match ($b->status) {
                                        'confirmed' => 'Đã xác nhận',
                                        'pending' => 'Chờ xử lý',
                                        'cancelled' => 'Đã hủy',
                                        default => $b->status,
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                            @if ($b->status === 'pending')
                                <div class="mt-2">
                                    <form action="{{ route('client.bookings.cancel', $b->booking_id) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt tour này không?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-times me-1"></i>Hủy đặt
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="far fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Bạn chưa có tour nào được đặt</h5>
                <p class="text-muted">Hãy khám phá các tour du lịch tuyệt vời của chúng tôi!</p>
                <a href="{{ route('client.tours.index') }}" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Tìm tour
                </a>
            </div>
        @endforelse

        @if ($bookings->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection
