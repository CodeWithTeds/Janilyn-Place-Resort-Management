<?php

namespace Database\Seeders;

use App\Models\LoyaltyReward;
use Illuminate\Database\Seeder;

class LoyaltyRewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rewards = [
            [
                'name' => 'Free Breakfast Upgrade',
                'description' => 'Enjoy a complimentary premium breakfast upgrade for your stay.',
                'points_required' => 500,
                'is_active' => true,
            ],
            [
                'name' => 'Late Check-out (2PM)',
                'description' => 'Relax a bit longer with a guaranteed late check-out until 2 PM.',
                'points_required' => 800,
                'is_active' => true,
            ],
            [
                'name' => 'Complimentary Massage (1 Hour)',
                'description' => 'Unwind with a 1-hour massage at our spa.',
                'points_required' => 1500,
                'is_active' => true,
            ],
            [
                'name' => 'One Free Night Stay',
                'description' => 'Redeem for a free night in a Deluxe Room (subject to availability).',
                'points_required' => 5000,
                'is_active' => true,
            ],
        ];

        foreach ($rewards as $reward) {
            LoyaltyReward::create($reward);
        }
    }
}
