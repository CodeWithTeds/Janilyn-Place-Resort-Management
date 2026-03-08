<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\ResortUnit;
use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;

class Mar8DemoSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();
        $yesterday = $today->copy()->subDay();

        $deluxeType = RoomType::whereRaw('UPPER(category) = ?', ['DELUXE ROOM'])->first() ?? RoomType::first();
        if ($deluxeType && !Booking::where('guest_name', 'Walk-in Mar8 Check-in')->whereDate('check_in', $today)->exists()) {
            Booking::create([
                'guest_name' => 'Walk-in Mar8 Check-in',
                'guest_email' => 'walkin-checkin@example.com',
                'guest_phone' => '0000000000',
                'room_type_id' => $deluxeType->id,
                'check_in' => $today->toDateString(),
                'check_out' => $tomorrow->toDateString(),
                'pax_count' => 2,
                'has_cooking_fee' => false,
                'total_price' => 1000,
                'status' => BookingStatus::CONFIRMED,
                'payment_status' => PaymentStatus::PAID,
            ]);
        }

        $apartmentType = RoomType::whereRaw('UPPER(category) = ?', ['APARTMENT STYLE'])->first();
        $apartmentUnit = $apartmentType ? ResortUnit::where('room_type_id', $apartmentType->id)->first() : null;
        if ($apartmentType && $apartmentUnit && !Booking::where('guest_name', 'Walk-in Mar8 Check-out')->whereDate('check_out', $today)->exists()) {
            Booking::create([
                'guest_name' => 'Walk-in Mar8 Check-out',
                'guest_email' => 'walkin-checkout@example.com',
                'guest_phone' => '0000000000',
                'room_type_id' => $apartmentType->id,
                'resort_unit_id' => $apartmentUnit->id,
                'check_in' => $yesterday->toDateString(),
                'check_out' => $today->toDateString(),
                'pax_count' => 2,
                'has_cooking_fee' => false,
                'total_price' => 1200,
                'status' => BookingStatus::CHECKED_IN,
                'payment_status' => PaymentStatus::PAID,
            ]);
        }
    }
}
