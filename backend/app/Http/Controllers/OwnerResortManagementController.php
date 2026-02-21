<?php

namespace App\Http\Controllers;

use App\Services\ResortManagementService;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OwnerResortManagementController extends Controller
{
    public function __construct(
        protected ResortManagementService $resortService
    ) {}

    public function bookings(): View
    {
        $roomTypes = $this->resortService->getAllRoomTypes();
        $pendingBookings = $this->resortService->getPendingBookings();

        return view('owner.resort-management.bookings', compact('roomTypes', 'pendingBookings'));
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

    public function calendar(): View
    {
        // Ideally pass bookings to calendar to show occupancy
        return view('owner.resort-management.calendar');
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
}
