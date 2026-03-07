<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerExclusiveResortRentalController;
use App\Http\Controllers\OwnerGuestRelationshipController;
use App\Http\Controllers\OwnerHousekeepingController;
use App\Http\Controllers\OwnerResortManagementController;
use App\Http\Controllers\OwnerResortUnitController;
use App\Http\Controllers\OwnerRoomTypeController;
use App\Http\Controllers\OwnerStaffManagementController;
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

    Route::middleware(['can:access-admin-dashboard'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/analytics', [App\Http\Controllers\OwnerAnalyticsController::class, 'index'])->name('analytics.index');

        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [App\Http\Controllers\OwnerInventoryController::class, 'index'])->name('index');
        });

        Route::prefix('damage-reports')->name('damage-reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\AdminDamageReportController::class, 'index'])->name('index');
            Route::get('/{damageReport}/edit', [App\Http\Controllers\AdminDamageReportController::class, 'edit'])->name('edit');
            Route::put('/{damageReport}', [App\Http\Controllers\AdminDamageReportController::class, 'update'])->name('update');
        });

        Route::prefix('room-inspections')->name('room-inspections.')->group(function () {
            Route::get('/', [App\Http\Controllers\OwnerRoomInspectionController::class, 'index'])->name('index');
        });

        Route::prefix('housekeeping')->name('housekeeping.')->group(function () {
            Route::get('/', [OwnerHousekeepingController::class, 'index'])->name('index');
            Route::get('/schedules', [OwnerHousekeepingController::class, 'schedules'])->name('schedules');
        });

        Route::prefix('staff-management')->name('staff-management.')->group(function () {
            Route::get('/', [OwnerStaffManagementController::class, 'index'])->name('index');
            Route::get('/schedules', [OwnerStaffManagementController::class, 'schedules'])->name('schedules');
            Route::get('/tasks', [OwnerStaffManagementController::class, 'tasks'])->name('tasks');
            Route::get('/attendance', [OwnerStaffManagementController::class, 'attendance'])->name('attendance');
            Route::get('/performance', [OwnerStaffManagementController::class, 'performance'])->name('performance');
        });

        Route::prefix('resort-management')->name('resort-management.')->group(function () {
            Route::get('/bookings', [OwnerResortManagementController::class, 'bookings'])->name('bookings');
            Route::get('/calendar', [OwnerResortManagementController::class, 'calendar'])->name('calendar');
            Route::get('/check-in-out', [OwnerResortManagementController::class, 'checkInOut'])->name('check-in-out');
            Route::get('/cancellations', [OwnerResortManagementController::class, 'cancellations'])->name('cancellations');
            Route::get('/room-types', [OwnerRoomTypeController::class, 'index'])->name('room-types.index');
            Route::get('/resort-units', [OwnerResortUnitController::class, 'index'])->name('resort-units.index');
            Route::get('/exclusive-resort-rentals', [OwnerExclusiveResortRentalController::class, 'index'])->name('exclusive-resort-rentals.index');
            Route::get('/guest-management', [OwnerGuestRelationshipController::class, 'index'])->name('guest-management.index');
            Route::get('/guest-management/loyalty', [OwnerGuestRelationshipController::class, 'loyalty'])->name('guest-management.loyalty');
        });
    });

    // Owner Dashboard
    Route::get('/owner/dashboard', [DashboardController::class, 'owner'])
        ->middleware('can:access-owner-dashboard')
        ->name('owner.dashboard');

    // Owner Analytics
    Route::get('/owner/analytics', [App\Http\Controllers\OwnerAnalyticsController::class, 'index'])
        ->middleware('can:access-owner-dashboard')
        ->name('owner.analytics.index');

    // Owner Housekeeping
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/housekeeping')->name('owner.housekeeping.')->group(function () {
        Route::get('/', [OwnerHousekeepingController::class, 'index'])->name('index');
        Route::post('/tasks', [OwnerHousekeepingController::class, 'store'])->name('tasks.store');
        Route::put('/tasks/{task}', [OwnerHousekeepingController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [OwnerHousekeepingController::class, 'destroy'])->middleware('can:delete-owner-resources')->name('tasks.destroy');
        Route::patch('/units/{unit}/status', [OwnerHousekeepingController::class, 'updateUnitStatus'])->name('units.status');

        Route::get('/staff', [OwnerHousekeepingController::class, 'staff'])->name('staff');
        Route::get('/schedules', [OwnerHousekeepingController::class, 'schedules'])->name('schedules');
        Route::get('/staff/create', [OwnerHousekeepingController::class, 'createStaff'])->name('staff.create');
        Route::post('/staff', [OwnerHousekeepingController::class, 'storeStaff'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [OwnerHousekeepingController::class, 'editStaff'])->name('staff.edit');
        Route::put('/staff/{staff}', [OwnerHousekeepingController::class, 'updateStaff'])->name('staff.update');
        Route::delete('/staff/{staff}', [OwnerHousekeepingController::class, 'destroyStaff'])->middleware('can:delete-owner-resources')->name('staff.destroy');
    });

    // Owner Resort Management
    Route::middleware(['can:access-resort-management'])->prefix('resort-management')->name('resort-management.')->group(function () {
        Route::get('/bookings', [OwnerResortManagementController::class, 'bookings'])->name('bookings');
        Route::post('/bookings/summary', [OwnerResortManagementController::class, 'summary'])->name('bookings.summary');
        Route::post('/bookings', [OwnerResortManagementController::class, 'storeBooking'])->name('bookings.store');
        Route::get('/bookings/{booking}/receipt', [OwnerResortManagementController::class, 'downloadReceipt'])->name('bookings.receipt');
        Route::get('/bookings/available-rooms', [OwnerResortManagementController::class, 'availableRooms'])->name('bookings.available-rooms');
        Route::get('/bookings/available-units', [OwnerResortManagementController::class, 'availableUnits'])->name('bookings.available-units');
        Route::patch('/bookings/{booking}/approve', [OwnerResortManagementController::class, 'approveBooking'])->name('bookings.approve');
        Route::patch('/bookings/{booking}/cancel', [OwnerResortManagementController::class, 'cancelBooking'])->name('bookings.cancel');

        Route::get('/bookings/payment-success/{booking_id}', [OwnerResortManagementController::class, 'paymentSuccess'])->name('bookings.payment-success')->withoutMiddleware(['auth:sanctum', 'verified', 'can:access-resort-management']);
        Route::get('/bookings/payment-cancel/{booking_id}', [OwnerResortManagementController::class, 'paymentCancel'])->name('bookings.payment-cancel')->withoutMiddleware(['auth:sanctum', 'verified', 'can:access-resort-management']);

        Route::get('/calendar', [OwnerResortManagementController::class, 'calendar'])->name('calendar');
        Route::get('/check-in-out', [OwnerResortManagementController::class, 'checkInOut'])->name('check-in-out');
        Route::patch('/bookings/{booking}/check-in', [OwnerResortManagementController::class, 'checkIn'])->name('bookings.check-in');
        Route::patch('/bookings/{booking}/check-out', [OwnerResortManagementController::class, 'checkOut'])->name('bookings.check-out');
        Route::get('/cancellations', [OwnerResortManagementController::class, 'cancellations'])->name('cancellations');

        // Room Types Management
        Route::get('/room-types', [OwnerRoomTypeController::class, 'index'])->name('room-types.index');
        Route::middleware(['can:access-owner-dashboard'])->group(function () {
            Route::get('/room-types/create', [OwnerRoomTypeController::class, 'create'])->name('room-types.create');
            Route::post('/room-types', [OwnerRoomTypeController::class, 'store'])->name('room-types.store');
            Route::get('/room-types/{room_type}/edit', [OwnerRoomTypeController::class, 'edit'])->name('room-types.edit');
            Route::put('/room-types/{room_type}', [OwnerRoomTypeController::class, 'update'])->name('room-types.update');
            Route::delete('/room-types/{room_type}', [OwnerRoomTypeController::class, 'destroy'])->middleware('can:delete-owner-resources')->name('room-types.destroy');
        });

        // Resort Units Management
        Route::get('/resort-units', [OwnerResortUnitController::class, 'index'])->name('resort-units.index');
        Route::middleware(['can:access-owner-dashboard'])->group(function () {
            Route::get('/resort-units/create', [OwnerResortUnitController::class, 'create'])->name('resort-units.create');
            Route::post('/resort-units', [OwnerResortUnitController::class, 'store'])->name('resort-units.store');
            Route::get('/resort-units/{resort_unit}/edit', [OwnerResortUnitController::class, 'edit'])->name('resort-units.edit');
            Route::put('/resort-units/{resort_unit}', [OwnerResortUnitController::class, 'update'])->name('resort-units.update');
            Route::delete('/resort-units/{resort_unit}', [OwnerResortUnitController::class, 'destroy'])->middleware('can:delete-owner-resources')->name('resort-units.destroy');
        });

        // Exclusive Resort Rentals Management
        Route::get('/exclusive-resort-rentals', [OwnerExclusiveResortRentalController::class, 'index'])->name('exclusive-resort-rentals.index');
        Route::middleware(['can:access-owner-dashboard'])->group(function () {
            Route::get('/exclusive-resort-rentals/create', [OwnerExclusiveResortRentalController::class, 'create'])->name('exclusive-resort-rentals.create');
            Route::post('/exclusive-resort-rentals', [OwnerExclusiveResortRentalController::class, 'store'])->name('exclusive-resort-rentals.store');
            Route::get('/exclusive-resort-rentals/{exclusive_resort_rental}/edit', [OwnerExclusiveResortRentalController::class, 'edit'])->name('exclusive-resort-rentals.edit');
            Route::put('/exclusive-resort-rentals/{exclusive_resort_rental}', [OwnerExclusiveResortRentalController::class, 'update'])->name('exclusive-resort-rentals.update');
            Route::delete('/exclusive-resort-rentals/{exclusive_resort_rental}', [OwnerExclusiveResortRentalController::class, 'destroy'])->middleware('can:delete-owner-resources')->name('exclusive-resort-rentals.destroy');
        });

        // Guest Relationship Management (GRM)
        Route::get('/guest-management', [OwnerGuestRelationshipController::class, 'index'])->name('guest-management.index');
        Route::get('/guest-management/loyalty', [OwnerGuestRelationshipController::class, 'loyalty'])->name('guest-management.loyalty');
    });

    // Owner Inventory
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/inventory')->name('owner.inventory.')->group(function () {
        Route::get('/', [App\Http\Controllers\OwnerInventoryController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\OwnerInventoryController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\OwnerInventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}/edit', [App\Http\Controllers\OwnerInventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [App\Http\Controllers\OwnerInventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [App\Http\Controllers\OwnerInventoryController::class, 'destroy'])->middleware('can:delete-owner-resources')->name('destroy');
    });

    // Owner Damage Reports
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/damage-reports')->name('owner.damage-reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\OwnerDamageReportController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\OwnerDamageReportController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\OwnerDamageReportController::class, 'store'])->name('store');
        Route::get('/{damageReport}/edit', [App\Http\Controllers\OwnerDamageReportController::class, 'edit'])->name('edit');
        Route::put('/{damageReport}', [App\Http\Controllers\OwnerDamageReportController::class, 'update'])->name('update');
        Route::delete('/{damageReport}', [App\Http\Controllers\OwnerDamageReportController::class, 'destroy'])->middleware('can:delete-owner-resources')->name('destroy');
    });

    // Owner Room Inspections
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/room-inspections')->name('owner.room-inspections.')->group(function () {
        Route::get('/', [App\Http\Controllers\OwnerRoomInspectionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\OwnerRoomInspectionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\OwnerRoomInspectionController::class, 'store'])->name('store');
        Route::get('/{roomInspection}/edit', [App\Http\Controllers\OwnerRoomInspectionController::class, 'edit'])->name('edit');
        Route::put('/{roomInspection}', [App\Http\Controllers\OwnerRoomInspectionController::class, 'update'])->name('update');
        Route::delete('/{roomInspection}', [App\Http\Controllers\OwnerRoomInspectionController::class, 'destroy'])->middleware('can:delete-owner-resources')->name('destroy');
    });

    // Owner Staff Management
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/staff-management')->name('owner.staff-management.')->group(function () {
        Route::get('/', [OwnerStaffManagementController::class, 'index'])->name('index');
        Route::get('/create', [OwnerStaffManagementController::class, 'create'])->name('create');
        Route::post('/', [OwnerStaffManagementController::class, 'store'])->name('store');
        Route::get('/schedules', [OwnerStaffManagementController::class, 'schedules'])->name('schedules');
        Route::post('/schedules', [OwnerStaffManagementController::class, 'storeSchedule'])->name('schedules.store');
        Route::get('/attendance', [OwnerStaffManagementController::class, 'attendance'])->name('attendance');
        Route::post('/attendance', [OwnerStaffManagementController::class, 'storeAttendance'])->name('attendance.store');
        Route::get('/performance', [OwnerStaffManagementController::class, 'performance'])->name('performance');
        Route::post('/performance', [OwnerStaffManagementController::class, 'storePerformance'])->name('performance.store');
        Route::get('/tasks', [OwnerStaffManagementController::class, 'tasks'])->name('tasks');
        Route::post('/tasks', [OwnerStaffManagementController::class, 'storeTask'])->name('tasks.store');

        // Specific staff routes (must be after other specific routes to avoid conflict with {staff})
        Route::get('/{staff}/edit', [OwnerStaffManagementController::class, 'edit'])->name('edit');
        Route::put('/{staff}', [OwnerStaffManagementController::class, 'update'])->name('update');
        Route::delete('/{staff}', [OwnerStaffManagementController::class, 'destroy'])->middleware('can:delete-owner-resources')->name('destroy');
    });

    // Staff Dashboard
    Route::get('/staff/dashboard', [DashboardController::class, 'staff'])
        ->middleware('can:access-staff-dashboard')
        ->name('staff.dashboard');

    // Staff Routes
    Route::middleware(['can:access-staff-dashboard'])->prefix('staff')->name('staff.')->group(function () {
        // Guest Management
        Route::resource('guests', App\Http\Controllers\StaffGuestController::class)->except(['show', 'destroy']);

        // Room Allocation
        Route::get('/rooms', [App\Http\Controllers\StaffRoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/{booking}/allocate', [App\Http\Controllers\StaffRoomController::class, 'allocate'])->name('rooms.allocate');
        Route::post('/rooms/{booking}', [App\Http\Controllers\StaffRoomController::class, 'storeAllocation'])->name('rooms.store');

        // Check-in / Check-out
        Route::get('/check-in', [App\Http\Controllers\StaffCheckInController::class, 'index'])->name('check-in.index');
        Route::post('/check-in/{booking}', [App\Http\Controllers\StaffCheckInController::class, 'checkIn'])->name('check-in.store');
        Route::post('/check-out/{booking}', [App\Http\Controllers\StaffCheckInController::class, 'checkOut'])->name('check-out.store');

        // Special Requests
        Route::get('/requests', [App\Http\Controllers\StaffRequestController::class, 'index'])->name('requests.index');
        Route::post('/requests/{booking}', [App\Http\Controllers\StaffRequestController::class, 'store'])->name('requests.store');

        // Damage & Incident Reports
        Route::prefix('damage-reports')->name('damage-reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\StaffDamageReportController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\StaffDamageReportController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\StaffDamageReportController::class, 'store'])->name('store');
        });
    });
});
