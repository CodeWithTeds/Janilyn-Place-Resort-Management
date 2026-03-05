<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\RoomType;
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

        $this->get(route('resort-management.bookings'))->assertStatus(200);
        $this->get(route('resort-management.calendar'))->assertStatus(200);
        $this->get(route('resort-management.check-in-out'))->assertStatus(200);
        $this->get(route('resort-management.cancellations'))->assertStatus(200);
    }

    public function test_admin_can_access_resort_management_pages()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($admin);

        $this->get(route('resort-management.bookings'))->assertStatus(200);
        $this->get(route('resort-management.calendar'))->assertStatus(200);
        $this->get(route('resort-management.check-in-out'))->assertStatus(200);
        $this->get(route('resort-management.cancellations'))->assertStatus(200);
    }

    public function test_staff_can_access_resort_management_pages()
    {
        $staff = User::factory()->create(['role' => UserRole::STAFF]);

        $this->actingAs($staff);

        $this->get(route('resort-management.bookings'))->assertStatus(200);
    }

    public function test_admin_cannot_delete_owner_resource()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $roomType = RoomType::create([
            'name' => 'Deluxe Room',
            'description' => 'Sample room',
            'base_price_weekday' => 1200,
            'base_price_weekend' => 1500,
            'min_pax' => 1,
            'max_pax' => 4,
            'extra_person_charge' => 200,
            'cooking_fee' => 0,
        ]);

        $this->actingAs($admin);

        $this->delete(route('resort-management.room-types.destroy', $roomType))->assertForbidden();
    }
}
