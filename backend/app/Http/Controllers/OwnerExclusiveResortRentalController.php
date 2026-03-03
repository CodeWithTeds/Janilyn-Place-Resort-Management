<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExclusiveResortRentalRequest;
use App\Http\Requests\UpdateExclusiveResortRentalRequest;
use App\Models\ExclusiveResortRental;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OwnerExclusiveResortRentalController extends Controller
{
    public function index(): View
    {
        $rentals = ExclusiveResortRental::latest()->get();
        return view('owner.resort-management.exclusive-resort-rentals.index', compact('rentals'));
    }

    public function create(): View
    {
        return view('owner.resort-management.exclusive-resort-rentals.create');
    }

    public function store(StoreExclusiveResortRentalRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('exclusive-rentals', 'public');
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
            $data['min_pax'] = 1;
            $data['max_pax'] = 2;
            $data['base_price_weekday'] = 0;
            $data['base_price_weekend'] = 0;
        }
        
        // Ensure optional fees are set to 0 if null
        $data['extra_person_charge'] = $data['extra_person_charge'] ?? 0;
        $data['cooking_fee'] = $data['cooking_fee'] ?? 0;

        $rental = ExclusiveResortRental::create($data);

        if (!empty($data['pricing_tiers'])) {
            $rental->pricingTiers()->createMany($data['pricing_tiers']);
        }

        return redirect()->route('resort-management.exclusive-resort-rentals.index')
            ->with('success', 'Exclusive Resort Rental created successfully.');
    }

    public function edit(ExclusiveResortRental $exclusiveResortRental): View
    {
        return view('owner.resort-management.exclusive-resort-rentals.edit', [
            'rental' => $exclusiveResortRental
        ]);
    }

    public function update(UpdateExclusiveResortRentalRequest $request, ExclusiveResortRental $exclusiveResortRental): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('exclusive-rentals', 'public');
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

        $exclusiveResortRental->update($data);

        if ($request->has('pricing_tiers')) {
            $exclusiveResortRental->pricingTiers()->delete();
            if (!empty($data['pricing_tiers'])) {
                $exclusiveResortRental->pricingTiers()->createMany($data['pricing_tiers']);
            }
        }

        return redirect()->route('resort-management.exclusive-resort-rentals.index')
            ->with('success', 'Exclusive Resort Rental updated successfully.');
    }

    public function destroy(ExclusiveResortRental $exclusiveResortRental): RedirectResponse
    {
        $exclusiveResortRental->delete();

        return redirect()->route('resort-management.exclusive-resort-rentals.index')
            ->with('success', 'Exclusive Resort Rental deleted successfully.');
    }
}
