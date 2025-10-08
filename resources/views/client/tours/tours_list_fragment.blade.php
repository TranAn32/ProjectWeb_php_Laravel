@php use Illuminate\Support\Str; @endphp
<div class="d-flex flex-wrap justify-content-between align-items-end mb-3 gap-2">
    <div>
        <h1 class="h4 mb-1">Danh sách Tour</h1>
        <div class="text-muted small">
            @if (!empty($activeType))
                @php $label = strtolower($activeType)==='international' ? 'Tour nước ngoài' : 'Tour trong nước'; @endphp
                <span class="me-2">Bộ lọc:</span>
                <span class="badge bg-secondary">{{ $label }}</span>
            @endif
            @if (!empty($activeCategory))
                <span class="badge bg-info ms-1">Danh mục: {{ $activeCategory->categoryName }}</span>
            @endif
        </div>
    </div>
    <div class="text-muted small">Tổng: <strong>{{ $tours->total() }}</strong> tour</div>
</div>

@if ($tours->isEmpty())
    <div class="alert alert-light border">Chưa có tour phù hợp.</div>
@else
    <div class="row g-3">
        @foreach ($tours as $tour)
            @php
                $imgSrc = $tour->image_path ?: asset('assets/images/destinations/dest1.jpg');
                $typeVal = $tour->type ?? ($tour->tourType ?? null);
                $typeLabel = $typeVal
                    ? (strtolower(trim($typeVal)) === 'international'
                        ? 'Nước ngoài'
                        : (strtolower(trim($typeVal)) === 'domestic'
                            ? 'Trong nước'
                            : $typeVal))
                    : null;
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="tour-card h-100">
                    <div class="tour-thumb">
                        <img src="{{ $imgSrc }}" alt="{{ $tour->title }}" loading="lazy">
                        <div class="tour-badges">
                            @if ($tour->category && $tour->category->categoryName)
                                <span class="badge-soft">{{ $tour->category->categoryName }}</span>
                            @endif
                            @if ($typeLabel)
                                <span class="badge-soft badge-type">{{ $typeLabel }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="tour-body">
                        <h3 class="tour-title">{{ $tour->title }}</h3>
                        @if (!empty($tour->description))
                            <p class="tour-desc">{{ Str::limit(strip_tags($tour->description), 120) }}</p>
                        @endif
                        <div class="tour-meta">
                            @if (!empty($tour->departurePoint))
                                <span><i class="far fa-map-marker-alt me-1"></i> {{ $tour->departurePoint }}</span>
                            @endif
                            @if (!empty($tour->days))
                                <span><i class="far fa-clock me-1"></i> {{ $tour->days }} ngày</span>
                            @endif
                        </div>
                        <div class="tour-price">
                            @php
                                $adult = $tour->priceAdult;
                                $child = $tour->priceChild;
                            @endphp
                            @if (!is_null($adult))
                                <div class="price-main">Từ <span
                                        class="amount">{{ number_format($adult, 0, ',', '.') }}đ</span> <span
                                        class="unit">/ người lớn</span></div>
                            @elseif(!is_null($child))
                                <div class="price-main">Từ <span
                                        class="amount">{{ number_format($child, 0, ',', '.') }}đ</span> <span
                                        class="unit">/ trẻ em</span></div>
                            @else
                                <div class="price-note">Liên hệ để biết giá</div>
                            @endif
                            @if (!is_null($child) && !is_null($adult))
                                <div class="price-note">Trẻ em: {{ number_format($child, 0, ',', '.') }}đ</div>
                            @endif
                        </div>
                    </div>
                    <div class="tour-actions">
                        <a class="btn btn-sm btn-outline-primary"
                            href="{{ route('client.tours.show', $tour->tourID) }}">Xem chi tiết</a>
                        <a class="btn btn-sm btn-primary" href="{{ route('client.booking', ['tour' => $tour->tourID]) }}">Đặt tour</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="d-flex justify-content-center mt-3">{{ $tours->links() }}</div>
