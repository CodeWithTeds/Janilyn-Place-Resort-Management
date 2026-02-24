<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestAccommodationController;
use App\Http\Controllers\OwnerExclusiveResortRentalController;
use App\Http\Controllers\OwnerHousekeepingController;
use App\Http\Controllers\OwnerResortManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Restricted Dashboard (Admin & Owner)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('can:access-dashboard')
        ->name('dashboard');

    // Admin Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('can:access-admin-dashboard')
        ->name('admin.dashboard');

    // Owner Dashboard
    Route::get('/owner/dashboard', [DashboardController::class, 'owner'])
        ->middleware('can:access-owner-dashboard')
        ->name('owner.dashboard');

    // Owner Housekeeping
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/housekeeping')->name('owner.housekeeping.')->group(function () {
        Route::get('/', [OwnerHousekeepingController::class, 'index'])->name('index');
        Route::post('/tasks', [OwnerHousekeepingController::class, 'store'])->name('tasks.store');
        Route::put('/tasks/{task}', [OwnerHousekeepingController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [OwnerHousekeepingController::class, 'destroy'])->name('tasks.destroy');
        Route::patch('/units/{unit}/status', [OwnerHousekeepingController::class, 'updateUnitStatus'])->name('units.status');
        
        Route::get('/staff', [OwnerHousekeepingController::class, 'staff'])->name('staff');
        Route::get('/staff/create', [OwnerHousekeepingController::class, 'createStaff'])->name('staff.create');
        Route::post('/staff', [OwnerHousekeepingController::class, 'storeStaff'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [OwnerHousekeepingController::class, 'editStaff'])->name('staff.edit');
        Route::put('/staff/{staff}', [OwnerHousekeepingController::class, 'updateStaff'])->name('staff.update');
        Route::delete('/staff/{staff}', [OwnerHousekeepingController::class, 'destroyStaff'])->name('staff.destroy');
    });

    // Owner Resort Management
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/resort-management')->name('owner.resort-management.')->group(function () {
        Route::get('/bookings', [OwnerResortManagementController::class, 'bookings'])->name('bookings');
        Route::post('/bookings', [OwnerResortManagementController::class, 'storeBooking'])->name('bookings.store');
        Route::get('/bookings/available-rooms', [OwnerResortManagementController::class, 'availableRooms'])->name('bookings.available-rooms');
        Route::get('/bookings/available-units', [OwnerResortManagementController::class, 'availableUnits'])->name('bookings.available-units');
        Route::patch('/bookings/{booking}/approve', [OwnerResortManagementController::class, 'approveBooking'])->name('bookings.approve');
        Route::patch('/bookings/{booking}/cancel', [OwnerResortManagementController::class, 'cancelBooking'])->name('bookings.cancel');

        Route::get('/calendar', [OwnerResortManagementController::class, 'calendar'])->name('calendar');
        Route::get('/check-in-out', [OwnerResortManagementController::class, 'checkInOut'])->name('check-in-out');
        Route::patch('/bookings/{booking}/check-in', [OwnerResortManagementController::class, 'checkIn'])->name('bookings.check-in');
        Route::patch('/bookings/{booking}/check-out', [OwnerResortManagementController::class, 'checkOut'])->name('bookings.check-out');
        Route::get('/cancellations', [OwnerResortManagementController::class, 'cancellations'])->name('cancellations');

        // Room Types Management
        Route::resource('room-types', App\Http\Controllers\OwnerRoomTypeController::class);

        // Resort Units Management
        Route::resource('resort-units', App\Http\Controllers\OwnerResortUnitController::class);

        // Exclusive Resort Rentals Management
        Route::resource('exclusive-resort-rentals', App\Http\Controllers\OwnerExclusiveResortRentalController::class);
    });
});
