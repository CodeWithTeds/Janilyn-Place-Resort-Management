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
        RoomType::create($request->validated());

        return redirect()->route('owner.resort-management.room-types.index')
            ->with('success', 'Room Type created successfully.');
    }

    public function edit(RoomType $roomType): View
    {
        return view('owner.resort-management.room-types.edit', compact('roomType'));
    }

    public function update(StoreRoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $roomType->update($request->validated());

        return redirect()->route('owner.resort-management.room-types.index')
            ->with('success', 'Room Type updated successfully.');
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        $roomType->delete();

        return redirect()->route('owner.resort-management.room-types.index')
            ->with('success', 'Room Type deleted successfully.');
    }
}
