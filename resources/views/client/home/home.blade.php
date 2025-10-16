@extends('client.layouts.app')

@section('title', 'Trang chủ | TripGo')
@push('styles')
    @if (!empty($slides[0]['src']))
        <link rel="preload" as="image" href="{{ $slides[0]['src'] }}" imagesrcset="{{ $slides[0]['src'] }} 1600w" />
    @endif
@endpush
@section('content')
    <!-- Hero Area Start -->
    <section class="hero-area bgc-lighter rpt-60 rel z-2">
        <div class="container-fluid">
            @php
                // Fallback phòng trường hợp controller không truyền
                if (!isset($slides) || empty($slides)) {
                    $heroImage = $heroImage ?? asset('assets/images/hero/hero.jpg');
                    $slides = [$heroImage];
                }
            @endphp
            <div class="hero-slideshow position-relative overflow-hidden appears" data-autoplay="3000" data-anim="fade-scale">
                <div class="slides-wrapper" style="width:100%;height:420px;">
                    @foreach ($slides as $idx => $slide)
                        <div class="hero-slide @if ($idx === 0) is-active @endif"
                            data-index="{{ $idx }}">
                            <a href="{{ $slide['url'] }}" class="slide-link" aria-label="{{ $slide['title'] }}">
                                <img src="{{ $slide['src'] }}" alt="{{ $slide['title'] }}" width="1600" height="420"
                                    @if ($idx > 0) loading="lazy" @endif>
                            </a>
                        </div>
                    @endforeach
                </div>
                @if (count($slides) > 1)
                    <button class="slide-nav prev" aria-label="Trước"><i class="far fa-chevron-left"></i></button>
                    <!-- <button class="slide-nav next" aria-label="Sau"><i class="far fa-chevron-right"></i></button> -->
                    <div class="slide-dots">
                        @foreach ($slides as $i => $s)
                            <button class="slide-dot @if ($i === 0) active @endif"
                                data-go="{{ $i }}" aria-label="Tới slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="slide-indicators">
                        @foreach ($slides as $i => $s)
                            <button class="indicator-item @if ($i === 0) active @endif"
                                data-go="{{ $i }}" aria-label="Slide {{ $i + 1 }}: {{ $s['title'] }}">
                                <img src="{{ $s['src'] }}" alt="{{ $s['title'] }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="container container-900">
                <div class="search-filter-inner" data-aos="zoom-out-down" data-aos-duration="700">
                    <form id="searchForm" action="{{ route('client.tours.index') }}" method="GET"
                        class="d-flex align-items-center">
                        <div class="filter-item clearfix flex-fill">
                            <div class="icon"><i class="fal fa-map-marker-alt"></i></div>

                            <button type="button" id="destinationButton" class="destination-select">
                                <span style="font-size: 20px" id="selectedDestination">Bạn muốn đi đâu ?</span>
                                <i class="far fa-chevron-down"></i>
                            </button>
                            <input type="hidden" name="departure" id="departure" required>
                        </div>
                        <div class="search-button">
                            <button type="submit" class="theme-btn">
                                <span data-hover="Tìm kiếm">Tìm kiếm</span>
                                <i class="far fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Destination Popup -->
                    <!-- Popup chọn điểm đến -->
                    <div id="destinationPopup" class="destination-popup">
                        <div class="popup-overlay"></div>
                        <div class="popup-wrapper" data-aos="zoom-in" data-aos-duration="500">
                            <div class="popup-header d-flex justify-content-between align-items-center">
                                <h4 class="popup-title mb-0">Chọn điểm đến</h4>
                                <button type="button" class="close-popup" id="closePopup">
                                    <i class="far fa-times"></i>
                                </button>
                            </div>

                            <div class="popup-body">
                                <div class="destination-search position-relative mb-3">
                                    <input type="text" id="destinationSearch" placeholder="Tìm kiếm điểm đến..."
                                        class="search-input form-control ps-5" />
                                    <i class="far fa-search search-icon position-absolute"></i>
                                </div>

                                <div class="destination-grid">
                                    @foreach ($departures ?? [] as $departure)
                                        @php
                                            // Đếm số lượng tours thực tế cho từng điểm đến
                                            $tourCount = isset($popularTours)
                                                ? $popularTours->where('departurePoint', $departure)->count()
                                                : 0;
                                        @endphp
                                        <div class="destination-box" data-value="{{ $departure }}">
                                            <div class="destination-content">
                                                <span class="destination-name">{{ $departure }}</span>
                                                <span class="tour-count">{{ $tourCount }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
    </section>
    <!-- Hero Area End -->

    <!-- Destinations Area start -->
    <section class="destinations-area bgc-lighter pt-50 pb-40 rel z-1">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="section-title text-black text-center counter-text-wrap mb-50" data-aos="fade-up"
                        data-aos-duration="450" data-aos-offset="20" data-aos-anchor-placement="top-bottom">
                        <div class="title-inline">
                            <h2>Khám phá các địa điểm du lịch cùng</h2>
                            <div class="logo"><a href="{{ route('home') }}"><img
                                        src="{{ asset('assets/images/logos/logo.png') }}" alt="Logo"></a></div>
                        </div>
                        <p>Hơn <span class="count-text plus" data-speed="3000" data-stop="1200">0</span> trải nghiệm đáng
                            nhớ đang chờ bạn</p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="500" data-aos-offset="80">
                @php $images = [1,2,3,4]; @endphp
                @forelse(($categories ?? collect())->take(8) as $idx => $cat)
                    <div class="col-xxl-3 col-xl-4 col-md-6 mb-4">
                        <div class="destination-item style-four" data-aos="zoom-in" data-aos-duration="600"
                            data-aos-delay="{{ $idx * 60 }}">
                            <div class="image">
                                <img src="{{ $cat->imageURL ?: asset('assets/images/destinations/dest' . $images[$idx % 4] . '.jpg') }}"
                                    alt="{{ $cat->categoryName }}" width="600" height="400" loading="lazy"
                                    style="width:100%;height:280px;">
                            </div>
                            <div class="content">
                                <h5><a
                                        href="{{ route('client.tours.category', ['category' => $cat->categoryID]) }}">{{ $cat->categoryName }}</a>
                                </h5>
                                <span>{{ $cat->tours_count }} tour</span>
                            </div>
                            <div class="destination-footer">
                                <a href="{{ route('client.tours.category', ['category' => $cat->categoryID]) }}"
                                    class="theme-btn ">Xem chi tiết <i class="far fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted mb-0">Chưa có dữ liệu danh mục.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- Destinations Area end -->



    <!-- Popular Destinations Area (placeholder) -->
    <section class="popular-destinations-area rel z-1">
        <div class="container-fluid">
            <div class="popular-destinations-wrap br-20 bgc-lighter pt-50 pb-70">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="section-title text-center counter-text-wrap mb-70" data-aos="fade-up"
                            data-aos-duration="700">
                            <h2>Điểm đến phổ biến</h2>
                            <p>Lựa chọn được yêu thích bởi khách hàng</p>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row justify-content-center align-items-stretch g-4">
                        @php
                            $list = ($popularTours ?? collect())->take(8);
                        @endphp
                        @forelse($list as $idx => $tour)
                            <div class="col-xl-3 col-md-6 d-flex">
                                <div class="destination-item style-two h-100 w-100 d-flex flex-column" data-aos="fade-up"
                                    data-aos-duration="700" data-aos-delay="{{ $idx * 60 }}">
                                    <div class="image">
                                        <a href="{{ route('client.tours.show', ['id' => $tour->tourID]) }}">
                                            <img src="{{ $tour->image_path ?: asset('assets/images/destinations/pop' . (($idx % 3) + 1) . '.jpg') }}"
                                                alt="{{ $tour->departurePoint }}" width="600" height="400"
                                                loading="lazy" style="width:100%;height:auto;">
                                        </a>
                                    </div>
                                    <div class="content">
                                        <h6 class="mt-2"><a
                                                href="{{ route('client.tours.show', ['id' => $tour->tourID]) }}">{{ $tour->departurePoint }}</a>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <p class="text-muted mb-0">Chưa có tour để hiển thị.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Hotel Area start -->
    <!-- <section class="hotel-area bgc-lighter p-80 rel z-1">
                                                                <div class="container-fluid bgc-lighter">
                                                                    <div class="row justify-content-center ">
                                                                        <div class="col-lg-12">
                                                                            <div class="section-title text-black text-center counter-text-wrap mb-70" data-aos="fade-up" data-aos-duration="700">
                                                                                <h2>Khám phá khách sạn hàng đầu</h2>
                                                                                <p>Hơn <span class="count-text plus" data-speed="3000" data-stop="34500">0</span> trải nghiệm tuyệt vời chờ bạn</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row justify-content-center g-4">
                                                                        @php $hotels = ($featuredHotels ?? []); @endphp
                                                                        @forelse($hotels as $i => $h)
                                                                        <div class="col-xxl-6 col-xl-8 col-lg-10">
                                                                            <div class="destination-item style-four" data-aos="fade-up" data-aos-duration="700" data-aos-delay="{{ $i * 80 }}">
                                                                                <div class="image">
                                                                                    <img src="{{ $h['image'] ?: asset('assets/images/hotels/vinpearl-halong.jpg') }}" width="200" height="360" loading="lazy" style="width:100%;height:230px;object-fit:cover;">
                                                                                </div>
                                                                                <div class="content p-4">
                                                                                    <h5 style="color: black;" class="mb-1">{{ $h['name'] }}</h5>
                                                                                    <div class="d-flex align-items-center gap-2 small text-warning" aria-label="Đánh giá {{ $h['rating'] }} trên 5 sao">
                                                                                        @for ($s = 1; $s <= 5; $s++)
    @if ($s <= ($h['rating'] ?? 0))
    <i class="fas fa-star"></i>
@else
    <i class="far fa-star"></i>
    @endif
    @endfor
                                                                                            <span class="text-muted ms-2">{{ $h['rating'] }}/5</span>
                                                                                    </div>
                                                                                    <div class="text-muted small mt-1">
                                                                                        <i class="fal fa-map-marker-alt me-1"></i>{{ $h['departurePoint'] ?? 'Địa điểm không xác định' }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
            @empty
                                                                        <div class="col-12 text-center">
                                                                            <p class="text-muted mb-0">Chưa có dữ liệu khách sạn.</p>
                                                                        </div>
                                                                        @endforelse
                                                                    </div>
                                                                    <div  class="hotel-more-btn text-center mt-40">
                                                                        <a style="background-color: #63AB45;" href="#" class="theme-btn style-four">
                                                                            <span  data-hover="Xem thêm khách sạn">Xem thêm khách sạn</span>
                                                                            <i class="fal fa-arrow-right"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </section> -->
    <!-- Hotel Area end -->
    <!-- About Us Area start -->
    <section id="about" class="about-us-area py-50 rpb-90 rel z-1">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-5 col-lg-6">
                    <div class="about-us-content rmb-55" data-aos="fade-left" data-aos-duration="700">
                        <div class="section-title mb-25">
                            <h2>Về chúng tôi</h2>
                        </div>
                        <p>Chúng tôi luôn nỗ lực vượt mong đợi để biến giấc mơ du lịch của bạn thành hiện thực, kết hợp giữa
                            điểm đến nổi tiếng và những viên ngọc ẩn mình.</p>
                        <div class="divider counter-text-wrap mt-45 mb-55"><span>Chúng tôi có <span><span
                                        class="count-text plus" data-speed="3000" data-stop="5">0</span> năm</span> kinh
                                nghiệm</span></div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded small">Dịch vụ tận tâm</div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded small">Hỗ trợ 24/7</div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded small">Tour đa dạng</div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded small">Giá minh bạch</div>
                            </div>
                        </div>
                        <a href="#about" class="theme-btn mt-10 style-two"><span data-hover="Tìm hiểu thêm">Tìm hiểu
                                thêm</span><i class="fal fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-xl-7 col-lg-6" data-aos="fade-right" data-aos-duration="700">
                    <div class="about-us-image">
                        <img src="{{ asset('assets/images/about/about.png') }}" alt="About">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About Us Area end -->
    <!-- Features Area start -->
    <!-- <section class="features-area pt-100 pb-45 rel z-1">
                                                                <div class="container">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-xl-6">
                                                                            <div class="features-content-part mb-55" data-aos="fade-left" data-aos-duration="700">
                                                                                <div class="section-title mb-60">
                                                                                    <h2>Vì sao chọn TripGo?</h2>
                                                                                    <p>Chất lượng dịch vụ tạo nên khác biệt.</p>
                                                                                </div>
                                                                                <div class="features-customer-box d-grid gap-3">
                                                                                    <div class="p-3 bg-light rounded">1. Hành trình linh hoạt</div>
                                                                                    <div class="p-3 bg-light rounded">2. Hỗ trợ chuyên nghiệp</div>
                                                                                    <div class="p-3 bg-light rounded">3. Đánh giá minh bạch</div>
                                                                                    <div class="p-3 bg-light rounded">4. Thanh toán an toàn</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-xl-6" data-aos="fade-right" data-aos-duration="700">
                                                                            <div class="row pb-25 g-3">
                                                                                <div class="col-md-6">
                                                                                    <div class="h-100 bgc-black text-white p-4 rounded">Ưu đãi độc quyền</div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="h-100 bgc-black text-white p-4 rounded">Hơn 500+ khách hàng</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </section> -->
    <!-- Features Area end -->


    <!-- Blog Area start -->
    <!-- <section class="blog-area py-70 rel z-1">
                                                                <div class="container">
                                                                    <div class="row justify-content-center">
                                                                        <div class="col-lg-12">
                                                                            <div class="section-title text-center counter-text-wrap mb-70" data-aos="fade-up" data-aos-duration="700">
                                                                                <h2>Tin & Blog Du Lịch</h2>
                                                                                <p>Cập nhật thông tin và mẹo du lịch mới nhất.</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row justify-content-center g-4">
                                                                        @for ($i = 0; $i < 3; $i++)
    <div class="col-xl-4 col-md-6">
                                                                            <div class="blog-item" data-aos="fade-up" data-aos-duration="700">
                                                                                <div class="image" style="height:200px;border-radius:10px;overflow:hidden;">
                                                                                    <img src="{{ asset('assets/images/blog/blog' . (($i % 3) + 1) . '.jpg') }}" alt="Bài viết {{ $i + 1 }}" style="width:100%;height:100%;object-fit:cover;">
                                                                                </div>
                                                                                <div class="content p-3">
                                                                                    <h5><a href="#">Bài viết {{ $i + 1 }}</a></h5>
                                                                                    <p class="small mb-2">Mô tả ngắn gọn nội dung nổi bật của bài viết.</p>
                                                                                    <a href="#" class="read-more">Đọc tiếp <i class="far fa-arrow-right"></i></a>
                                                                                </div>
                                                                            </div>
                                                                    </div>
    @endfor
                                                                </div>
                                                                </div>
                                                            </section> -->
    <!-- Blog Area end -->

    @push('styles')
        <style>
            .search-filter-inner {
                padding: 20px 24px;
                border-radius: 22px;
                background: #ffffffc9;
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
                display: flex;
                align-items: center;
                gap: 20px;
            }

            .filter-item {
                position: relative;
                flex: 1;
                background: transparent;
                border: none;
                padding: 0;
            }

            .filter-item .icon {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #63AB45;
                font-size: 18px;
                z-index: 2;
            }

            .filter-item .title {
                position: absolute;
                left: 45px;
                top: 8px;
                font-size: 12px;
                color: #666;
                font-weight: 500;
                z-index: 2;
                pointer-events: none;
            }

            .destination-select {
                width: 100%;
                padding: 35px 15px 10px 45px;
                border: none;
                background: transparent;
                font-size: 16px;
                color: #333;
                outline: none;
                appearance: none;
                cursor: pointer;
                min-height: 60px;
            }

            .destination-select:focus {
                outline: none;
            }

            .destination-select option {
                background: white;
                color: #333;
                padding: 10px;
            }

            .search-button {
                flex-shrink: 0;
            }

            /* Hero slideshow styles (enhanced) */
            .hero-slideshow {
                min-height: 420px;
            }

            .hero-slideshow .slides-wrapper {
                position: relative;
            }

            /* Inline title + logo */
            .section-title .title-inline {
                display: inline-flex;
                align-items: center;
                gap: 14px;
                flex-wrap: nowrap;
            }

            .section-title .title-inline h2 {
                margin: 0;
                white-space: nowrap;
                display: inline-block;
            }

            .section-title .title-inline .logo img {
                height: 64px;
                width: auto;
                display: block;
            }

            .hero-slideshow .hero-slide {
                position: absolute;
                inset: 0;
                opacity: 0;
                transition: opacity .9s ease, transform 1s ease;
            }

            .hero-slideshow[data-anim="fade-scale"] .hero-slide {
                transform: scale(1.04);
            }

            .hero-slideshow[data-anim="fade-scale"] .hero-slide.is-active {
                transform: scale(1);
            }

            .hero-slideshow[data-anim="slide"] .hero-slide {
                transform: translateX(20%);
            }

            .hero-slideshow[data-anim="slide"] .hero-slide.is-active {
                transform: translateX(0);
            }

            .hero-slideshow[data-anim="kenburns"] .hero-slide img {
                animation: kenburns 12s linear infinite;
            }

            @keyframes kenburns {
                0% {
                    transform: scale(1) translate(0, 0);
                }

                100% {
                    transform: scale(1.1) translate(2%, 2%);
                }
            }

            .hero-slideshow .hero-slide img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                filter: brightness(.88);
            }

            .hero-slideshow .hero-slide.is-active {
                opacity: 1;
                z-index: 2;
            }

            .hero-slideshow .hero-slide .slide-caption {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                color: #fff;
                text-align: center;
                text-shadow: 0 3px 12px rgba(0, 0, 0, .55);
                max-width: 70%;
            }

            .hero-slideshow .hero-slide .slide-caption h2 {
                font-size: clamp(1.8rem, 4vw, 3.2rem);
                margin: 0;
                font-weight: 700;
                animation: fadeInUp .9s ease both;
            }

            @keyframes fadeInUp {
                0% {
                    opacity: 0;
                    transform: translate(-50%, -40%);
                }

                100% {
                    opacity: 1;
                    transform: translate(-50%, -50%);
                }
            }

            .hero-slideshow .slide-nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(0, 0, 0, .45);
                border: none;
                color: #fff;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(2px);
                cursor: pointer;
                z-index: 5;
            }

            .hero-slideshow .slide-nav:hover {
                background: rgba(0, 0, 0, .65);
            }

            .hero-slideshow .slide-nav.prev {
                left: 15px;
            }

            .hero-slideshow .slide-nav.next {
                right: 15px;
            }

            .hero-slideshow .slide-dots {
                position: absolute;
                bottom: 12px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 8px;
                z-index: 6;
            }

            .hero-slideshow .slide-dot {
                width: 11px;
                height: 11px;
                border-radius: 50%;
                border: 0;
                background: rgba(255, 255, 255, .45);
                padding: 0;
                cursor: pointer;
                transition: background .3s, transform .3s;
            }

            .hero-slideshow .slide-dot.active {
                background: #fff;
                transform: scale(1.15);
            }

            .hero-slideshow .slide-dot:focus-visible {
                outline: 2px solid #fff;
                outline-offset: 2px;
            }

            .hero-slideshow .slide-indicators {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                display: flex;
                flex-direction: column;
                gap: 8px;
                z-index: 6;
            }

            .hero-slideshow .indicator-item {
                width: 56px;
                height: 40px;
                padding: 0;
                border: 2px solid transparent;
                background: #1119;
                border-radius: 6px;
                overflow: hidden;
                cursor: pointer;
                position: relative;
            }

            .hero-slideshow .indicator-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                filter: brightness(.6);
                transition: filter .3s, transform .3s;
            }

            .hero-slideshow .indicator-item.active img,
            .hero-slideshow .indicator-item:hover img {
                filter: brightness(1);
                transform: scale(1.05);
            }

            .hero-slideshow .indicator-item.active {
                border-color: #fff;
            }

            /* Appear animation when page loads */
            .hero-slideshow.appears {
                opacity: 0;
                animation: heroFadeIn .9s ease forwards;
            }

            @keyframes heroFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @media (max-width:992px) {
                .hero-slideshow .slide-indicators {
                    display: none;
                }
            }

            @media (max-width:768px) {
                .hero-slideshow .slides-wrapper {
                    height: 300px !important;
                }
            }

            /* --- FORM SEARCH FILTER --- */
            .search-filter-inner {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px 24px;
                border-radius: 22px;
                background: #ffffffc9;
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                max-height: 7.7rem;
                max-width: 40rem;
                margin-left: 100px;
            }

            /* Form layout ngang */
            #searchForm {
                display: flex;
                align-items: center;
                width: 100%;
                gap: 16px;
            }

            /* Mỗi filter-item (điểm đến) */
            .filter-item {
                display: flex;
                align-items: center;
                background: rgba(255, 255, 255, 0.7);
                border-radius: 16px;
                padding: 10px 16px;
                gap: 10px;
                flex: 1;
                transition: background 0.3s ease;
            }

            .filter-item:hover {
                background: rgba(255, 255, 255, 0.9);
            }

            /* Icon phía trước */
            .filter-item .icon {
                color: #63AB45;
                font-size: 18px;
            }

            /* Tiêu đề “Điểm đến” */
            .filter-item .title {
                font-weight: 500;
                color: #333;
                white-space: nowrap;
            }

            /* Select box */
            .destination-select {
                flex: 1;
                border: none;
                outline: none;
                background: transparent;
                font-size: 15px;
                color: #333;
                padding: 6px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                cursor: pointer;
                transition: all 0.3s ease;

            }

            .destination-select:hover {
                color: #63AB45;
            }

            .destination-select i {
                margin-left: 8px;
                font-size: 12px;
                transition: transform 0.3s ease;
            }

            .destination-select.active i {
                transform: rotate(180deg);
            }

            /* Destination Popup Styles */
            .destination-popup {
                position: fixed;
                inset: 0;
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                pointer-events: none;
                margin-bottom: 15rem;
            }

            .destination-popup.active {
                display: flex;
            }

            .popup-overlay {
                position: absolute;
                inset: 0;
                background: transparent;
                pointer-events: all;
            }

            .popup-wrapper {
                position: relative;
                background: #fff;
                border-radius: 16px;
                max-width: 800px;
                width: 95%;
                padding: 20px 24px;
                z-index: 10;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
                animation: fadeIn 0.4s ease;
                max-height: 80vh;
                overflow: hidden;
                pointer-events: all;
                border: 2px solid #e9ecef;
            }

            @keyframes fadeIn {
                from {
                    transform: translateY(10px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .popup-header {
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
                margin-bottom: 15px;
            }

            .popup-title {
                font-size: 1.3rem;
                font-weight: 600;
                color: #333;
            }

            .close-popup {
                background: none;
                border: none;
                color: #333;
                font-size: 1.2rem;
                cursor: pointer;
                transition: 0.2s;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
            }

            .close-popup:hover {
                color: #ff5a5f;
                background: #f5f5f5;
            }

            .destination-search .search-input {
                width: 100%;
                border: 2px solid #eee;
                border-radius: 12px;
                padding: 12px 16px 12px 45px;
                font-size: 15px;
                outline: none;
                transition: 0.3s;
            }

            .destination-search .search-input:focus {
                border-color: #63AB45;
                box-shadow: 0 0 0 3px rgba(99, 171, 69, 0.1);
            }

            .search-icon {
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #999;
                font-size: 16px;
            }

            .destination-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 8px;
                max-height: 400px;
                overflow-y: auto;
                padding: 5px;
            }

            .destination-box {
                background: #f8f9fa;
                border: 2px solid transparent;
                border-radius: 8px;
                padding: 10px 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
            }

            .destination-box:hover {
                background: #e8f5e8;
                border-color: #63AB45;
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(99, 171, 69, 0.15);
            }

            .destination-box.selected {
                background: #63AB45;
                border-color: #63AB45;
                color: #fff;
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(99, 171, 69, 0.3);
            }

            .destination-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
            }

            .destination-name {
                font-size: 13px;
                font-weight: 600;
                color: #333;
                line-height: 1.2;
                flex: 1;
                text-align: left;
            }

            .destination-box.selected .destination-name {
                color: #fff;
            }

            .tour-count {
                font-size: 12px;
                font-weight: 700;
                color: #63AB45;
                background: rgba(99, 171, 69, 0.1);
                padding: 2px 6px;
                border-radius: 10px;
                min-width: 20px;
                text-align: center;
                margin-left: 8px;
            }

            .destination-box.selected .tour-count {
                color: #fff;
                background: rgba(255, 255, 255, 0.2);
            }

            /* Hide items when searching */
            .destination-box.hidden {
                display: none;
            }

            /* Custom scrollbar for destination grid */
            .destination-grid::-webkit-scrollbar {
                width: 6px;
            }

            .destination-grid::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }

            .destination-grid::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 3px;
            }

            .destination-grid::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }

            /* Nút tìm kiếm */
            .search-button .theme-btn {
                background: #63AB45;
                color: #fff;
                border: none;
                border-radius: 16px;
                padding: 10px 20px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.25s ease;
            }

            .search-button .theme-btn:hover {
                background: #57993d;
                transform: scale(1.05);
            }

            /* Responsive - Mobile */
            @media (max-width: 768px) {
                #searchForm {
                    flex-direction: column;
                    gap: 10px;
                }

                .filter-item {
                    width: 100%;
                }

                .search-button {
                    width: 100%;
                }

                .search-button .theme-btn {
                    width: 100%;
                    justify-content: center;
                }

                .popup-wrapper {
                    width: 98%;
                    max-width: none;
                    margin: 0 auto;
                    max-height: 85vh;
                    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);
                }

                .popup-header {
                    padding-bottom: 12px;
                    margin-bottom: 12px;
                }

                .popup-title {
                    font-size: 1.1rem;
                }

                .popup-body {
                    padding: 0;
                }

                .destination-grid {
                    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                    gap: 6px;
                    max-height: 350px;
                }

                .destination-box {
                    padding: 8px 10px;
                }

                .destination-name {
                    font-size: 12px;
                }

                .tour-count {
                    font-size: 11px;
                    padding: 1px 5px;
                    margin-left: 6px;
                }
            }

            @media (max-width: 480px) {
                .destination-grid {
                    grid-template-columns: 1fr;
                    gap: 4px;
                }

                .destination-box {
                    padding: 6px 8px;
                }

                .destination-name {
                    font-size: 11px;
                }

                .tour-count {
                    font-size: 10px;
                    padding: 1px 4px;
                    margin-left: 4px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/slideshow.js') }}" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchForm = document.getElementById('searchForm');
                const departureInput = document.getElementById('departure');
                const destinationButton = document.getElementById('destinationButton');
                const selectedDestination = document.getElementById('selectedDestination');
                const destinationPopup = document.getElementById('destinationPopup');
                const closePopup = document.getElementById('closePopup');
                const popupOverlay = destinationPopup.querySelector('.popup-overlay');
                const destinationSearch = document.getElementById('destinationSearch');
                const destinationBoxes = destinationPopup.querySelectorAll('.destination-box');

                // Open popup when click destination button
                destinationButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    destinationPopup.classList.add('active');

                    // Focus search input
                    setTimeout(() => {
                        destinationSearch.focus();
                    }, 100);
                });

                // Close popup
                function closeDestinationPopup() {
                    destinationPopup.classList.remove('active');
                    destinationSearch.value = '';

                    // Reset search results
                    destinationBoxes.forEach(box => {
                        box.classList.remove('hidden');
                    });
                }

                // Close popup events
                closePopup.addEventListener('click', closeDestinationPopup);
                popupOverlay.addEventListener('click', function(e) {
                    // Only close if clicking on the overlay itself, not the popup content
                    if (e.target === popupOverlay) {
                        closeDestinationPopup();
                    }
                });

                // Close when clicking outside the popup wrapper
                destinationPopup.addEventListener('click', function(e) {
                    if (e.target === destinationPopup) {
                        closeDestinationPopup();
                    }
                });

                // Close on Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && destinationPopup.classList.contains('active')) {
                        closeDestinationPopup();
                    }
                });

                // Search functionality
                destinationSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();

                    destinationBoxes.forEach(box => {
                        const text = box.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            box.classList.remove('hidden');
                        } else {
                            box.classList.add('hidden');
                        }
                    });
                });

                // Select destination
                destinationBoxes.forEach(box => {
                    box.addEventListener('click', function() {
                        const value = this.getAttribute('data-value');
                        const text = this.querySelector('.destination-name').textContent;

                        // Update hidden input and button text
                        departureInput.value = value;
                        selectedDestination.textContent = text;

                        // Remove selected class from all boxes
                        destinationBoxes.forEach(b => b.classList.remove('selected'));

                        // Add selected class to clicked box
                        this.classList.add('selected');

                        // Close popup
                        closeDestinationPopup();

                        // Reset any error styling
                        destinationButton.style.borderColor = '';
                    });
                });

                // Handle form submission
                if (searchForm) {
                    searchForm.addEventListener('submit', function(e) {
                        const departure = departureInput.value;
                        if (!departure) {
                            e.preventDefault();

                            // Style the button to show error
                            destinationButton.style.borderColor = '#dc3545';
                            destinationButton.style.border = '2px solid #dc3545';
                            destinationButton.focus();

                            // Reset border color after focus
                            setTimeout(() => {
                                destinationButton.style.borderColor = '';
                                destinationButton.style.border = '';
                            }, 3000);

                            return false;
                        }
                    });
                }
            });
        </script>
    @endpush

@endsection
