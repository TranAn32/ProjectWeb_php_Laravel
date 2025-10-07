@extends('client.layouts.app')
@section('title', 'Tour đã đặt')
@section('content')
    <div class="container py-4">
        <h1 class="h4 mb-3">Tour đã đặt</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @forelse($bookings as $b)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            @if ($b->tour)
                                @php
                                    $imgSrc = $b->tour->image_path ?: asset('assets/images/destinations/dest1.jpg');
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
