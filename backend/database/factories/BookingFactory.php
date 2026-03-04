<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'guest_name' => $this->faker->name,
            'guest_email' => $this->faker->email,
            'guest_phone' => $this->faker->phoneNumber,
            'room_type_id' => \App\Models\RoomType::factory(),
            'check_in' => now(),
            'check_out' => now()->addDay(),
            'pax_count' => 2,
            'total_price' => 1000,
            'status' => 'pending',
            'notes' => null,
        ];
    }
}
