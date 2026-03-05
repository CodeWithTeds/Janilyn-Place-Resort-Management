<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOwnerModulesAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_all_owner_sidebar_pages(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($admin);

        $routes = [
            'admin.analytics.index',
            'admin.inventory.index',
            'admin.damage-reports.index',
            'admin.room-inspections.index',
            'admin.housekeeping.index',
            'admin.housekeeping.schedules',
            'admin.staff-management.index',
            'admin.staff-management.schedules',
            'admin.staff-management.tasks',
            'admin.staff-management.attendance',
            'admin.staff-management.performance',
            'admin.resort-management.bookings',
            'admin.resort-management.calendar',
            'admin.resort-management.check-in-out',
            'admin.resort-management.cancellations',
            'admin.resort-management.guest-management.index',
            'admin.resort-management.guest-management.loyalty',
            'admin.resort-management.room-types.index',
            'admin.resort-management.resort-units.index',
            'admin.resort-management.exclusive-resort-rentals.index',
        ];

        foreach ($routes as $route) {
            $this->get(route($route))->assertStatus(200);
        }
    }
}
