<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng ký | TripGo</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logos/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome-5.14.0.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e5ffd9, #fff7ec 55%, #ffe4d1);
            font-family: 'Outfit', sans-serif;
        }

        .auth-shell {
            display: flex;
            min-height: 100vh;
        }

        .illustration {
            flex: 1.05;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 50px 60px;
            position: relative;
            overflow: hidden;
        }

        .illus-inner {
            max-width: 560px;
            width: 100%;
            position: relative;
        }

        .illus-card {
            position: relative;
            border-radius: 38px;
            overflow: hidden;
            box-shadow: 0 30px 55px -22px rgba(0, 0, 0, .35);
        }

        .illus-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(.92);
        }

        .illus-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(140deg, rgba(99, 171, 69, .55), rgba(255, 107, 61, .55));
            mix-blend-mode: overlay;
        }

        .illus-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 42px 45px 34px;
            color: #fff;
            backdrop-filter: blur(6px);
            background: linear-gradient(180deg, rgba(0, 0, 0, 0), rgba(0, 0, 0, .78));
        }

        .illus-caption h2 {
            font-size: clamp(1.45rem, 2.3vw, 2.2rem);
            margin: 0 0 .55rem;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .illus-caption p {
            font-size: .85rem;
            max-width: 460px;
            margin: 0;
            opacity: .92;
        }

        .shapeA,
        .shapeB {
            position: absolute;
            border-radius: 50%;
            filter: blur(2px) saturate(1.2);
            animation: floatY 8s ease-in-out infinite;
            z-index: 3;
        }

        .shapeA {
            top: 9%;
            left: 7%;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle at 30% 30%, #63AB45, #4e8e34);
        }

        .shapeB {
            bottom: 12%;
            right: 10%;
            width: 190px;
            height: 190px;
            background: radial-gradient(circle at 30% 30%, #ffb347, #ff6b3d);
            animation-delay: -3.2s;
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-30px)
            }
        }

        .form-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 30px 7vw 60px;
        }

        .brand-inline img {
            height: 60px;
            margin-right: 10px;
        }

        .brand-inline h1 {
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            margin: 0;
            font-weight: 700;
            background: linear-gradient(90deg, #63AB45, #ff6b3d 60%, #ff6b6b);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .register-panel {
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 25px 50px -15px rgba(0, 0, 0, .12);
            width: 100%;
            max-width: 520px;
            padding: 46px 50px 54px;
            position: relative;
            overflow: hidden;
            margin-left: 100px;
        }

        .register-panel:before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(150deg, rgba(255, 179, 71, .18), rgba(255, 107, 61, .12) 42%, rgba(99, 171, 69, .18));
            pointer-events: none;
        }

        .panel-title {
            font-weight: 700;
            font-size: 1.9rem;
            line-height: 1.05;
            margin: 0 0 .6rem;
            letter-spacing: .5px;
        }

        .panel-sub {
            font-size: .9rem;
            color: #555;
            margin-bottom: 1.55rem;
        }

        .form-label {
            font-weight: 600;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #222;
            margin-bottom: 4px;
        }

        .form-control {
            border-radius: 14px;
            padding: .75rem 1rem;
            border: 1px solid #d9d9d9;
            font-size: .95rem;
        }

        .form-control:focus {
            border-color: #ff6b3d;
            box-shadow: 0 0 0 3px rgba(255, 107, 61, .25);
        }

        .btn-register {
            height: 50px;
            background: linear-gradient(95deg, #ffb347, #ff6b3d 50%, #ff6b6b);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 18px;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 28px -8px rgba(255, 107, 61, .6);
            transition: .45s;
        }

        .btn-register:hover {
            filter: brightness(1.07) saturate(1.05);
            transform: translateY(-2px);
        }

        .alt-link {
            font-size: .85rem;
            margin-top: 1.1rem;
            color: #444;
        }

        .alt-link a {
            color: #63AB45;
            font-weight: 600;
        }

        .alt-link a:hover {
            text-decoration: underline;
        }

        .error-list {
            font-size: .75rem;
        }

        @media (max-width:1100px) {
            .illustration {
                display: none
            }

            .form-side {
                flex: 1;
                padding: 70px 9vw;
            }
        }

        @media (max-width:600px) {
            .register-panel {
                padding: 42px 34px 50px;
            }

            .brand-inline h1 {
                font-size: 1.8rem
            }
        }
    </style>
</head>

<body>
    <div class="auth-shell">
        <div class="form-side">
            <div class="brand-inline mb-4" data-aos="fade-right" data-aos-duration="800">
                <a href="{{ route('home') }}" class="d-inline-flex align-items-center text-decoration-none">
                    <img src="{{ asset('assets/images/logos/logo.png') }}" alt="TripGo">

                </a>
            </div>
            <div class="register-panel" data-aos="zoom-in" data-aos-duration="800">
                <h2 class="panel-title">Tạo tài khoản</h2>
                <div class="panel-sub">Chỉ vài bước để bắt đầu hành trình.</div>

                <form method="POST" action="{{ route('register.post') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                            id="username" name="username" value="{{ old('username') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                            id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                            name="address" value="{{ old('address') }}">
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select id="gender" name="gender"
                            class="form-control @error('gender') is-invalid @enderror">
                            <option value="">-- Chọn --</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required>
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn-register">
                        <span>Đăng ký</span><i class="far fa-user-plus"></i>
                    </button>
                </form>
                <div class="alt-link">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></div>
            </div>
        </div>
        <div class="illustration" data-aos="fade-left" data-aos-duration="1000">
            <div class="shapeA"></div>
            <div class="shapeB"></div>
            <div class="illus-inner">
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/aos.js') }}"></script>
    <script>
        AOS.init({
            once: true,
            duration: 700
        });
    </script>
</body>

</html>
