<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomTypeRequest;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerRoomTypeController extends Controller
{
    public function index(): View
    {
        $roomTypes = RoomType::latest()->get();
        return view('owner.resort-management.room-types.index', compact('roomTypes'));
    }

    public function create(): View
    {
        return view('owner.resort-management.room-types.create');
    }

    public function store(StoreRoomTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('room-types', 'public');
        }

        // Calculate min/max pax from tiers if not provided
        if (!empty($data['pricing_tiers'])) {
            $tiers = collect($data['pricing_tiers']);
            $data['min_pax'] = $tiers->min('min_guests');
            $data['max_pax'] = $tiers->max('max_guests');
            // Set base prices from the first tier as fallback/default
            $firstTier = $tiers->first();
            $data['base_price_weekday'] = $firstTier['price_weekday'];
            $data['base_price_weekend'] = $firstTier['price_weekend'];
        } else {
            // Fallback defaults if no tiers (should not happen due to validation)
            $data['min_pax'] = $data['min_pax'] ?? 1;
            $data['max_pax'] = $data['max_pax'] ?? 2;
            $data['base_price_weekday'] = $data['base_price_weekday'] ?? 0;
            $data['base_price_weekend'] = $data['base_price_weekend'] ?? 0;
        }
        
        // Ensure optional fees are set to 0 if null
        $data['extra_person_charge'] = $data['extra_person_charge'] ?? 0;
        $data['cooking_fee'] = $data['cooking_fee'] ?? 0;

        $roomType = RoomType::create($data);

        if (!empty($data['pricing_tiers'])) {
            $roomType->pricingTiers()->createMany($data['pricing_tiers']);
        }

        return redirect()->route('resort-management.room-types.index')
            ->with('success', 'Room Type created successfully.');
    }

    public function edit(RoomType $roomType): View
    {
        return view('owner.resort-management.room-types.edit', compact('roomType'));
    }

    public function update(StoreRoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('room-types', 'public');
        }

        // Calculate min/max pax from tiers if not provided
        if (!empty($data['pricing_tiers'])) {
            $tiers = collect($data['pricing_tiers']);
            $data['min_pax'] = $tiers->min('min_guests');
            $data['max_pax'] = $tiers->max('max_guests');
            // Set base prices from the first tier as fallback/default
            $firstTier = $tiers->first();
            $data['base_price_weekday'] = $firstTier['price_weekday'];
            $data['base_price_weekend'] = $firstTier['price_weekend'];
        }
        
        // Ensure optional fees are set to 0 if null
        $data['extra_person_charge'] = $data['extra_person_charge'] ?? 0;
        $data['cooking_fee'] = $data['cooking_fee'] ?? 0;

        $roomType->update($data);

        if ($request->has('pricing_tiers')) {
            $roomType->pricingTiers()->delete();
            if (!empty($data['pricing_tiers'])) {
                $roomType->pricingTiers()->createMany($data['pricing_tiers']);
            }
        }

        return redirect()->route('resort-management.room-types.index')
            ->with('success', 'Room Type updated successfully.');
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        $roomType->delete();

        return redirect()->route('resort-management.room-types.index')
            ->with('success', 'Room Type deleted successfully.');
    }
}
