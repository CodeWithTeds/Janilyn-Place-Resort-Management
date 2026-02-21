<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerResortManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_resort_management_pages()
    {
        $owner = User::factory()->create(['role' => UserRole::OWNER]);

        $this->actingAs($owner);

        $this->get(route('owner.resort-management.bookings'))->assertStatus(200);
        $this->get(route('owner.resort-management.calendar'))->assertStatus(200);
        $this->get(route('owner.resort-management.check-in-out'))->assertStatus(200);
        $this->get(route('owner.resort-management.cancellations'))->assertStatus(200);
    }

    public function test_admin_cannot_access_resort_management_pages()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($admin);

        $this->get(route('owner.resort-management.bookings'))->assertForbidden();
        $this->get(route('owner.resort-management.calendar'))->assertForbidden();
        $this->get(route('owner.resort-management.check-in-out'))->assertForbidden();
        $this->get(route('owner.resort-management.cancellations'))->assertForbidden();
    }

    public function test_staff_cannot_access_resort_management_pages()
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF]);

        $this->actingAs($staff);

        $this->get(route('owner.resort-management.bookings'))->assertForbidden();
    }
}
