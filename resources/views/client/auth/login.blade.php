<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập | TripGo</title>
    <link rel="shortcut icon" href="{{ secure_asset('assets/images/logos/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ secure_asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('assets/css/fontawesome-5.14.0.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('assets/css/aos.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('assets/css/style.css') }}">
    <style>
        body {
            min-height: 100vh;
            background: radial-gradient(circle at 25% 30%, #ffe2c4 0, #fff7f0 40%, #f4fff0 100%);
            font-family: 'Outfit', sans-serif;
        }

        .auth-shell {
            display: flex;
            min-height: 100vh;
        }

        .auth-left {
            flex: 1.1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 30px 6vw 50px;
            position: relative;
            z-index: 2;
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

        .auth-panel {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 25px 50px -15px rgba(0, 0, 0, .15);
            max-width: 480px;
            width: 100%;
            padding: 42px 46px 50px;
            position: relative;
            overflow: hidden;
            margin-left: 100px;
        }

        .auth-panel:before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(140deg, rgba(255, 179, 71, .18), rgba(255, 107, 61, .12) 40%, rgba(99, 171, 69, .15));
            pointer-events: none;
        }

        .panel-title {
            font-weight: 700;
            font-size: 1.9rem;
            line-height: 1.05;
            margin: 0 0 .65rem;
            letter-spacing: .5px;
        }

        .panel-sub {
            font-size: .9rem;
            color: #555;
            margin-bottom: 1.6rem;
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
            border-color: #63AB45;
            box-shadow: 0 0 0 3px rgba(99, 171, 69, .2);
        }

        .auth-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: .2rem;
            margin-bottom: 1.1rem;
        }

        .btn-auth {
            height: 50px;
            background: linear-gradient(95deg, #63AB45, #4e8e34);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 16px;
            padding: .95rem 1.4rem;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 6px 18px -6px rgba(78, 142, 52, .6);
            transition: .4s;
        }

        .btn-auth:hover {
            filter: brightness(1.07) contrast(1.05) saturate(1.05);
            transform: translateY(-2px);
        }

        .divider-text {
            display: flex;
            align-items: center;
            text-align: center;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: #666;
            margin: 1.5rem 0 1.15rem;
        }

        .divider-text:before,
        .divider-text:after {
            content: "";
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(0, 0, 0, .15), rgba(0, 0, 0, .05));
        }

        .divider-text:before {
            margin-right: 12px
        }

        .divider-text:after {
            margin-left: 12px
        }

        .social-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 1.25rem;
        }

        .social-btn {
            border: 1px solid #e3e3e3;
            background: #fff;
            border-radius: 14px;
            font-weight: 600;
            font-size: .8rem;
            padding: .7rem .9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            transition: .35s;
            color: #222;
        }

        .social-btn:hover {
            border-color: #63AB45;
            box-shadow: 0 0 0 3px rgba(99, 171, 69, .15);
            color: #222;
        }

        .alt-link {
            font-size: .85rem;
            margin-top: .9rem;
            color: #444;
        }

        .alt-link a {
            color: #ff6b3d;
            font-weight: 600;
        }

        .alt-link a:hover {
            text-decoration: underline;
        }

        .error-list {
            font-size: .75rem;
        }

        .right-art {
            flex: 1;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .art-wrap {
            max-width: 560px;
            width: 100%;
            position: relative;
        }

        .art-card {
            position: relative;
            border-radius: 34px;
            overflow: hidden;
            box-shadow: 0 28px 55px -20px rgba(0, 0, 0, .35);
        }

        .art-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(.9);
        }

        .art-glow {
            position: absolute;
            inset: 0;
            background: linear-gradient(140deg, rgba(255, 107, 61, .55), rgba(99, 171, 69, .4) 55%, rgba(0, 0, 0, .2));
            mix-blend-mode: overlay;
        }

        .art-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 38px 40px 32px;
            color: #fff;
            backdrop-filter: blur(6px);
            background: linear-gradient(180deg, rgba(0, 0, 0, 0), rgba(0, 0, 0, .75));
        }

        .art-caption h2 {
            font-size: clamp(1.4rem, 2.2vw, 2.1rem);
            margin: 0 0 .5rem;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .art-caption p {
            font-size: .85rem;
            max-width: 420px;
            margin: 0;
            opacity: .92;
        }

        .floating-shape {
            position: absolute;
            animation: floatY 7s ease-in-out infinite;
            z-index: 3;
        }

        .shape-1 {
            top: 8%;
            left: 6%;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle at 30% 30%, #ffb347, #ff6b3d);
            border-radius: 50%;
            filter: blur(2px) saturate(1.2);
            opacity: .85;
        }

        .shape-2 {
            bottom: 10%;
            right: 8%;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle at 30% 30%, #63AB45, #4e8e34);
            border-radius: 50%;
            filter: blur(2px) saturate(1.25);
            opacity: .8;
            animation-delay: -3s;
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-28px)
            }
        }

        @media (max-width:1100px) {
            .right-art {
                display: none
            }

            .auth-left {
                flex: 1;
                padding: 60px 7vw 60px
            }
        }

        @media (max-width:600px) {
            .auth-panel {
                padding: 40px 32px 46px;
            }

            .brand-inline h1 {
                font-size: 1.8rem
            }
        }
    </style>
</head>

<body>
    <div class="auth-shell">
        <div class="auth-left">
            <div class="brand-inline mb-4" data-aos="fade-right" data-aos-duration="800">
                <a href="{{ route('home') }}" class="d-inline-flex align-items-center text-decoration-none">
                    <img src="{{ secure_asset('assets/images/logos/logo.png') }}" alt="TripGo">
                </a>
            </div>
            <div class="auth-panel" data-aos="zoom-in" data-aos-duration="800">
                <h2 class="panel-title">Chào mừng trở lại</h2>
                <div class="panel-sub">Đăng nhập để tiếp tục hành trình khám phá.</div>

                <form method="POST" action="{{ route('login.post') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="login" class="form-label">Email hoặc tên đăng nhập</label>
                        <input type="text" class="form-control @error('login') is-invalid @enderror" id="login"
                            name="login" value="{{ old('login') }}" required autofocus
                            placeholder="nhập email hoặc userName">
                    </div>
                    <div class="mb-2">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required>
                    </div>
                    <div class="auth-actions">
                        <div class="form-check mb-0">
                            <input class="check-input" type="checkbox" value="1" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ</label>
                        </div>
                        <a href="#" class="small text-decoration-none">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="btn-auth">
                        <span>Đăng nhập</span><i class="far fa-sign-in"></i>
                    </button>
                </form>
                <div class="divider-text">Hoặc</div>
                <div class="social-grid">
                    <button type="button" class="social-btn"><i class="fab fa-google"></i> Google</button>
                    <button type="button" class="social-btn"><i class="fab fa-facebook-f"></i> Facebook</button>
                </div>
                <div class="alt-link">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></div>
            </div>
        </div>
        <div class="right-art" data-aos="fade-left" data-aos-duration="1000">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="art-wrap">

            </div>
        </div>
    </div>
    <script src="{{ secure_asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ secure_asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ secure_asset('assets/js/aos.js') }}"></script>
    <script>
        AOS.init({
            once: true,
            duration: 700
        });
    </script>
</body>

</html>
