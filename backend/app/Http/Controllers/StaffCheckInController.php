<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Http\Request;

class StaffCheckInController extends Controller
{
    /**
     * Display today's check-ins and check-outs.
     */
    public function index()
    {
        $today = now()->format('Y-m-d');

        $checkIns = Booking::with(['user', 'resortUnit'])
            ->whereDate('check_in', $today)
            ->where('status', BookingStatus::CONFIRMED)
            ->get();

        $checkOuts = Booking::with(['user', 'resortUnit'])
            ->whereDate('check_out', $today)
            ->where('status', BookingStatus::CHECKED_IN)
            ->get();

        return view('staff.check-in.index', compact('checkIns', 'checkOuts'));
    }

    /**
     * Process check-in for a booking.
     */
    public function checkIn(Booking $booking)
    {
        if ($booking->status !== BookingStatus::CONFIRMED) {
            return back()->with('error', 'Booking is not confirmed or already checked in.');
        }

        // Ensure a room is allocated before check-in
        if (!$booking->resort_unit_id) {
            return back()->with('error', 'Please allocate a room before checking in.');
        }

        $booking->update(['status' => BookingStatus::CHECKED_IN]);

        return back()->with('success', 'Guest checked in successfully.');
    }

    /**
     * Process check-out for a booking.
     */
    public function checkOut(Booking $booking)
    {
        if ($booking->status !== BookingStatus::CHECKED_IN) {
            return back()->with('error', 'Booking is not checked in.');
        }

        $booking->update(['status' => BookingStatus::COMPLETED]);

        // Optionally update unit cleaning status here
        if ($booking->resortUnit) {
            $booking->resortUnit->update(['cleaning_status' => \App\Enums\UnitCleaningStatus::DIRTY]); // Assuming Dirty is the status after use
        }

        return back()->with('success', 'Guest checked out successfully.');
    }
}
