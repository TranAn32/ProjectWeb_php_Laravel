<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Admin | TripGo')</title>
    <link rel="stylesheet" href="{{ asset('admin/css/admin-core.css') }}">
    @stack('styles')
</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <div class="left_col">
                <div class="navbar nav_title" style="border:0;">
                    <a href="{{ route('admin.dashboard') }}" class="site_title">TripGo Admin</a>
                </div>
                <div class="clearfix"></div>
                <ul class="nav side-menu">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.tours.index') }}">Tours</a></li>
                    <li><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
                    <li><a href="{{ route('admin.users.index') }}">Users</a></li>
                </ul>
            </div>
            <div class="top_nav">
                <div class="nav_menu">
                    <span>@yield('page_title','')</span>
                </div>
            </div>
            <div class="right_col" role="main">
                @yield('content')
            </div>
        </div>
    </div>
    <script src="{{ asset('admin/js/admin-core.js') }}" defer></script>
    @stack('scripts')
</body>

</html>