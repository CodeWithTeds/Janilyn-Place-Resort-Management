<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;

class Mar8TenCheckoutsSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();

        $roomTypes = RoomType::with('units')->get();
        if ($roomTypes->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $roomType = $roomTypes[($i - 1) % $roomTypes->count()];
            $unitId = optional($roomType->units->first())->id;

            Booking::create([
                'guest_name' => 'Seed Checkout ' . $today->toDateString() . ' #' . $i,
                'guest_email' => 'seed-checkout-' . $i . '@example.com',
                'guest_phone' => '000000000' . (($i % 10)),
                'room_type_id' => $roomType->id,
                'resort_unit_id' => $unitId,
                'check_in' => $yesterday->toDateString(),
                'check_out' => $today->toDateString(),
                'pax_count' => 2,
                'has_cooking_fee' => false,
                'total_price' => 1000 + ($i * 10),
                'status' => BookingStatus::CHECKED_IN,
                'payment_status' => PaymentStatus::PAID,
            ]);
        }
    }
}
