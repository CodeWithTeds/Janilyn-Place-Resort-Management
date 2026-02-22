<?php

namespace App\Http\Controllers;

use App\Services\ResortManagementService;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OwnerResortManagementController extends Controller
{
    public function __construct(
        protected ResortManagementService $resortService
    ) {}

    public function bookings(Request $request): View
    {
        $roomTypes = $this->resortService->getAllRoomTypes();
        
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'date' => $request->input('date'),
        ];

        $bookings = $this->resortService->getBookings($filters);

        return view('owner.resort-management.bookings', compact('roomTypes', 'bookings'));
    }

    public function storeBooking(StoreBookingRequest $request): RedirectResponse
    {
        $this->resortService->createWalkInBooking($request->validated());

        return redirect()->route('owner.resort-management.bookings')
            ->with('success', 'Walk-in booking created successfully.');
    }

    public function approveBooking(Booking $booking): RedirectResponse
    {
        $this->resortService->updateBookingStatus($booking, BookingStatus::CONFIRMED);

        return redirect()->back()
            ->with('success', 'Booking approved successfully.');
    }

    public function cancelBooking(Booking $booking): RedirectResponse
    {
        $this->resortService->updateBookingStatus($booking, BookingStatus::CANCELLED);

        return redirect()->back()
            ->with('success', 'Booking cancelled successfully.');
    }

    public function calendar(Request $request): View
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $bookings = $this->resortService->getBookingsForMonth($year, $month);
        $roomTypes = $this->resortService->getAllRoomTypes();

        $currentDate = Carbon::createFromDate($year, $month, 1);
        
        return view('owner.resort-management.calendar', compact('bookings', 'roomTypes', 'currentDate'));
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        if ($this->resortService->checkInBooking($booking)) {
            return redirect()->back()->with('success', 'Guest checked in successfully.');
        }
        return redirect()->back()->with('error', 'Unable to check in guest.');
    }

    public function checkOut(Booking $booking): RedirectResponse
    {
        if ($this->resortService->checkOutBooking($booking)) {
            return redirect()->back()->with('success', 'Guest checked out successfully.');
        }
        return redirect()->back()->with('error', 'Unable to check out guest.');
    }

    public function checkInOut(): View
    {
        $checkIns = $this->resortService->getCheckInsToday();
        $checkOuts = $this->resortService->getCheckOutsToday();

        return view('owner.resort-management.check-in-out', compact('checkIns', 'checkOuts'));
    }

    public function cancellations(): View
    {
        $cancellations = $this->resortService->getCancellations();

        return view('owner.resort-management.cancellations', compact('cancellations'));
    }

    public function availableRooms(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $rooms = $this->resortService->getAvailableRoomTypes(
            $request->check_in,
            $request->check_out
        );

        return response()->json($rooms);
    }
}
