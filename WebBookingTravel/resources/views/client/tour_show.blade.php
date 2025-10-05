@extends('client.layouts.app')
@section('title', $tour->title ?? 'Chi tiết tour')
@section('content')
    @php
        // Build gallery: prefer all images if available, falling back to image_path like list
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
            // unwrap common wra
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
                    $gallery[] = $u;
                }
            }
        }
        // Always ensure primary image like list
        $primary = $tour->image_path ?: asset('assets/images/destinations/dest1.jpg');
        if (!$gallery) {
            $gallery[] = $primary;
        } elseif (!in_array($primary, $gallery, true)) {
            array_unshift($gallery, $primary);
        }

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
    @endphp

    <div class="container py-4">
        <style>
            .thumb.active {
                outline: 2px solid var(--bs-primary);
            }

            .hero-slideshow {
                position: relative;
                overflow: hidden;
            }

            .hero-slide {
                display: none;
            }

            .hero-slide.is-active,
            .hero-slide.active {
                display: block;
            }

            .slide-nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                z-index: 2;
            }

            .slide-nav.prev {
                left: 8px;
            }

            .slide-nav.next {
                right: 8px;
            }

            /* Friendly UI helpers */
            .section-card {
                background-color: #fff;
                border: 1px solid rgba(0, 0, 0, .08);
                border-radius: .75rem;
            }

            .section-card+.section-card {
                margin-top: 1rem;
            }

            .icon-text {
                display: inline-flex;
                align-items: center;
                gap: .5rem;
            }

            .icon-text i {
                color: var(--bs-primary);
            }

            .muted {
                color: #6c757d;
            }

            .itinerary-day {
                padding: .75rem 1rem;
                border: 1px solid rgba(0, 0, 0, .06);
                border-radius: .75rem;
            }

            .itinerary-day+.itinerary-day {
                margin-top: .75rem;
            }

            .meal-badge {
                background: #f8f9fa;
                border: 1px solid #e9ecef;
                color: #495057;
                padding: .15rem .5rem;
                border-radius: .5rem;
                font-size: .85rem;
            }

            .amenity {
                background: #f1f3f5;
                color: #495057;
                border-radius: 999px;
                padding: .15rem .5rem;
                font-size: .8rem;
            }

            .price-chip {
                background: #fff3cd;
                color: #8a6d3b;
                border: 1px solid #ffe69c;
                padding: .15rem .5rem;
                border-radius: .5rem;
                font-weight: 600;
            }

            .list-tight {
                margin: 0;
                padding-left: 1.25rem;
            }

            .list-tight li {
                margin-bottom: .25rem;
            }
        </style>
        <div class="mb-3">
            <a href="{{ url()->previous() ?: route('client.tours.index') }}" class="text-decoration-none small"><i
                    class="far fa-arrow-left me-1"></i> Quay lại</a>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="border rounded-3 overflow-hidden bg-light hero-slideshow" data-autoplay="5000">
                    @foreach ($gallery as $i => $u)
                        @php
                            $isAbsolute =
                                is_string($u) &&
                                (strpos($u, 'http://') === 0 ||
                                    strpos($u, 'https://') === 0 ||
                                    strpos($u, '//') === 0 ||
                                    strpos($u, 'data:') === 0 ||
                                    strpos($u, 'blob:') === 0);
                            $normalized = str_replace('\\', '/', (string) $u);
                            $src = $isAbsolute ? $u : asset(ltrim($normalized, '/'));
                        @endphp
                        <div class="hero-slide {{ $i === 0 ? 'is-active' : '' }}"
                            aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
                            <img src="{{ $src }}" alt="{{ $tour->title }}" class="w-100"
                                style="aspect-ratio: 16/10; object-fit: cover;">
                        </div>
                    @endforeach
                    @if (count($gallery) > 1)
                        <button type="button" class="slide-nav prev btn btn-light btn-sm" aria-label="Prev"><i
                                class="far fa-chevron-left"></i></button>
                        <button type="button" class="slide-nav next btn btn-light btn-sm" aria-label="Next"><i
                                class="far fa-chevron-right"></i></button>
                        <div class="d-flex justify-content-center gap-2 py-2">
                            @foreach ($gallery as $i => $u)
                                <button type="button"
                                    class="slide-dot {{ $i === 0 ? 'active' : '' }} btn btn-xs btn-secondary"
                                    style="width:8px;height:8px;border-radius:50%;padding:0;border:none;"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <h1 class="h3 mb-2">{{ $tour->title }}</h1>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if ($tour->category && $tour->category->categoryName)
                        <span class="badge bg-secondary">{{ $tour->category->categoryName }}</span>
                    @endif
                    @if ($typeLabel)
                        <span class="badge bg-info text-dark">{{ $typeLabel }}</span>
                    @endif
                    @if (!empty($tour->days))
                        <span class="badge bg-light text-dark"><i class="far fa-clock me-1"></i>{{ $tour->days }}
                            ngày</span>
                    @endif
                </div>

                <div class="border rounded-3 p-3 mb-3">
                    @if (!is_null($adult))
                        <div class="fs-5 mb-1">Từ <strong>{{ number_format($adult, 0, ',', '.') }}đ</strong> <span
                                class="text-muted">/ người lớn</span></div>
                    @elseif(!is_null($child))
                        <div class="fs-5 mb-1">Từ <strong>{{ number_format($child, 0, ',', '.') }}đ</strong> <span
                                class="text-muted">/ trẻ em</span></div>
                    @else
                        <div class="text-muted">Liên hệ để biết giá</div>
                    @endif
                    @if (!is_null($child) && !is_null($adult))
                        <div class="text-muted">Trẻ em: {{ number_format($child, 0, ',', '.') }}đ</div>
                    @endif
                </div>

                <div class="row g-2 mb-3">
                    @if (!empty($tour->departurePoint))
                        <div class="col-sm-6"><i class="far fa-map-marker-alt me-1"></i><strong>Khởi hành:</strong>
                            {{ $tour->departurePoint }}</div>
                    @endif
                    @if (!empty($tour->pickupPoint))
                        <div class="col-sm-6"><i class="far fa-bus me-1"></i><strong>Điểm đón:</strong>
                            {{ $tour->pickupPoint }}</div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <a class="btn btn-primary" href="{{ route('client.booking', ['tour' => $tour->tourID]) }}">Đặt tour</a>
                </div>
            </div>
        </div>

        @if (!empty($tour->description))
            <div class="mt-4">
                <h2 class="h5 mb-2">Mô tả chi tiết</h2>
                <div class="bg-white border rounded-3 p-3">
                    {!! $tour->description !!}
                </div>
            </div>
        @endif

        @php
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
            $hotelsJson = $tour->hotels;
            if (is_string($hotelsJson)) {
                $tmp = json_decode($hotelsJson, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $hotelsJson = $tmp;
                }
            }
            $startDate = $tour->startDate ?? null;
            $endDate = $tour->endDate ?? null;
        @endphp

        <div class="mt-4">
            <h2 class="h5 mb-2">Thông tin tour</h2>
            <div class="section-card p-3">
                <div class="row g-3">
        
                    @if (!empty($tour->departurePoint))
                        <div class="col-md-4">
                            <div class="text-muted">Khởi hành</div>
                            <div class="fw-semibold">{{ $tour->departurePoint }}</div>
                        </div>
                    @endif
                    @if (!empty($tour->pickupPoint))
                        <div class="col-md-4">
                            <div class="text-muted">Điểm đón</div>
                            <div class="fw-semibold">{{ $tour->pickupPoint }}</div>
                        </div>
                    @endif
                    @if (!empty($tour->days))
                        <div class="col-md-4">
                            <div class="text-muted">Số ngày</div>
                            <div class="fw-semibold">{{ $tour->days }}</div>
                        </div>
                    @endif
                    @if (!empty($startDate))
                        <div class="col-md-4">
                            <div class="text-muted">Ngày bắt đầu</div>
                            <div class="fw-semibold">{{ $startDate }}</div>
                        </div>
                    @endif
                    @if (!empty($endDate))
                        <div class="col-md-4">
                            <div class="text-muted">Ngày kết thúc</div>
                            <div class="fw-semibold">{{ $endDate }}</div>
                        </div>
                    @endif
                    @if ($tour->category && $tour->category->categoryName)
                        <div class="col-md-4">
                            <div class="text-muted">Danh mục</div>
                            <div class="fw-semibold">{{ $tour->category->categoryName }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h2 class="h5 mb-2">Giá chi tiết</h2>
            <div class="section-card p-3">
                @if (is_array($pricesJson))
                    <div class="row g-2">
                        @foreach ($pricesJson as $k => $v)
                            <div class="col-sm-6 col-md-4">
                                <div class="text-muted">{{ ucfirst($k) }}</div>
                                <div class="fw-semibold">
                                    @if (is_numeric($v))
                                        {{ number_format((float) $v, 0, ',', '.') }}đ
                                    @else
                                        {{ is_string($v) ? $v : json_encode($v, JSON_UNESCAPED_UNICODE) }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif(!empty($tour->priceAdult) || !empty($tour->priceChild))
                    <div>Người lớn: <strong>{{ number_format((float) ($tour->priceAdult ?? 0), 0, ',', '.') }}đ</strong>
                    </div>
                    @if (!is_null($tour->priceChild))
                        <div>Trẻ em: <strong>{{ number_format((float) $tour->priceChild, 0, ',', '.') }}đ</strong></div>
                    @endif
                @else
                    <div class="text-muted">Không có dữ liệu giá.</div>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <h2 class="h5 mb-2">Lịch trình</h2>
            <div class="section-card p-3">
                @php
                    $days = [];
                    if (is_array($itineraryJson)) {
                        if (isset($itineraryJson['days']) && is_array($itineraryJson['days'])) {
                            $days = $itineraryJson['days'];
                        } elseif (array_values($itineraryJson) === $itineraryJson) {
                            $days = $itineraryJson; // already a list
                        }
                    }
                @endphp
                @if (!empty($days))
                    <div class="vstack">
                        @foreach ($days as $idx => $day)
                            @php
                                $dTitle = is_array($day)
                                    ? $day['title'] ?? ($day['name'] ?? 'Ngày ' . ($day['day'] ?? $idx + 1))
                                    : 'Ngày ' . ($idx + 1);
                                $dDesc = is_array($day)
                                    ? $day['content'] ??
                                        ($day['description'] ??
                                            (isset($day['details'])
                                                ? (is_string($day['details'])
                                                    ? $day['details']
                                                    : json_encode($day['details'], JSON_UNESCAPED_UNICODE))
                                                : ''))
                                    : (string) $day;
                                $dTime = is_array($day)
                                    ? $day['time'] ??
                                        (isset($day['startTime'], $day['endTime'])
                                            ? $day['startTime'] . ' - ' . $day['endTime']
                                            : null)
                                    : null;
                                $dTransport = is_array($day)
                                    ? $day['transport'] ?? ($day['vehicle'] ?? ($day['bus'] ?? ($day['train'] ?? null)))
                                    : null;
                                $dDistance = is_array($day) ? $day['distance'] ?? null : null;
                                $dPlaces = is_array($day)
                                    ? $day['places'] ?? ($day['locations'] ?? ($day['stops'] ?? null))
                                    : null;
                                $dItems = is_array($day)
                                    ? $day['items'] ?? ($day['activities'] ?? ($day['activity'] ?? null))
                                    : null;
                                $dMealsRaw = is_array($day) ? $day['meals'] ?? ($day['meal'] ?? null) : null;
                                $dImgs = is_array($day) ? $day['images'] ?? ($day['photos'] ?? null) : null;
                                $meals = [];
                                if (is_array($dMealsRaw)) {
                                    $meals = $dMealsRaw;
                                } elseif (is_string($dMealsRaw)) {
                                    $meals = array_filter(array_map('trim', preg_split('/[,;\/]+/u', $dMealsRaw)));
                                } else {
                                    if (is_array($day)) {
                                        foreach (
                                            ['breakfast' => 'Sáng', 'lunch' => 'Trưa', 'dinner' => 'Tối']
                                            as $mk => $ml
                                        ) {
                                            if (!empty($day[$mk])) {
                                                $meals[] = $ml;
                                            }
                                        }
                                    }
                                }
                                $dGuide = is_array($day) ? $day['guide'] ?? ($day['tourGuide'] ?? null) : null;
                                $dNotes = is_array($day) ? $day['note'] ?? ($day['notes'] ?? null) : null;
                                if (!empty($dPlaces) && is_string($dPlaces)) {
                                    $dPlaces = array_filter(array_map('trim', preg_split('/[,;]+/u', $dPlaces)));
                                }
                                if (!empty($dItems) && is_string($dItems)) {
                                    $dItems = array_filter(array_map('trim', preg_split('/\n|[,;]+/u', $dItems)));
                                }
                                $thumbs = [];
                                if (is_array($dImgs)) {
                                    foreach ($dImgs as $im) {
                                        $url = is_string($im)
                                            ? $im
                                            : (is_array($im)
                                                ? $im['url'] ?? ($im['src'] ?? ($im['path'] ?? null))
                                                : null);
                                        if ($url) {
                                            $thumbs[] =
                                                str_starts_with($url, 'http') || str_starts_with($url, '//')
                                                    ? $url
                                                    : asset(ltrim(str_replace('\\', '/', $url), '/'));
                                        }
                                    }
                                }
                            @endphp
                            <div class="itinerary-day">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fw-semibold mb-1">{{ $dTitle }}</div>
                                        @if ($dTime)
                                            <div class="icon-text muted small"><i
                                                    class="far fa-clock"></i><span>{{ $dTime }}</span></div>
                                        @endif
                                        @if ($dTransport)
                                            <div class="icon-text muted small"><i
                                                    class="far fa-bus"></i><span>{{ $dTransport }}</span></div>
                                        @endif
                                        @if ($dDistance)
                                            <div class="icon-text muted small"><i
                                                    class="far fa-road"></i><span>{{ $dDistance }}</span></div>
                                        @endif
                                        @if (!empty($dPlaces) && is_array($dPlaces))
                                            <div class="muted small mt-1"><i
                                                    class="far fa-map-marker-alt me-1"></i>{{ implode(' • ', $dPlaces) }}
                                            </div>
                                        @endif
                                    </div>
                                    @if (!empty($meals))
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($meals as $m)
                                                <span class="meal-badge">{{ $m }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @if (!empty($dDesc))
                                    <div class="mt-2">{!! is_string($dDesc) ? nl2br(e($dDesc)) : '' !!}</div>
                                @endif
                                @if (!empty($dGuide))
                                    <div class="muted small mt-1"><i class="far fa-user me-1"></i>Hướng dẫn viên:
                                        {{ is_string($dGuide) ? $dGuide : json_encode($dGuide, JSON_UNESCAPED_UNICODE) }}
                                    </div>
                                @endif
                                @if (!empty($dNotes))
                                    <div class="muted small mt-1"><i
                                            class="far fa-info-circle me-1"></i>{{ is_string($dNotes) ? $dNotes : json_encode($dNotes, JSON_UNESCAPED_UNICODE) }}
                                    </div>
                                @endif
                                @if (!empty($dItems) && is_array($dItems))
                                    <ul class="list-tight mt-2">
                                        @foreach ($dItems as $it)
                                            <li>{{ is_string($it) ? $it : json_encode($it, JSON_UNESCAPED_UNICODE) }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if (!empty($thumbs))
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach ($thumbs as $t)
                                            <img src="{{ $t }}" alt="{{ $dTitle }}"
                                                style="width:88px;height:66px;object-fit:cover;border-radius:.5rem;border:1px solid rgba(0,0,0,.08);" />
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif(is_string($tour->itinerary) && trim($tour->itinerary) !== '')
                    <div>{!! $tour->itinerary !!}</div>
                @elseif(is_array($itineraryJson))
                    <pre class="mb-0 small">{{ json_encode($itineraryJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                @else
                    <div class="text-muted">Chưa có lịch trình.</div>
                @endif
            </div>
        </div>
        <div class="mt-4">
            <h2 class="h5 mb-2">Khách sạn</h2>
            <div class="section-card p-3">
                @php
                    $hotelItems = [];
                    if (is_array($hotelsJson)) {
                        if (isset($hotelsJson['items']) && is_array($hotelsJson['items'])) {
                            $hotelItems = $hotelsJson['items'];
                        } elseif (array_values($hotelsJson) === $hotelsJson) {
                            $hotelItems = $hotelsJson;
                        }
                    }
                @endphp
                @if (!empty($hotelItems))
                    <div class="row g-3">
                        @foreach ($hotelItems as $h)
                            @php
                                $hName = is_array($h) ? $h['name'] ?? ($h['title'] ?? 'Khách sạn') : (string) $h;
                                $hStars = is_array($h) ? $h['stars'] ?? ($h['rating'] ?? null) : null;
                                $hAddr = is_array($h) ? $h['address'] ?? null : null;
                                $hRooms =
                                    is_array($h) && isset($h['rooms']) && is_array($h['rooms']) ? $h['rooms'] : [];
                                $hImg = null;
                                if (is_array($h)) {
                                    $hImg =
                                        $h['image'] ?? ($h['imageUrl'] ?? ($h['image_url'] ?? ($h['photo'] ?? null)));
                                    if (!$hImg && !empty($h['images']) && is_array($h['images'])) {
                                        $hImg = is_string($h['images'][0] ?? null)
                                            ? $h['images'][0]
                                            : (is_array($h['images'][0] ?? null)
                                                ? $h['images'][0]['url'] ?? null
                                                : null);
                                    }
                                }
                                $imgSrc = null;
                                if ($hImg) {
                                    $abs =
                                        is_string($hImg) &&
                                        (str_starts_with($hImg, 'http://') ||
                                            str_starts_with($hImg, 'https://') ||
                                            str_starts_with($hImg, '//'));
                                    $imgSrc = $abs ? $hImg : asset(ltrim(str_replace('\\', '/', $hImg), '/'));
                                }
                                // Price extraction
                                $hPrice = null;
                                $hPriceLabel = 'đ';
                                $nightly = false;
                                if (is_array($h)) {
                                    foreach (
                                        [
                                            'price',
                                            'pricePerNight',
                                            'per_night',
                                            'nightly',
                                            'roomPrice',
                                            'minPrice',
                                            'cost',
                                            'rate',
                                        ]
                                        as $pk
                                    ) {
                                        if (isset($h[$pk]) && is_numeric($h[$pk])) {
                                            $hPrice = (float) $h[$pk];
                                            break;
                                        }
                                        if (
                                            isset($h[$pk]) &&
                                            is_string($h[$pk]) &&
                                            is_numeric(preg_replace('/[^0-9.]/', '', $h[$pk]))
                                        ) {
                                            $hPrice = (float) preg_replace('/[^0-9.]/', '', $h[$pk]);
                                            break;
                                        }
                                    }
                                    if (!$hPrice && !empty($h['rates']) && is_array($h['rates'])) {
                                        $min = null;
                                        foreach ($h['rates'] as $r) {
                                            $pv = is_array($r)
                                                ? $r['price'] ?? ($r['amount'] ?? null)
                                                : (is_numeric($r)
                                                    ? $r
                                                    : null);
                                            if (is_string($pv)) {
                                                $pv = (float) preg_replace('/[^0-9.]/', '', $pv);
                                            }
                                            if (is_numeric($pv)) {
                                                $min = is_null($min) ? (float) $pv : min($min, (float) $pv);
                                            }
                                        }
                                        if (!is_null($min)) {
                                            $hPrice = $min;
                                        }
                                    }
                                    foreach (['pricePerNight', 'per_night', 'nightly'] as $nk) {
                                        if (isset($h[$nk])) {
                                            $nightly = true;
                                            break;
                                        }
                                    }
                                    if (
                                        !empty($h['currency']) &&
                                        is_string($h['currency']) &&
                                        strtoupper($h['currency']) !== 'VND'
                                    ) {
                                        $hPriceLabel = $h['currency'];
                                    }
                                }
                                // Amenities
                                $amenities = [];
                                if (is_array($h)) {
                                    if (!empty($h['amenities']) && is_array($h['amenities'])) {
                                        $amenities = $h['amenities'];
                                    } elseif (!empty($h['features']) && is_array($h['features'])) {
                                        $amenities = $h['features'];
                                    }
                                }
                            @endphp
                            <div class="col-md-6">
                                <div class="border rounded-3 p-2 h-100 d-flex gap-2 align-items-start">
                                    @if ($imgSrc)
                                        <img src="{{ $imgSrc }}" alt="{{ $hName }}"
                                            style="width:84px;height:84px;object-fit:cover;border-radius:.5rem;" />
                                    @endif
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="fw-semibold">{{ $hName }}</div>
                                            @if ($hStars)
                                                <div class="text-warning small">({{ $hStars }}★)</div>
                                            @endif
                                            @if (!is_null($hPrice))
                                                <span
                                                    class="price-chip">{{ number_format($hPrice, 0, ',', '.') }}{{ $hPriceLabel }}
                                                    @if ($nightly)
                                                        / đêm
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                        @if ($hAddr)
                                            <div class="text-muted small mt-1">{{ $hAddr }}</div>
                                        @endif
                                        @if (!empty($amenities))
                                            <div class="d-flex flex-wrap gap-1 mt-2">
                                                @foreach (array_slice($amenities, 0, 6) as $am)
                                                    <span
                                                        class="amenity">{{ is_string($am) ? $am : json_encode($am, JSON_UNESCAPED_UNICODE) }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if (!empty($hRooms))
                                            <div class="d-flex flex-wrap gap-1 mt-2">
                                                @foreach ($hRooms as $rk => $rv)
                                                    @php $roomPrice = is_numeric($rv) ? (float)$rv : (is_string($rv) ? (float)preg_replace('/[^0-9.]/','',$rv) : null); @endphp
                                                    @if (!is_null($roomPrice))
                                                        <span class="amenity">{{ ucfirst($rk) }}:
                                                            {{ number_format($roomPrice, 0, ',', '.') }}đ</span>
                                                    @else
                                                        <span class="amenity">{{ ucfirst($rk) }}:
                                                            {{ is_string($rv) ? $rv : json_encode($rv, JSON_UNESCAPED_UNICODE) }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif(is_string($tour->hotels) && trim($tour->hotels) !== '')
                    <div>{!! $tour->hotels !!}</div>
                @elseif(is_array($hotelsJson))
                    <pre class="mb-0 small">{{ json_encode($hotelsJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                @else
                    <div class="text-muted">Chưa có thông tin khách sạn.</div>
                @endif
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/slideshow.js') }}" defer></script>
@endsection
