<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use App\Services\ResortManagementService;
use App\Enums\BookingStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestLoyaltyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_history_and_loyalty_service()
    {
        // Create a test user with bookings
        $user = User::factory()->create([
            'name' => 'Test Guest',
            'email' => 'testguest@example.com',
            'phone_number' => '1234567890',
            'loyalty_points' => 1000,
            'loyalty_tier' => 'Gold',
        ]);

        // Create bookings for this user
        Booking::factory()->count(6)->create([
            'user_id' => $user->id,
            'guest_name' => $user->name,
            'guest_email' => $user->email,
            'guest_phone' => $user->phone_number,
            'status' => BookingStatus::COMPLETED,
            'total_price' => 1000,
            'check_in' => now(),
            'check_out' => now()->addDay(),
            'room_type_id' => \App\Models\RoomType::factory()->create()->id,
        ]);

        $service = app(ResortManagementService::class);

        // Test Guest History
        $history = $service->getGuestHistory();
        $guest = $history->firstWhere('email', 'testguest@example.com');

        $this->assertNotNull($guest, 'Guest should be found in history');
        $this->assertEquals(6, $guest->total_bookings);
        $this->assertEquals(6000, $guest->total_spend);
        $this->assertEquals('1234567890', $guest->phone);

        // Test Loyalty Program Guests
        $loyaltyGuests = $service->getLoyaltyProgramGuests();
        $lGuest = $loyaltyGuests->firstWhere('email', 'testguest@example.com');

        $this->assertNotNull($lGuest, 'Loyalty guest should be found');
        $this->assertEquals(1000, $lGuest->loyalty_points);
        $this->assertEquals('Gold', $lGuest->loyalty_tier);
    }
}
