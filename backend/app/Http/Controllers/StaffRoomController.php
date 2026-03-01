<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ResortUnit;
use Illuminate\Http\Request;

class StaffRoomController extends Controller
{
    /**
     * Display bookings needing room allocation.
     */
    public function index()
    {
        // Bookings that are confirmed but not yet checked out, and maybe don't have a unit assigned or need re-assignment
        $bookings = Booking::with(['user', 'roomType', 'resortUnit'])
            ->where('status', 'confirmed')
            ->whereDate('check_in', '>=', now())
            ->orderBy('check_in')
            ->paginate(10);

        return view('staff.rooms.allocation', compact('bookings'));
    }

    /**
     * Show available units for a specific booking.
     */
    public function allocate(Booking $booking)
    {
        // Find units of the requested room type that are available during the booking dates
        // This is a simplified check. A robust system would check for overlapping bookings.
        
        $availableUnits = ResortUnit::where('room_type_id', $booking->room_type_id)
            ->whereDoesntHave('bookings', function ($query) use ($booking) {
                $query->overlapping($booking->check_in, $booking->check_out)
                      ->where('id', '!=', $booking->id); // Exclude current booking if it already has this unit
            })
            ->get();

        return view('staff.rooms.select-unit', compact('booking', 'availableUnits'));
    }

    /**
     * Assign a unit to a booking.
     */
    public function storeAllocation(Request $request, Booking $booking)
    {
        $request->validate([
            'resort_unit_id' => 'required|exists:resort_units,id',
        ]);

        // Verify the unit is valid for the booking (e.g. correct room type) if strictly enforced
        // For upgrades, we might allow different room types, but let's stick to basic allocation first.

        $booking->update([
            'resort_unit_id' => $request->resort_unit_id,
        ]);

        return redirect()->route('staff.rooms.index')->with('success', 'Room allocated successfully.');
    }
}
