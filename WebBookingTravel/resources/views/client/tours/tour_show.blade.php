@extends('client.layouts.app')
@section('title', $tour->title ?? 'Chi tiết tour')
@section('content')
    @php
        // Build gallery
        $gallery = [];
        $raw = $tour->images ?? null;
        if (is_string($raw)) {
            $dec = json_decode($raw, true);
            if (is_array($dec)) {
                $raw = $dec;
            } else {
                $raw = null;
            }
        }
        if (is_array($raw)) {
            $candidates = $raw;
            foreach (['images', 'photos', 'gallery', 'media', 'files'] as $k) {
                if (isset($raw[$k]) && is_array($raw[$k])) {
                    $candidates = $raw[$k];
                    break;
                }
            }
            foreach ($candidates as $img) {
                $u = null;
                if (is_string($img)) {
                    $u = $img;
                } elseif (is_array($img)) {
                    $u =
                        $img['url'] ??
                        ($img['src'] ??
                            ($img['path'] ?? ($img['image'] ?? ($img['imageUrl'] ?? ($img['image_url'] ?? null)))));
                }
                if ($u) {
                    // Normalize the URL for comparison
                    $normalizedU = $u;
                    if (strpos($u, 'http://127.0.0.1:8000/') === 0) {
                        $normalizedU = str_replace('http://127.0.0.1:8000/', '', $u);
                    } elseif (strpos($u, asset('')) === 0) {
                        $normalizedU = str_replace(asset(''), '', $u);
                    }
                    $normalizedU = ltrim($normalizedU, '/');

                    // Check if this normalized path already exists
                    $isDuplicate = false;
                    foreach ($gallery as $existing) {
                        $normalizedExisting = $existing;
                        if (strpos($existing, 'http://127.0.0.1:8000/') === 0) {
                            $normalizedExisting = str_replace('http://127.0.0.1:8000/', '', $existing);
                        } elseif (strpos($existing, asset('')) === 0) {
                            $normalizedExisting = str_replace(asset(''), '', $existing);
                        }
                        $normalizedExisting = ltrim($normalizedExisting, '/');

                        if ($normalizedU === $normalizedExisting) {
                            $isDuplicate = true;
                            break;
                        }
                    }

                    if (!$isDuplicate) {
                        $gallery[] = $u;
                    }
                }
            }
        }

        $primary = $tour->image_path ?: asset('assets/images/destinations/dest1.jpg');
        if (!$gallery) {
            $gallery[] = $primary;
        } else {
            // Check if primary image already exists in gallery
            $primaryNormalized = $primary;
            if (strpos($primary, 'http://127.0.0.1:8000/') === 0) {
                $primaryNormalized = str_replace('http://127.0.0.1:8000/', '', $primary);
            } elseif (strpos($primary, asset('')) === 0) {
                $primaryNormalized = str_replace(asset(''), '', $primary);
            }
            $primaryNormalized = ltrim($primaryNormalized, '/');

            $primaryExists = false;
            foreach ($gallery as $existing) {
                $normalizedExisting = $existing;
                if (strpos($existing, 'http://127.0.0.1:8000/') === 0) {
                    $normalizedExisting = str_replace('http://127.0.0.1:8000/', '', $existing);
                } elseif (strpos($existing, asset('')) === 0) {
                    $normalizedExisting = str_replace(asset(''), '', $existing);
                }
                $normalizedExisting = ltrim($normalizedExisting, '/');

                if ($primaryNormalized === $normalizedExisting) {
                    $primaryExists = true;
                    break;
                }
            }

            if (!$primaryExists) {
                array_unshift($gallery, $primary);
            }
        }

        // Remove any empty or invalid entries
        $gallery = array_values(
            array_filter($gallery, function ($img) {
                return !empty($img) && is_string($img);
            }),
        );

        $typeVal = $tour->type ?? ($tour->tourType ?? null);
        $typeLabel = $typeVal
            ? (strtolower(trim($typeVal)) === 'international'
                ? 'Nước ngoài'
                : (strtolower(trim($typeVal)) === 'domestic'
                    ? 'Trong nước'
                    : $typeVal))
            : null;
        $adult = $tour->priceAdult;
        $child = $tour->priceChild;

        $pricesJson = $tour->prices;
        if (is_string($pricesJson)) {
            $tmp = json_decode($pricesJson, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $pricesJson = $tmp;
            }
        }
        $itineraryJson = $tour->itinerary;
        if (is_string($itineraryJson)) {
            $tmp = json_decode($itineraryJson, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $itineraryJson = $tmp;
            }
        }
        $startDate = $tour->startDate ?? null;
        $endDate = $tour->endDate ?? null;
    @endphp

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
            --radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .tour-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0.6rem 0.3rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.15rem;
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.875rem;
            margin-bottom: 0.45rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .tour-header {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.6rem;
            margin-bottom: 0.9rem;
        }

        @media (min-width: 768px) {
            .tour-header {
                grid-template-columns: 1.5fr 1fr;
            }
        }

        .hero-slideshow {
            position: relative;
            border-radius: var(--radius);
            overflow: hidden;
            background: var(--gray-100);
            aspect-ratio: 16/10;
        }

        .hero-slide {
            display: none;
            width: 100%;
            height: 100%;
        }

        .hero-slide.is-active {
            display: block;
        }

        .hero-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slide-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 10;
        }

        .slide-nav:hover {
            background: white;
            box-shadow: var(--shadow-lg);
        }

        .slide-nav.prev {
            left: 0.3rem;
        }

        .slide-nav.next {
            right: 0.3rem;
        }

        .slide-dots {
            position: absolute;
            bottom: 0.3rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.15rem;
            z-index: 10;
        }

        .slide-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            border: none;
            padding: 0;
            cursor: pointer;
            transition: all 0.2s;
        }

        .slide-dot.is-active {
            background: white;
            width: 24px;
            border-radius: 4px;
        }

        .slide-category {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            z-index: 15;

        }

        .tour-info-card {
            background: white;
            border-radius: var(--radius);
            padding: 0.45rem;
            box-shadow: var(--shadow);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .tour-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0 0 1rem 0;
            line-height: 1.3;
        }

        .tour-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.15rem;
            margin-bottom: 1rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.075rem;
            padding: 0.15rem 0.25rem;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;

        }

        .badge-primary {
            background: #dbeafe;
            color: var(--primary);
        }

        .badge-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .price-section {
            padding: 0.45rem;
            background: var(--gray-50);
            border-radius: var(--radius);
            margin-bottom: 1rem;
            margin-top: 20px;
        }

        .price-main {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            display: inline;
        }

        .price-label {
            color: var(--gray-600);
            font-size: 1rem;
            display: inline;
            margin-left: 0.5rem;
        }

        .price-child {
            margin-top: 0.3rem;
            color: #F7921E;
            font-size: 1rem;
            font-weight: 600;
        }

        .info-grid {
            display: grid;
            gap: 0.3rem;
            margin-bottom: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .info-icon {
            color: var(--primary);
            flex-shrink: 0;
        }

        .info-content {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .info-label {
            font-size: 0.9rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .info-value {
            color: var(--gray-900);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-book {
            width: 40%;
            padding: 0.3rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .btn-book:hover {
            background: var(--primary-dark);
            color: white;
        }

        .section {
            background: white;
            border-radius: var(--radius);
            padding: 0.6rem;
            margin-bottom: 0.6rem;
            box-shadow: var(--shadow-sm);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0 0 0.45rem 0;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.45rem;
        }

        .detail-item {
            padding: 0.3rem;
            background: var(--gray-50);
            border-radius: 8px;
        }

        .detail-label {
            font-size: 0.75rem;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.15rem;
        }

        .detail-value {
            color: var(--gray-900);
            
        }

        .itinerary-day {
            padding: 0.45rem;
            background: var(--gray-50);
            border-radius: var(--radius);
            margin-bottom: 0.3rem;
        }

        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.3rem;
        }

        .day-title {
            font-size: 1.125rem;
    
            color: var(--gray-900);
            margin-bottom: 0.15rem;
        }

        .day-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .day-meta-item {
            display: flex;
            align-items: center;
            gap: 0.1125rem;
        }

        .meal-tags {
            display: flex;
            gap: 0.15rem;
            flex-wrap: wrap;
        }

        .meal-tag {
            padding: 0.075rem 0.225rem;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gray-700);
        }

        .day-description {
            color: var(--gray-700);
            line-height: 1.6;
            margin-top: 0.3rem;
        }

        .day-list {
            margin: 0.3rem 0 0 0;
            padding-left: 0.375rem;
        }

        .day-list li {
            margin-bottom: 0.15rem;
            color: var(--gray-700);
        }

        .day-images {
            display: flex;
            gap: 0.225rem;
            margin-top: 0.3rem;
            flex-wrap: wrap;
        }

        .day-image {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--gray-200);
        }

        @media (max-width: 767px) {
            .tour-detail-container {
                padding: 0.3rem;
            }

            .tour-title {
                font-size: 1.5rem;
            }

            .section {
                padding: 0.45rem;
            }

            .tour-info-card {
                position: static;
            }
        }
    </style>

    <div class="tour-detail-container">
        <a href="{{ url()->previous() ?: route('client.tours.index') }}" class="back-link">
            <i class="far fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>

        <div class="tour-header">
            <div class="hero-slideshow" data-autoplay="5000">
                @foreach ($gallery as $i => $u)
                    @php
                        $isAbsolute =
                            is_string($u) &&
                            (strpos($u, 'http://') === 0 ||
                                strpos($u, 'https://') === 0 ||
                                strpos($u, '//') === 0 ||
                                strpos($u, 'data:') === 0 ||
                                strpos($u, 'blob:') === 0);
                        $src = $isAbsolute ? $u : asset(ltrim(str_replace('\\', '/', (string) $u), '/'));
                    @endphp
                    <div class="hero-slide {{ $i === 0 ? 'is-active' : '' }}">
                        <img src="{{ $src }}" alt="{{ $tour->title }}">
                    </div>
                @endforeach

                {{-- Category badge in top-left corner --}}
                @if ($tour->category && $tour->category->categoryName)
                    <div class="slide-category">
                        <span class="badge badge-primary">{{ $tour->category->categoryName }}</span>
                    </div>
                @endif

                @if (count($gallery) > 1)
                    <button type="button" class="slide-nav prev" aria-label="Previous">
                        <i class="far fa-chevron-left"></i>
                    </button>
                    <button type="button" class="slide-nav next" aria-label="Next">
                        <i class="far fa-chevron-right"></i>
                    </button>
                    <div class="slide-dots">
                        @foreach ($gallery as $i => $u)
                            <button type="button" class="slide-dot indicator-item {{ $i === 0 ? 'is-active' : '' }}"
                                data-index="{{ $i }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="tour-info-card">
                <h1 class="tour-title">{{ $tour->title }}</h1>

                @if ($typeLabel)
                    <div class="tour-badges">
                        <span class="badge badge-secondary">{{ $typeLabel }}</span>
                    </div>
                @endif

                <div class="price-section">
                    @if (!is_null($adult))
                        <div>
                            <span class="price-main">{{ number_format($adult, 0, ',', '.') }}đ</span>
                            <span class="price-label">/ Người lớn</span>
                        </div>
                        @if (!is_null($child))
                            <div class="price-child">Trẻ em: {{ number_format($child, 0, ',', '.') }}đ</div>
                        @endif
                    @elseif(!is_null($child))
                        <div>
                            <span class="price-main">{{ number_format($child, 0, ',', '.') }}đ</span>
                            <span class="price-label">/ trẻ em</span>
                        </div>
                    @else
                        <div class="price-label">Liên hệ để biết giá</div>
                    @endif
                </div>

                <div class="info-grid">
                    @if (!empty($tour->departurePoint))
                        <div class="info-item">
                            <i class="far fa-bus info-icon"></i>
                            <div class="info-content">
                                <span class="info-label">Điểm đến:</span>
                                <span class="info-value">{{ $tour->departurePoint }}</span>
                            </div>
                        </div>
                    @endif

                </div>

                <a class="btn-book" href="{{ route('client.booking', ['tour' => $tour->tourID]) }}">
                    Đặt tour ngay
                </a>
            </div>
        </div>

        @if (!empty($tour->description))
            <div class="section">
                <h2 class="section-title">Mô tả chi tiết</h2>
                <div class="day-description">{!! $tour->description !!}</div>
            </div>
        @endif

        <div class="section">
            <div class="detail-grid">
                @if (!empty($tour->departurePoint))
                    <div class="detail-item">
                        <div class="detail-label">Khởi hành</div>
                        <div class="detail-value">{{ $tour->departurePoint }}</div>
                    </div>
                @endif
                @if (!empty($tour->pickupPoint))
                    <div class="detail-item">
                        <div class="detail-label">Điểm đón</div>
                        <div class="detail-value">{{ $tour->pickupPoint }}</div>
                    </div>
                @endif
                @if ($tour->category && $tour->category->categoryName)
                    <div class="detail-item">
                        <div class="detail-label">Danh mục</div>
                        <div class="detail-value">{{ $tour->category->categoryName }}</div>
                    </div>
                @endif
            </div>
        </div>


        <div class="section">
            <h2 class="section-title">Lịch trình</h2>
            @php
                $days = [];
                if (is_array($itineraryJson)) {
                    // Check if it's direct array of days
    if (isset($itineraryJson[0]['day']) || isset($itineraryJson[0]['activity'])) {
        $days = $itineraryJson;
    } elseif (isset($itineraryJson['days']) && is_array($itineraryJson['days'])) {
        $days = $itineraryJson['days'];
                    }
                }
            @endphp

            @if (!empty($days))
                @foreach ($days as $idx => $day)
                    @php
                        $dayNumber = $day['day'] ?? $idx + 1;
                        $activity = $day['activity'] ?? ($day['title'] ?? ($day['description'] ?? ''));
                        $details = $day['details'] ?? ($day['content'] ?? '');
                    @endphp
                    <div class="itinerary-day">
                        <div class="day-header">
                            <div>
                                <div class="day-title">Ngày {{ $dayNumber }}</div>
                            </div>
                        </div>

                        @if ($activity)
                            <div class="day-description">
                                <strong>{{ $activity }}</strong>
                                @if ($details && $details !== $activity)
                                    <br><br>{{ $details }}
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            @elseif(is_string($tour->itinerary) && trim($tour->itinerary) !== '')
                <div class="day-description">{!! nl2br(e($tour->itinerary)) !!}</div>
            @else
                <div class="day-description">Chưa có lịch trình chi tiết.</div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slideshow = document.querySelector('.hero-slideshow');
            if (!slideshow) return;

            const slides = slideshow.querySelectorAll('.hero-slide');
            const dots = slideshow.querySelectorAll('.slide-dot');
            const prevBtn = slideshow.querySelector('.slide-nav.prev');
            const nextBtn = slideshow.querySelector('.slide-nav.next');

            if (slides.length !== dots.length || slides.length === 0) return;

            let currentIndex = 0;
            let autoplayTimer = null;

            function updateSlideshow(index) {
                if (index < 0 || index >= slides.length) return;

                slides.forEach(slide => slide.classList.remove('is-active'));
                dots.forEach(dot => dot.classList.remove('is-active'));

                slides[index].classList.add('is-active');
                dots[index].classList.add('is-active');

                currentIndex = index;
            }

            // Event handlers
            dots.forEach((dot, index) => {
                dot.addEventListener('click', (e) => {
                    e.preventDefault();
                    updateSlideshow(index);
                });
            });

            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    updateSlideshow(currentIndex > 0 ? currentIndex - 1 : slides.length - 1);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    updateSlideshow(currentIndex < slides.length - 1 ? currentIndex + 1 : 0);
                });
            }

            // Auto-play
            const autoplayDelay = slideshow.getAttribute('data-autoplay');
            if (autoplayDelay && slides.length > 1) {
                const startAutoplay = () => {
                    autoplayTimer = setInterval(() => {
                        updateSlideshow(currentIndex < slides.length - 1 ? currentIndex + 1 : 0);
                    }, parseInt(autoplayDelay));
                };

                const stopAutoplay = () => {
                    if (autoplayTimer) {
                        clearInterval(autoplayTimer);
                        autoplayTimer = null;
                    }
                };

                startAutoplay();
                slideshow.addEventListener('mouseenter', stopAutoplay);
                slideshow.addEventListener('mouseleave', startAutoplay);
            }
        });
    </script>
@endsection
