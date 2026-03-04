<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'base_price_weekday' => 1000,
            'base_price_weekend' => 1500,
            'max_pax' => 4,
            'min_pax' => 1,
            'extra_person_charge' => 500,
            'cooking_fee' => 0,
            'category' => 'Standard',
            'image_path' => null,
            'amenities' => ['WiFi'],
        ];
    }
}
