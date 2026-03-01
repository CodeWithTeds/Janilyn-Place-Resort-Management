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
