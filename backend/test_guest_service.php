<?php

use App\Models\User;
use App\Models\Booking;
use App\Services\ResortManagementService;
use App\Enums\BookingStatus;

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
]);

$service = app(ResortManagementService::class);

echo "Checking Guest History...\n";
$history = $service->getGuestHistory();
$guest = $history->firstWhere('email', 'testguest@example.com');

if ($guest) {
    echo "Found guest: {$guest->name}\n";
    echo "Total Bookings: {$guest->total_bookings}\n";
    echo "Total Spend: {$guest->total_spend}\n";
    echo "Phone: {$guest->phone}\n";
} else {
    echo "Guest not found in history.\n";
}

echo "\nChecking Loyalty Program Guests...\n";
$loyaltyGuests = $service->getLoyaltyProgramGuests();
$lGuest = $loyaltyGuests->firstWhere('email', 'testguest@example.com');

if ($lGuest) {
    echo "Found loyalty guest: {$lGuest->name}\n";
    echo "Points: {$lGuest->loyalty_points}\n";
    echo "Tier: {$lGuest->loyalty_tier}\n";
} else {
    echo "Loyalty guest not found.\n";
}

// Clean up (optional, but good practice in a real test)
// Booking::where('user_id', $user->id)->delete();
// $user->delete();
