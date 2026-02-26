<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

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
    Route::post('/check-availability', [App\Http\Controllers\Api\GuestRoomController::class, 'checkAvailability']);

    // Guest Booking Routes
    Route::get('/bookings', [App\Http\Controllers\Api\GuestBookingController::class, 'index']);
    Route::post('/bookings', [App\Http\Controllers\Api\GuestBookingController::class, 'store']);
});
