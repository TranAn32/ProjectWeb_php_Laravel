<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TourController as ApiTourController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\BookingController as ApiBookingController;

Route::prefix('v1')->group(function () {
	Route::get('tours', [ApiTourController::class, 'index']);
	// Place static endpoints BEFORE the dynamic {id} route to avoid 404 collisions
	Route::get('tours/destinations', [ApiTourController::class, 'destinations']);
	Route::get('tours/departure-points', [ApiTourController::class, 'departurePoints']);
	Route::get('tours/pickup-points', [ApiTourController::class, 'pickupPoints']);
	Route::get('tours/{id}', [ApiTourController::class, 'show'])->whereNumber('id');
	Route::get('categories', [ApiCategoryController::class, 'index']);
	Route::get('bookings', [ApiBookingController::class, 'index']);
	Route::post('bookings', [ApiBookingController::class, 'store']);
});
