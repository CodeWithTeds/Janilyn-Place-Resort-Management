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
        Route::get('/schedules', [OwnerHousekeepingController::class, 'schedules'])->name('schedules');
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

        Route::get('/bookings/payment-success/{booking_id}', [OwnerResortManagementController::class, 'paymentSuccess'])->name('bookings.payment-success');
        Route::get('/bookings/payment-cancel/{booking_id}', [OwnerResortManagementController::class, 'paymentCancel'])->name('bookings.payment-cancel');

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

    // Owner Inventory
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/inventory')->name('owner.inventory.')->group(function () {
        Route::get('/', [App\Http\Controllers\OwnerInventoryController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\OwnerInventoryController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\OwnerInventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}/edit', [App\Http\Controllers\OwnerInventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [App\Http\Controllers\OwnerInventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [App\Http\Controllers\OwnerInventoryController::class, 'destroy'])->name('destroy');
    });

    // Owner Damage Reports
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/damage-reports')->name('owner.damage-reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\OwnerDamageReportController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\OwnerDamageReportController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\OwnerDamageReportController::class, 'store'])->name('store');
        Route::get('/{damageReport}/edit', [App\Http\Controllers\OwnerDamageReportController::class, 'edit'])->name('edit');
        Route::put('/{damageReport}', [App\Http\Controllers\OwnerDamageReportController::class, 'update'])->name('update');
        Route::delete('/{damageReport}', [App\Http\Controllers\OwnerDamageReportController::class, 'destroy'])->name('destroy');
    });

    // Owner Room Inspections
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/room-inspections')->name('owner.room-inspections.')->group(function () {
        Route::get('/', [App\Http\Controllers\OwnerRoomInspectionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\OwnerRoomInspectionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\OwnerRoomInspectionController::class, 'store'])->name('store');
        Route::get('/{roomInspection}/edit', [App\Http\Controllers\OwnerRoomInspectionController::class, 'edit'])->name('edit');
        Route::put('/{roomInspection}', [App\Http\Controllers\OwnerRoomInspectionController::class, 'update'])->name('update');
        Route::delete('/{roomInspection}', [App\Http\Controllers\OwnerRoomInspectionController::class, 'destroy'])->name('destroy');
    });
});
