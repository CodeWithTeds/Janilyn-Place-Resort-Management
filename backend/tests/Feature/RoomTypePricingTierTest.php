<?php

namespace Tests\Feature;

use App\Models\ResortUnit;
use App\Models\RoomType;
use App\Models\RoomTypePricingTier;
use App\Services\ResortManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTypePricingTierTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_type_can_have_pricing_tiers()
    {
        $roomType = RoomType::create([
            'name' => 'Apartment Style',
            'base_price_weekday' => 1000,
            'base_price_weekend' => 1500,
            'min_pax' => 1,
            'max_pax' => 6,
        ]);

        $tier1 = RoomTypePricingTier::create([
            'room_type_id' => $roomType->id,
            'min_guests' => 1,
            'max_guests' => 2,
            'price_weekday' => 1000,
            'price_weekend' => 1500,
        ]);

        $tier2 = RoomTypePricingTier::create([
            'room_type_id' => $roomType->id,
            'min_guests' => 3,
            'max_guests' => 4,
            'price_weekday' => 1200,
            'price_weekend' => 1800,
        ]);

        $this->assertCount(2, $roomType->pricingTiers);
        $this->assertEquals(1000, $roomType->pricingTiers->first()->price_weekday);
    }

    public function test_price_calculation_uses_tiers()
    {
        $roomType = RoomType::create([
            'name' => 'Apartment Style',
            'base_price_weekday' => 500, // Should be ignored if tier matches
            'base_price_weekend' => 700,
            'min_pax' => 1,
            'max_pax' => 10,
            'extra_person_charge' => 100,
            'cooking_fee' => 0,
        ]);

        // Tier for 3-4 guests
        RoomTypePricingTier::create([
            'room_type_id' => $roomType->id,
            'min_guests' => 3,
            'max_guests' => 4,
            'price_weekday' => 1200,
            'price_weekend' => 1500,
        ]);

        $service = app(ResortManagementService::class);

        // Test with 3 guests (should use tier)
        // 1 night weekday (e.g. Wednesday to Thursday)
        $price = $service->calculateTotalPrice($roomType, '2026-03-04', '2026-03-05', 3);
        $this->assertEquals(1200, $price);

        // Test with 2 guests (should fall back to base price + extra pax logic if base is for 1?)
        // Base price is 500. Min pax is 1. 2 guests = 1 extra pax?
        // Wait, min_pax=1. So 2 guests = 1 extra.
        // Base = 500 + (1 * 100) = 600.
        $priceBase = $service->calculateTotalPrice($roomType, '2026-03-04', '2026-03-05', 2);
        $this->assertEquals(600, $priceBase);
    }

    public function test_unit_specific_pricing_overrides_room_type_pricing()
    {
        $roomType = RoomType::create([
            'name' => 'Deluxe Room',
            'base_price_weekday' => 1000,
            'base_price_weekend' => 1500,
            'min_pax' => 1,
            'max_pax' => 4,
        ]);

        // Room Type Tier
        RoomTypePricingTier::create([
            'room_type_id' => $roomType->id,
            'min_guests' => 1,
            'max_guests' => 2,
            'price_weekday' => 1000,
            'price_weekend' => 1500,
        ]);

        $unit = ResortUnit::create([
            'room_type_id' => $roomType->id,
            'name' => 'Unit 101 (Premium View)',
            'status' => 'available',
        ]);

        // Unit Specific Tier (Higher Price)
        RoomTypePricingTier::create([
            'room_type_id' => $roomType->id,
            'resort_unit_id' => $unit->id,
            'min_guests' => 1,
            'max_guests' => 2,
            'price_weekday' => 2000, // Double the price
            'price_weekend' => 2500,
        ]);

        $service = app(ResortManagementService::class);

        // Calculate price for generic room type (should use room type tier: 1000)
        $genericPrice = $service->calculateTotalPrice($roomType, '2026-03-04', '2026-03-05', 2);
        $this->assertEquals(1000, $genericPrice);

        // Calculate price for specific unit (should use unit tier: 2000)
        $unitPrice = $service->calculateTotalPrice($roomType, '2026-03-04', '2026-03-05', 2, $unit->id);
        $this->assertEquals(2000, $unitPrice);
    }
}
