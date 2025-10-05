<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin | TripGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body {
            background: #0d2538;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 28px 30px 34px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 10px 35px -5px rgba(0, 0, 0, .35);
            position: relative;
        }

        .brand {
            font-weight: 600;
            font-size: 22px;
            letter-spacing: .5px;
        }

        .small-link {
            font-size: 12px;
        }

        .form-label {
            font-weight: 500;
        }

        .floating-alert {
            position: absolute;
            top: -70px;
            left: 0;
            right: 0;
        }

        .logo-circle {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: #134267;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(19, 66, 103, .4);
        }

        .divider {
            text-align: center;
            position: relative;
            margin: 24px 0 10px;
        }

        .divider span {
            background: #fff;
            padding: 0 10px;
            position: relative;
            z-index: 2;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
            color: #6c7a86;
            letter-spacing: .7px;
        }

        .divider:before {
            content: "";
            position: absolute;
            inset: 50% 0 auto;
            height: 1px;
            background: #e2e6ea;
            z-index: 1;
        }

        .form-check-label {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="text-center mb-3">
            <div class="logo-circle mx-auto">TG</div>
            <div class="brand">TripGo Admin</div>
            <div class="text-muted small mt-1">Đăng nhập bảng điều khiển quản trị</div>
        </div>
        <form method="POST" action="{{ route('admin.login.post') }}" novalidate class="needs-validation">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required
                    autofocus>
                <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
            </div>
            <div class="mb-2">
                <label class="form-label d-flex justify-content-between align-items-center">Mật khẩu
                    <a href="#" class="small-link text-decoration-none">Quên?</a>
                </label>
                <input type="password" name="password" class="form-control" required minlength="3">
                <div class="invalid-feedback">Nhập mật khẩu.</div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check m-0">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                    <label for="remember" class="form-check-label">Ghi nhớ</label>
                </div>
            </div>
            <button class="btn btn-primary w-100 py-2 fw-semibold"><i class="fa fa-right-to-bracket me-1"></i> Đăng
                nhập</button>
        </form>
        <div class="divider"><span>Bảo mật nội bộ</span></div>
        <p class="text-center small text-muted mb-0">&copy; {{ date('Y') }} TripGo. All rights reserved.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const form = document.querySelector('.needs-validation');
            if (form) {
                form.addEventListener('submit', e => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            }
        })();
    </script>
</body>

</html>
