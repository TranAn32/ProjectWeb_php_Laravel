<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>@yield('title','Khách hàng | TripGo')</title>
	<link rel="shortcut icon" href="{{ asset('assets/images/logos/favicon.png') }}" type="image/x-icon">
	<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/flaticon.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/fontawesome-5.14.0.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/nice-select.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/aos.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/slick.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
	@stack('styles')
</head>

<body>
	<div class="page-wrapper">
		<!-- Navbar embedded -->
		<header class="main-header header-one white-menu custom-header">
			<div style="padding: 5px 10px;" class="header-upper rpy-0">
				<div class="container-fluid clearfix">
					<div class="header-inner rel d-flex align-items-center">
						<div class="logo-outer">
							<div class="logo"><a href="{{ route('home') }}"><img src="{{ asset('assets/images/logos/logo.png') }}" alt="Logo"></a></div>
						</div>
						<div class="nav-outer mx-lg-auto ps-xxl-5 clearfix">
							<nav class="main-menu navbar-expand-lg">
								<div class="navbar-header">
									<div class="mobile-logo"><a href="{{ route('home') }}"><img src="{{ asset('assets/images/logos/logo.png') }}" alt="Logo"></a></div>

								</div>
								<div class="navbar-collapse collapse clearfix">
									<ul class="navigation clearfix">
										<li class="{{ request()->routeIs('home') ? 'current' : '' }}"><a href="{{ route('home') }}"><i class="fa fa-home" aria-hidden="true"></i> Trang chủ</a></li>
										<li class="dropdown {{ request()->routeIs('client.tours.*') ? 'current' : '' }}">
											<a href="{{ route('client.tours.index') }}" data-bs-toggle="dropdown" aria-expanded="false">
												<i class="fa fa-suitcase" aria-hidden="true"></i> Tour du lịch <span class="caret"></span>
											</a>
											<ul class="dropdown-menu" style="min-width:200px;">
												<li><a class="dropdown-item" href="{{ route('client.tours.index') }}">Tất cả tour</a></li>
												<li><a class="dropdown-item" href="{{ route('client.tours.type', ['type' => 'domestic']) }}">Tour trong nước</a></li>
												<li><a class="dropdown-item" href="{{ route('client.tours.type', ['type' => 'international']) }}">Tour nước ngoài</a></li>
											</ul>
										</li>
										<li class="{{ request()->routeIs('client.booking') ? 'current' : '' }}"><a href="{{ route('client.booking') }}"><i class="fa fa-ticket" aria-hidden="true" style="display:inline-block;transform:rotate(-45deg);"></i> Combo khuyến mại</a></li>
										<li><a href="#about"><i class="fa fa-info-circle" aria-hidden="true"></i> Giới thiệu</a></li>
										<!-- <li><a href="#contact"><i class="fa fa-envelope" aria-hidden="true"></i> Liên hệ</a></li> -->

									</ul>
								</div>
							</nav>
						</div>
						<!-- Nav Search -->
						<div class="nav-search">
							<button class="far fa-search"></button>
							<form action="#" class="hide">
								<input type="text" placeholder="Tìm kiếm" class="searchbox" required="">
								<button type="submit" class="searchbutton far fa-search"></button>
							</form>
						</div>
						<div class="menu-btns py-8">
							<a href="contact.html" class="theme-btn style-two bgc-secondary">
								<span data-hover="Đặt ngay">Đặt ngay</span>
								<i class="fal fa-arrow-right"></i>
							</a>
							<!-- menu sidbar -->
							<div class="account-icon ms-2">
								@php($loginUrl = Route::has('login') ? route('login') : '#')
								<a href="{{ $loginUrl }}" class="d-inline-flex align-items-center justify-content-center" aria-label="Tài khoản" style="width:44px;height:44px;border-radius:50%;background:rgba(0,0,0,.45);color:#c5c3c3;text-decoration:none;">
									<i class="far fa-user" style="color: #fff; font-size:18px;"></i>
								</a>
							</div>
						</div>

					</div>
				</div>
			</div>
		</header>

		<main class="content-wrapper" style="min-height:60vh;">
			@yield('content')
		</main>

		<!-- Footer embedded -->
		<footer class="main-footer bgs-cover overlay rel z-1 pb-25" style="background-image: url(assets/images/backgrounds/footer.jpg);">
			<div class="container">
				<div class="footer-top pt-100 pb-30">
					<div class="row justify-content-between">
						<div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
							<div class="footer-widget footer-text">
								<div class="footer-logo mb-25">
									<a href="index.html"><img src="assets/images/logos/logo.png" alt="Logo"></a>
								</div>
								<p>Chúng tôi xây dựng các hành trình riêng theo sở thích của bạn, đảm bảo mỗi chuyến đi đều liền mạch và giàu trải nghiệm, khám phá những điểm đến độc đáo.</p>
								<div class="social-style-one mt-15">
									<a href="contact.html"><i class="fab fa-facebook-f"></i></a>
									<a href="contact.html"><i class="fab fa-youtube"></i></a>
									<a href="contact.html"><i class="fab fa-pinterest"></i></a>
									<a href="contact.html"><i class="fab fa-twitter"></i></a>
								</div>
							</div>
						</div>
						<div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-delay="50" data-aos-duration="1500" data-aos-offset="50">
							<div class="section-title counter-text-wrap mb-35">
								<h2>Đăng ký nhận thông tin</h2>
								<p>Hơn <span class="count-text plus" data-speed="3000" data-stop="34500">0</span> trải nghiệm phổ biến đáng nhớ dành cho bạn</p>
							</div>
							<form class="newsletter-form mb-50" action="#">
								<input id="news-email" type="email" placeholder="Địa chỉ email" required>
								<button type="submit" class="theme-btn bgc-secondary style-two">
									<span data-hover="Đăng ký">Đăng ký</span>
									<i class="fal fa-arrow-right"></i>
								</button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="widget-area pt-95 pb-45">
				<div class="container">
					<div class="row row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
						<div class="col col-small" data-aos="fade-up" data-aos-duration="1500" data-aos-offset="50">
							<div class="footer-widget footer-links">
								<div class="footer-title">
									<h5>Dịch vụ</h5>
								</div>
								<ul class="list-style-three">
									<li><a href="destination-details.html">Hướng dẫn viên tốt nhất</a></li>
									<li><a href="destination-details.html">Đặt tour du lịch</a></li>
									<li><a href="destination-details.html">Đặt khách sạn</a></li>
									<li><a href="destination-details.html">Đặt vé</a></li>
									<li><a href="destination-details.html">Dịch vụ thuê</a></li>
								</ul>
							</div>
						</div>
						<div class="col col-small" data-aos="fade-up" data-aos-delay="50" data-aos-duration="1500" data-aos-offset="50">
							<div class="footer-widget footer-links">
								<div class="footer-title">
									<h5>Công ty</h5>
								</div>
								<ul class="list-style-three">
									<li><a href="about.html">Về công ty</a></li>
									<li><a href="blog.html">Blog cộng đồng</a></li>
									<li><a href="contact.html">Việc làm & cơ hội</a></li>
									<li><a href="blog.html">Tin tức mới</a></li>
									<li><a href="contact.html">Liên hệ</a></li>
								</ul>
							</div>
						</div>
						<div class="col col-small" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1500" data-aos-offset="50">
							<div class="footer-widget footer-links">
								<div class="footer-title">
									<h5>Điểm đến</h5>
								</div>
								<ul class="list-style-three">
									<li><a href="destination-details.html">Safari châu Phi</a></li>
									<li><a href="destination-details.html">Alaska & Canada</a></li>
									<li><a href="destination-details.html">Nam Mỹ</a></li>
									<li><a href="destination-details.html">Trung Đông</a></li>
									<li><a href="destination-details.html">Nam Mỹ</a></li>
								</ul>
							</div>
						</div>
						<div class="col col-small" data-aos="fade-up" data-aos-delay="150" data-aos-duration="1500" data-aos-offset="50">
							<div class="footer-widget footer-links">
								<div class="footer-title">
									<h5>Danh mục</h5>
								</div>
								<ul class="list-style-three">
									<li><a href="contact.html">Phiêu lưu</a></li>
									<li><a href="contact.html">Leo núi & trekking</a></li>
									<li><a href="contact.html">tour du lịch đạp xe</a></li>
									<li><a href="contact.html">Tour gia đình</a></li>
									<li><a href="contact.html">tour du lịch thiên nhiên hoang dã</a></li>
								</ul>
							</div>
						</div>
						<div class="col col-md-6 col-10 col-small" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1500" data-aos-offset="50">
							<div class="footer-widget footer-contact">
								<div class="footer-title">
									<h5>Liên hệ</h5>
								</div>
								<ul class="list-style-one">
									<li><i class="fal fa-map-marked-alt"></i> Công ty TNHH Tripgo, 102 Trần Phú, Hà Đông, Hà Nội</li>
									<li><i class="fal fa-envelope"></i> <a href="mailto:supportrevelo@gmail.com">supportripgo@gmail.com</a></li>
									<li><i class="fal fa-clock"></i> Thứ 2 - Thứ 6, 08:00 - 17:00</li>
									<li><i class="fal fa-phone-volume"></i> <a href="callto:+88012334588">+84964546302</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="footer-bottom pt-20 pb-5">
				<div class="container">
					<!-- <div class="row">
						<div class="col-lg-5">
							<div class="copyright-text text-center text-lg-start">
								<p>@Copy 2024 <a href="index.html">Ravelo</a>, All rights reserved</p>
							</div>
						</div>
						<div class="col-lg-7 text-center text-lg-end">
							<ul class="footer-bottom-nav">
								<li><a href="about.html">Terms</a></li>
								<li><a href="about.html">Privacy Policy</a></li>
								<li><a href="about.html">Legal notice</a></li>
								<li><a href="about.html">Accessibility</a></li>
							</ul>
						</div>
					</div> -->
					<!-- Scroll Top Button -->
					<button class="scroll-top scroll-to-target" data-target="html"><img src="assets/images/icons/scroll-up.png" alt="Lên đầu trang"></button>
				</div>
			</div>
		</footer>
	</div>

	<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
	<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('assets/js/appear.min.js') }}"></script>
	<script src="{{ asset('assets/js/slick.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
	<script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}"></script>
	<script src="{{ asset('assets/js/skill.bars.jquery.min.js') }}"></script>
	<script src="{{ asset('assets/js/isotope.pkgd.min.js') }}"></script>
	<script src="{{ asset('assets/js/aos.js') }}"></script>
	<script src="{{ asset('assets/js/script.js') }}"></script>
	@stack('scripts')
</body>

</html>