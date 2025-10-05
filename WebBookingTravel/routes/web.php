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
use App\Services\AdminValidationService;

// Client routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/tours', [ClientTourController::class, 'index'])->name('client.tours.index');
Route::get('/tours/{id}', [ClientTourController::class, 'show'])->name('client.tours.show');
Route::get('/category/{category}', [ClientTourController::class, 'category'])->name('client.tours.category');
Route::get('/booking', [ClientBookingController::class, 'index'])->middleware(['auth:web'])->name('client.booking');
Route::post('/booking', [ClientBookingController::class, 'store'])->middleware(['auth:web'])->name('client.booking.store');

// Auth (client)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web')->name('logout');

// -------------------------------------

// Admin entry point - redirect tùy theo trạng thái đăng nhập
Route::get('/admin', function () {
    // Debug: Trả về text để xem logic
    if (Auth::guard('admin')->check()) {
        $user = Auth::guard('admin')->user();
        
        // Test xem AdminValidationService có hoạt động không
        try {
            $validation = AdminValidationService::validateAdminUser($user);
        
            if ($validation['valid']) {
                // User hợp lệ -> redirect dashboard
                return redirect('/admin/dashboard');
            } else {
                // User không hợp lệ -> logout và redirect login
                Auth::guard('admin')->logout();
                return redirect('/admin/login')->with('error', $validation['message']);
            }
        } catch (\Exception $e) {
            return 'AdminValidationService error: ' . $e->getMessage();
        }
    }

    // Chưa đăng nhập -> redirect login
    return redirect('/admin/login');
})->name('admin.entry');

// Admin routes khác - khai báo trực tiếp không dùng prefix
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Tours management
Route::get('/admin/tours', [AdminTourController::class, 'index'])->name('admin.tours.index');
Route::get('/admin/tours/create', [AdminTourController::class, 'create'])->name('admin.tours.create');
Route::post('/admin/tours', [AdminTourController::class, 'store'])->name('admin.tours.store');
Route::get('/admin/tours/{id}/edit', [AdminTourController::class, 'edit'])->name('admin.tours.edit');
Route::put('/admin/tours/{id}', [AdminTourController::class, 'update'])->name('admin.tours.update');
Route::delete('/admin/tours/{id}', [AdminTourController::class, 'destroy'])->name('admin.tours.destroy');

// Users management
Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');

// Bookings management
Route::get('/admin/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');

// Không khai báo fallback để Laravel trả về 404 cho đường dẫn không tồn tại