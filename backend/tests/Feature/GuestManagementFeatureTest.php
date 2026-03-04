<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected RoomType $room;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->room = RoomType::create([
            'name' => 'Deluxe Room',
            'description' => 'Test Room',
            'base_price_weekday' => 1000,
            'base_price_weekend' => 1200,
            'min_pax' => 1,
            'max_pax' => 2,
            'extra_person_charge' => 0,
            'cooking_fee' => 0,
        ]);
    }

    public function test_owner_can_view_guest_history()
    {
        $owner = User::factory()->create(['role' => UserRole::OWNER]);
        
        // Create 3 bookings for same guest manually since factory might be random
        Booking::create([
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
            'room_type_id' => $this->room->id,
            'check_in' => Carbon::now()->addDays(1),
            'check_out' => Carbon::now()->addDays(2),
            'pax_count' => 1,
            'total_price' => 1000,
            'status' => BookingStatus::COMPLETED,
        ]);
        Booking::create([
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
            'room_type_id' => $this->room->id,
            'check_in' => Carbon::now()->addDays(3),
            'check_out' => Carbon::now()->addDays(4),
            'pax_count' => 1,
            'total_price' => 1000,
            'status' => BookingStatus::COMPLETED,
        ]);
        Booking::create([
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
            'room_type_id' => $this->room->id,
            'check_in' => Carbon::now()->addDays(5),
            'check_out' => Carbon::now()->addDays(6),
            'pax_count' => 1,
            'total_price' => 1000,
            'status' => BookingStatus::COMPLETED,
        ]);

        $response = $this->actingAs($owner)->get(route('resort-management.guest-management.index'));
        
        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('3 Visits');
        // number_format default 2 decimals
        $response->assertSee('3,000.00');
    }

    public function test_loyalty_program_shows_eligible_guests()
    {
        $owner = User::factory()->create(['role' => UserRole::OWNER]);
        
        // Create 5 bookings for Loyal Guest
        for ($i = 0; $i < 5; $i++) {
            Booking::create([
                'guest_name' => 'Loyal Guest',
                'guest_email' => 'loyal@example.com',
                'room_type_id' => $this->room->id,
                'check_in' => Carbon::now()->addDays($i*2 + 1),
                'check_out' => Carbon::now()->addDays($i*2 + 2),
                'pax_count' => 1,
                'total_price' => 1000,
                'status' => BookingStatus::COMPLETED,
            ]);
        }

        // Create 2 bookings for Regular Guest
        for ($i = 0; $i < 2; $i++) {
            Booking::create([
                'guest_name' => 'Regular Guest',
                'guest_email' => 'regular@example.com',
                'room_type_id' => $this->room->id,
                'check_in' => Carbon::now()->addDays($i*2 + 1),
                'check_out' => Carbon::now()->addDays($i*2 + 2),
                'pax_count' => 1,
                'total_price' => 1000,
                'status' => BookingStatus::COMPLETED,
            ]);
        }

        $response = $this->actingAs($owner)->get(route('resort-management.guest-management.loyalty'));
        
        $response->assertStatus(200);
        $response->assertSee('Loyal Guest');
        $response->assertDontSee('Regular Guest');
        $response->assertSee('Gold Member');
    }
}
