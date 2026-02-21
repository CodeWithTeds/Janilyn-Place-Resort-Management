<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected RoomType $room;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create Room Types
        $this->room = RoomType::create([
            'name' => 'Deluxe Room',
            'description' => 'A nice room',
            'base_price_weekday' => 1200,
            'base_price_weekend' => 1500,
            'extra_person_charge' => 300,
            'min_pax' => 2,
            'max_pax' => 4,
            'cooking_fee' => 300,
        ]);
    }

    public function test_owner_can_view_bookings_page()
    {
        $owner = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        $response = $this->actingAs($owner)->get(route('owner.resort-management.bookings'));

        $response->assertStatus(200);
        $response->assertViewIs('owner.resort-management.bookings');
        $response->assertViewHas('roomTypes');
        $response->assertViewHas('pendingBookings');
    }

    public function test_owner_can_create_walk_in_booking()
    {
        $owner = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        $checkIn = Carbon::today()->addDays(1)->format('Y-m-d'); // Tomorrow
        $checkOut = Carbon::today()->addDays(3)->format('Y-m-d'); // 2 days later

        $response = $this->actingAs($owner)->post(route('owner.resort-management.bookings.store'), [
            'guest_name' => 'Walk-in Guest',
            'room_type_id' => $this->room->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'pax_count' => 2,
        ]);

        $response->assertRedirect(route('owner.resort-management.bookings'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'guest_name' => 'Walk-in Guest',
            'room_type_id' => $this->room->id,
            'status' => BookingStatus::CONFIRMED->value,
        ]);
    }

    public function test_owner_can_approve_pending_booking()
    {
        $owner = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        $booking = Booking::create([
            'user_id' => null, // Online booking might have user or null depending on implementation
            'guest_name' => 'Online Guest',
            'room_type_id' => $this->room->id,
            'check_in' => Carbon::today()->addDays(5),
            'check_out' => Carbon::today()->addDays(6),
            'pax_count' => 2,
            'total_price' => 1200,
            'status' => BookingStatus::PENDING,
        ]);

        $response = $this->actingAs($owner)->patch(route('owner.resort-management.bookings.approve', $booking));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => BookingStatus::CONFIRMED->value,
        ]);
    }

    public function test_owner_can_cancel_booking()
    {
        $owner = User::factory()->create([
            'role' => UserRole::OWNER,
        ]);

        $booking = Booking::create([
            'guest_name' => 'To Cancel Guest',
            'room_type_id' => $this->room->id,
            'check_in' => Carbon::today()->addDays(10),
            'check_out' => Carbon::today()->addDays(11),
            'pax_count' => 2,
            'total_price' => 1200,
            'status' => BookingStatus::PENDING,
        ]);

        $response = $this->actingAs($owner)->patch(route('owner.resort-management.bookings.cancel', $booking));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => BookingStatus::CANCELLED->value,
        ]);
    }

    public function test_price_calculation_logic()
    {
        // Test Weekend vs Weekday + Extra Pax + Cooking Fee
        // Weekday: 1200, Weekend: 1500, Extra: 300, Cooking: 300
        
        // Scenario 1: 1 Weekday (Monday-Tuesday), 2 Pax (Base)
        // Price = 1200 + 300 (cooking) = 1500
        
        // Let's create a specific service instance test or just run via controller
        // Easier to test via controller/service directly if unit test, but feature test works too.
        
        $owner = User::factory()->create(['role' => UserRole::OWNER]);
        
        // Find a Monday
        $monday = Carbon::parse('next monday');
        $tuesday = $monday->copy()->addDay();
        
        $response = $this->actingAs($owner)->post(route('owner.resort-management.bookings.store'), [
            'guest_name' => 'Weekday Guest',
            'room_type_id' => $this->room->id,
            'check_in' => $monday->format('Y-m-d'),
            'check_out' => $tuesday->format('Y-m-d'),
            'pax_count' => 2,
        ]);

        $booking = Booking::where('guest_name', 'Weekday Guest')->first();
        // 1200 (weekday) + 300 (cooking) = 1500
        $this->assertEquals(1500, $booking->total_price);

        // Scenario 2: 1 Weekend (Saturday-Sunday), 3 Pax (1 Extra)
        // Price = 1500 (weekend) + 300 (extra) + 300 (cooking) = 2100
        
        $saturday = Carbon::parse('next saturday');
        $sunday = $saturday->copy()->addDay();

        $response = $this->actingAs($owner)->post(route('owner.resort-management.bookings.store'), [
            'guest_name' => 'Weekend Guest',
            'room_type_id' => $this->room->id,
            'check_in' => $saturday->format('Y-m-d'),
            'check_out' => $sunday->format('Y-m-d'),
            'pax_count' => 3, // Min pax is 2, so 1 extra
        ]);

        $booking = Booking::where('guest_name', 'Weekend Guest')->first();
        $this->assertEquals(2100, $booking->total_price);
    }
}
