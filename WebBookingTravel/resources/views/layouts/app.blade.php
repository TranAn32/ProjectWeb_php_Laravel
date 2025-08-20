<!DOCTYPE html>
<html>
<head>
    <title>Travel Booking</title>
    <style>
        body{font-family:Arial, Helvetica, sans-serif;background:#f7f7f9;color:#222}
        .container{width:900px;margin:24px auto;background:#fff;border:1px solid #e8e8ef;border-radius:8px;padding:24px;box-shadow:0 4px 18px rgba(0,0,0,.06)}
        h1{margin-top:0}
        .card{display:flex;flex-direction:column;gap:8px;padding:16px;border:1px solid #eee;border-radius:8px;background:#fafafa}
        label{font-weight:600;margin-top:8px}
        input[type="text"],input[type="number"],select,textarea{width:100%;padding:10px;border:1px solid #d5d5df;border-radius:6px}
        textarea{min-height:100px}
        .btn-primary{background:#2563eb;color:#fff;border:none;padding:10px 14px;border-radius:6px;cursor:pointer}
        .btn-primary:hover{background:#1e40af}
        .alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:10px 12px;border-radius:6px;margin-bottom:12px}
        a{color:#2563eb;text-decoration:none}
        a:hover{text-decoration:underline}
    </style>
</head>
<body>
    <div class="container">
        <h1>travelBooking.com</h1>
        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif
        @yield('content')
    </div>
</body>
</html>
