<?php

namespace App\Http\Controllers;

use App\Models\ResortUnit;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OwnerResortUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = ResortUnit::with('roomType')->latest()->paginate(10);
        return view('owner.resort-management.resort-units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roomTypes = RoomType::all();
        return view('owner.resort-management.resort-units.create', compact('roomTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:available,maintenance,occupied',
            'notes' => 'nullable|string',
            'pricing_tiers' => ['nullable', 'array'],
            'pricing_tiers.*.min_guests' => ['required_with:pricing_tiers', 'integer', 'min:1'],
            'pricing_tiers.*.max_guests' => ['required_with:pricing_tiers', 'integer', 'gte:pricing_tiers.*.min_guests'],
            'pricing_tiers.*.price_weekday' => ['required_with:pricing_tiers', 'numeric', 'min:0'],
            'pricing_tiers.*.price_weekend' => ['required_with:pricing_tiers', 'numeric', 'min:0'],
        ]);

        $resortUnit = ResortUnit::create($validated);

        // Handle Pricing Tiers
        if ($request->has('pricing_tiers') && !empty($validated['pricing_tiers'])) {
            // Add room_type_id to each tier (required by schema, even if it's unit-specific)
            // We use the unit's room_type_id
            $tiers = collect($validated['pricing_tiers'])->map(function ($tier) use ($resortUnit) {
                $tier['room_type_id'] = $resortUnit->room_type_id;
                return $tier;
            });
            $resortUnit->pricingTiers()->createMany($tiers);
        }

        return redirect()->route('resort-management.resort-units.index')
            ->with('success', 'Resort unit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResortUnit $resortUnit)
    {
        $roomTypes = RoomType::all();
        return view('owner.resort-management.resort-units.edit', compact('resortUnit', 'roomTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResortUnit $resortUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:available,maintenance,occupied',
            'notes' => 'nullable|string',
            'pricing_tiers' => ['nullable', 'array'],
            'pricing_tiers.*.min_guests' => ['required_with:pricing_tiers', 'integer', 'min:1'],
            'pricing_tiers.*.max_guests' => ['required_with:pricing_tiers', 'integer', 'gte:pricing_tiers.*.min_guests'],
            'pricing_tiers.*.price_weekday' => ['required_with:pricing_tiers', 'numeric', 'min:0'],
            'pricing_tiers.*.price_weekend' => ['required_with:pricing_tiers', 'numeric', 'min:0'],
        ]);

        $resortUnit->update($validated);

        // Handle Pricing Tiers
        if ($request->has('pricing_tiers')) {
            $resortUnit->pricingTiers()->delete();
            if (!empty($validated['pricing_tiers'])) {
                // Add room_type_id to each tier (required by schema, even if it's unit-specific)
                // We use the unit's room_type_id
                $tiers = collect($validated['pricing_tiers'])->map(function ($tier) use ($resortUnit) {
                    $tier['room_type_id'] = $resortUnit->room_type_id;
                    return $tier;
                });
                $resortUnit->pricingTiers()->createMany($tiers);
            }
        }

        return redirect()->route('resort-management.resort-units.index')
            ->with('success', 'Resort unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResortUnit $resortUnit)
    {
        $resortUnit->delete();

        return redirect()->route('resort-management.resort-units.index')
            ->with('success', 'Resort unit deleted successfully.');
    }
}
