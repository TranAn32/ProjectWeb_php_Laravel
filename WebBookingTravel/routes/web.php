<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\TourController as ClientTourController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Client\AuthController;
use Illuminate\Support\Facades\Auth;

// Client routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/tours', [ClientTourController::class, 'index'])->name('client.tours.index');
Route::get('/tours/{id}', [ClientTourController::class, 'show'])->name('client.tours.show');
Route::get('/category/{category}', [ClientTourController::class, 'category'])->name('client.tours.category');
Route::get('/booking', [ClientBookingController::class, 'index'])->middleware(['auth:web', 'check.user'])->name('client.booking');

// Auth (client)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web')->name('logout');

// (Tùy chọn) Healthcheck chỉ trong local (giữ lại nếu cần monitor)
if (app()->environment('local')) {
    Route::get('/health', fn() => response()->json(['ok' => true, 'time' => now()->toDateTimeString()]));
}

// Các URL client khác nếu không khai báo sẽ trả 404 mặc định

// Admin auth + redirect logic
Route::get('/admin', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
})->name('admin.entry');

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest (chưa đăng nhập admin) - chỉ xét guard admin
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    // Khu vực admin - dùng middleware check.admin để đảm bảo redirect đúng trang admin.login
    Route::middleware(['check.admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/tours', [AdminTourController::class, 'index'])->name('tours.index');
        Route::get('/tours/create', [AdminTourController::class, 'create'])->name('tours.create');
        Route::post('/tours', [AdminTourController::class, 'store'])->name('tours.store');
        Route::get('/tours/{id}/edit', [AdminTourController::class, 'edit'])->name('tours.edit');
        Route::put('/tours/{id}', [AdminTourController::class, 'update'])->name('tours.update');
        Route::delete('/tours/{id}', [AdminTourController::class, 'destroy'])->name('tours.destroy');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        // Chỉ giữ các phần phục vụ 4 bảng: Users, Category, Tour, Booking
    });
});

// Không khai báo fallback để Laravel trả về 404 cho đường dẫn không tồn tại