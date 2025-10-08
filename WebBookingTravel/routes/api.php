<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TourController as ApiTourController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;

Route::prefix('v1')->group(function () {
	Route::get('tours', [ApiTourController::class, 'index']);
	Route::get('tours/destinations', [ApiTourController::class, 'destinations']);
	Route::get('tours/departure-points', [ApiTourController::class, 'departurePoints']);
	Route::get('tours/{id}', [ApiTourController::class, 'show'])->whereNumber('id');
	Route::get('categories', [ApiCategoryController::class, 'index']);
	// Booking API removed
});
