<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-dashboard', function (User $user) {
            return $user->isAdmin() || $user->isOwner();
        });

        Gate::define('access-admin-dashboard', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('access-owner-dashboard', function (User $user) {
            return $user->isOwner() || $user->isAdmin();
        });

        Gate::define('access-staff-dashboard', function (User $user) {
            return $user->isStaff();
        });

        Gate::define('access-resort-management', function (User $user) {
            return $user->isOwner() || $user->isAdmin() || $user->isStaff();
        });

        Gate::define('delete-owner-resources', function (User $user) {
            return $user->isOwner();
        });
    }
}
