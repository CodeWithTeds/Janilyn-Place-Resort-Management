<?php

use App\Http\Controllers\DashboardController;
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

    // Owner Resort Management
    Route::middleware(['can:access-owner-dashboard'])->prefix('owner/resort-management')->name('owner.resort-management.')->group(function () {
        Route::get('/bookings', [OwnerResortManagementController::class, 'bookings'])->name('bookings');
        Route::get('/calendar', [OwnerResortManagementController::class, 'calendar'])->name('calendar');
        Route::get('/check-in-out', [OwnerResortManagementController::class, 'checkInOut'])->name('check-in-out');
        Route::get('/cancellations', [OwnerResortManagementController::class, 'cancellations'])->name('cancellations');
    });
});
