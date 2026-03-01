<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class StaffRequestController extends Controller
{
    /**
     * Display special requests (bookings with notes).
     */
    public function index()
    {
        $bookings = Booking::whereNotNull('notes')
            ->where('notes', '!=', '')
            ->whereIn('status', [\App\Enums\BookingStatus::CONFIRMED, \App\Enums\BookingStatus::CHECKED_IN])
            ->paginate(10);

        return view('staff.requests.index', compact('bookings'));
    }

    /**
     * Store a new request (append to notes).
     */
    public function store(Request $request, Booking $booking)
    {
        $request->validate([
            'request' => 'required|string',
        ]);

        $currentNotes = $booking->notes;
        $newNote = now()->format('Y-m-d H:i') . ': ' . $request->request;
        
        $booking->update([
            'notes' => $currentNotes ? $currentNotes . "\n" . $newNote : $newNote,
        ]);

        return back()->with('success', 'Request recorded successfully.');
    }
}
