<?php

namespace App\Http\Controllers;

use App\Models\RoomInspection;
use App\Models\ResortUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerRoomInspectionController extends Controller
{
    public function index()
    {
        $inspections = RoomInspection::with(['resortUnit', 'inspector'])->latest()->paginate(10);
        return view('owner.room-inspections.index', compact('inspections'));
    }

    public function create()
    {
        $units = ResortUnit::orderBy('name')->get();
        return view('owner.room-inspections.create', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resort_unit_id' => 'required|exists:resort_units,id',
            'type' => 'required|in:Check-in,Check-out,Routine,Deep Clean',
            'status' => 'required|in:Passed,Failed,Needs Cleaning',
            'notes' => 'nullable|string',
        ]);

        $validated['inspector_id'] = Auth::id();
        $validated['checklist_data'] = []; // Placeholder for checklist data

        RoomInspection::create($validated);

        return redirect()->route('owner.room-inspections.index')->with('success', 'Room inspection logged successfully.');
    }

    public function edit(RoomInspection $roomInspection)
    {
        $units = ResortUnit::orderBy('name')->get();
        return view('owner.room-inspections.edit', compact('roomInspection', 'units'));
    }

    public function update(Request $request, RoomInspection $roomInspection)
    {
        $validated = $request->validate([
            'resort_unit_id' => 'required|exists:resort_units,id',
            'type' => 'required|in:Check-in,Check-out,Routine,Deep Clean',
            'status' => 'required|in:Passed,Failed,Needs Cleaning',
            'notes' => 'nullable|string',
        ]);

        $roomInspection->update($validated);

        return redirect()->route('owner.room-inspections.index')->with('success', 'Room inspection updated successfully.');
    }

    public function destroy(RoomInspection $roomInspection)
    {
        $roomInspection->delete();

        return redirect()->route('owner.room-inspections.index')->with('success', 'Room inspection deleted successfully.');
    }
}
