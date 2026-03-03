<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public Routes (or Session-based accessible)
// Room Type Details (For UI dynamic loading)
Route::get('/room-types/{roomType}', [App\Http\Controllers\Api\RoomTypeController::class, 'show']);
Route::get('/exclusive-rentals/{exclusiveResortRental}', [App\Http\Controllers\Api\ExclusiveRentalController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Guest Room Routes
    Route::get('/rooms', [App\Http\Controllers\Api\GuestRoomController::class, 'index']);
    Route::get('/rooms/{id}', [App\Http\Controllers\Api\GuestRoomController::class, 'showRoom']);
    Route::get('/rentals/{id}', [App\Http\Controllers\Api\GuestRoomController::class, 'showRental']);
    Route::get('/available-units', [App\Http\Controllers\Api\GuestRoomController::class, 'getAvailableUnits']);
    Route::post('/check-availability', [App\Http\Controllers\Api\GuestRoomController::class, 'checkAvailability']);

    // Guest Booking Routes
    Route::get('/bookings', [App\Http\Controllers\Api\GuestBookingController::class, 'index']);
    Route::post('/bookings', [App\Http\Controllers\Api\GuestBookingController::class, 'store']);

    // Price Calculation (For Owner/Staff UI and potentially Mobile)
    Route::post('/calculate-price', [App\Http\Controllers\Api\PriceCalculationController::class, 'calculate']);
});
