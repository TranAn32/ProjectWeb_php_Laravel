<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin | TripGo')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome (optional if already loaded in core css) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-olY6gjlJawVSKuEJySLjeRGna43EIBgzHuLlHotE5RX7V6czS4EzFqqpwYBFvToNiOfzmiEJeZVrDCTnhgypnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap Icons (needed for .bi icon classes used in tours index) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!-- Existing global admin css (if any) -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        body {
            background-color: #f5f7fa;
            font-size: 14px;
        }

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            height: 100%;
            width: 240px;
            background: #0d2538;
            color: #fff;
            display: flex;
            flex-direction: column;
            position: fixed;
        }

        .sidebar .brand {
            font-weight: 600;
            font-size: 20px;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar a {
            color: #c9d6df;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 500;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #17354d;
            color: #fff;
        }

        .sidebar .nav-section-title {
            font-size: 11px;
            text-transform: uppercase;
            opacity: .5;
            padding: 14px 18px 6px;
            letter-spacing: .5px;
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Fixed topbar so it does not shift on scroll */
        .topbar {
            height: 58px;
            background: #fff;
            border-bottom: 1px solid #e5e8ec;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: fixed;
            top: 0;
            left: 240px;
            /* align with sidebar width on desktop */
            right: 0;
            z-index: 1060;
        }

        .topbar .page-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .content-wrapper {
            padding: 24px 28px 40px;
            flex: 1;
            margin-top: 58px;
            /* compensate fixed topbar height */
            margin-left: 240px;
            /* align with sidebar width on desktop */
        }

        .footer {
            font-size: 12px;
            padding: 12px 28px;
            color: #6c7a86;
            background: #fff;
            border-top: 1px solid #e5e8ec;
            text-align: center;
        }

        .table thead th {
            background: #f1f3f5;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .status-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 20px;
        }

        .status-published {
            background: #e1f7e9;
            color: #147a2e;
        }

        .status-draft {
            background: #fff4d6;
            color: #9f6600;
        }

        .status-canceled {
            background: #fde2e1;
            color: #a22115;
        }

        .search-bar-inline>* {
            margin-right: 8px;
        }

        .search-bar-inline form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .kpi-card {
            border: 1px solid #e5e8ec;
            border-radius: 10px;
            padding: 16px 18px;
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .kpi-card h6 {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            color: #556270;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }

        .kpi-value {
            font-size: 26px;
            font-weight: 600;
            line-height: 1;
        }

        .kpi-trend {
            font-size: 12px;
            font-weight: 500;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            background: #fff;
            border: 1px dashed #cdd3d9;
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 42px;
            margin-bottom: 12px;
            color: #9aa7b4;
        }

        .breadcrumb {
            margin-bottom: 0;
        }

        .page-header {
            margin-bottom: 24px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .page-header-actions>* {
            margin-left: 8px;
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #17354d;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }

        .dropdown-menu {
            font-size: 14px;
        }

        .btn-sm {
            font-size: 12px;
            padding: .35rem .7rem;
        }

        /* Ensure user dropdown stays at far right */
        .topbar .d-flex.align-items-center.gap-3 {
            margin-left: auto;
        }

        .topbar .dropdown {
            margin-left: auto;
        }

        /* (Removed tour-specific styles; now localized in view) */

        @media (max-width: 991px) {
            .sidebar {
                position: fixed;
                z-index: 1040;
                transform: translateX(-100%);
                transition: .25s;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-toggle {
                display: inline-flex;
            }

            .topbar {
                left: 0;
                /* full width when sidebar is off-canvas */
            }
        }

        @media (min-width: 992px) {
            .sidebar-toggle {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="layout-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="brand d-flex align-items-center justify-content-between">

                <div class="logo-outer">
                    <div class="logo"><a href="{{ route('admin.dashboard') }}"><img
                                src="{{ asset('assets/images/logos/logo.png') }}" alt="Logo"></a></div>
                </div>
            </div>
            <nav class="flex-grow-1 mt-2">
                <div class="nav-section">
                    <div class="nav-section-title">Quản trị</div>
                    <ul class="list-unstyled m-0 p-0">
                        <li><a class="@if (request()->routeIs('admin.dashboard')) active @endif"
                                href="{{ route('admin.dashboard') }}"><i class="fa fa-gauge"></i>
                                <span>Dashboard</span></a></li>
                        <li><a class="@if (request()->routeIs('admin.tours.*')) active @endif"
                                href="{{ route('admin.tours.index') }}"><i class="fa fa-route"></i>
                                <span>Tours</span></a></li>
                        <li><a class="@if (request()->routeIs('admin.bookings.*')) active @endif"
                                href="{{ route('admin.bookings.index') }}"><i class="fa fa-ticket"></i>
                                <span>Bookings</span></a></li>
                    </ul>
                </div>
            </nav>

        </aside>

        <!-- Main -->
        <div class="main">
            <header class="topbar">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm sidebar-toggle" onclick="toggleSidebar()"><i
                            class="fa fa-bars"></i></button>
                    <h1 class="page-title mb-0">@yield('page_title', 'Trang')</h1>
                    @hasSection('breadcrumbs')
                        <nav aria-label="breadcrumb" class="ms-3">
                            <ol class="breadcrumb breadcrumb-sm mb-0">
                                @yield('breadcrumbs')
                            </ol>
                        </nav>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-3">
                    @yield('page_actions')
                    <div class="dropdown">
                        @php($adminUser = Auth::guard('admin')->user())
                        <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <div class="avatar">{{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}</div>
                            <div class="text-start d-none d-sm-block" style="line-height:1;">
                                <div class="fw-semibold">{{ $adminUser->name ?? 'Admin' }}</div>
                                <small class="text-muted" style="font-size:11px;">{{ $adminUser->email ?? '' }}</small>
                            </div>
                            <i class="fa fa-chevron-down small text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li>
                                <h6 class="dropdown-header">Tài khoản</h6>
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="fa fa-user me-2"></i> Hồ sơ</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('admin.logout') }}"
                                    onsubmit="return confirm('Đăng xuất?')">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i
                                            class="fa fa-sign-out-alt me-2"></i>Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            <main class="content-wrapper">
                {{-- Include Toast Notification Component --}}
                @include('components.toast-notification')

                @yield('content')
            </main>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }
    </script>
    @stack('scripts')
</body>

</html>
