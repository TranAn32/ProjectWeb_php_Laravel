<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | TripGo Travel Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Login Card */
        .login-card {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(13, 36, 55, 0.08);
            border: 1px solid #e1e8ed;
            padding: 40px 32px;
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #0D2437;
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #64748b;
        }

        /* Form Labels */
        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
            display: block;
        }

        /* Form Inputs */
        .form-input {
            width: 100%;
            padding: 12px 14px;
            font-size: 15px;
            border: 1.5px solid #e1e8ed;
            border-radius: 8px;
            transition: all 0.2s ease;
            background: white;
            font-family: 'Inter', sans-serif;
            margin-bottom: 20px;
        }

        .form-input:focus {
            outline: none;
            border-color: #0D2437;
            box-shadow: 0 0 0 3px rgba(13, 36, 55, 0.06);
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            background: #0D2437;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .submit-btn:hover {
            background: #1a3a52;
        }

        .submit-btn:active {
            transform: scale(0.98);
        }

        /* Alert */
        .alert-box {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            background: #fee;
            border: 1px solid #fcc;
            color: #dc3545;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-close {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            opacity: 0.6;
        }

        .alert-close:hover {
            opacity: 1;
        }

        /* Validation */
        .form-input.is-invalid {
            border-color: #dc3545;
        }

        .error-text {
            display: none;
            font-size: 13px;
            color: #dc3545;
            margin-top: -16px;
            margin-bottom: 16px;
        }

        .form-input.is-invalid+.error-text {
            display: block;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
            }

            .login-title {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    {{-- Include Toast Notification Component --}}
    @include('components.toast-notification')

    <div class="login-card">

        <div class="login-header">
            <h1 class="login-title">Đăng nhập</h1>
            <p class="login-subtitle">Quản lý hệ thống TripGo Travel</p>
        </div>

        <form method="POST" action="{{ route('admin.login.post') }}" novalidate>
            @csrf

            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-input"
                    placeholder="admin@tripgo.com" required autofocus>
                <div class="error-text">Vui lòng nhập email hợp lệ</div>
            </div>


            <div>
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-input" placeholder="Nhập mật khẩu" required
                    minlength="3">
                <div class="error-text">Vui lòng nhập mật khẩu</div>
            </div>


            <button type="submit" class="submit-btn">Đăng nhập</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const form = document.querySelector('form');

            form.addEventListener('submit', function(e) {
                const inputs = form.querySelectorAll('.form-input[required]');
                let isValid = true;

                inputs.forEach(input => {
                    if (!input.value.trim() || !input.checkValidity()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });

            // Remove error on input
            const inputs = form.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim() && this.checkValidity()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Auto dismiss alerts
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-box');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.3s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 8000);
        })();
    </script>
</body>

</html>
