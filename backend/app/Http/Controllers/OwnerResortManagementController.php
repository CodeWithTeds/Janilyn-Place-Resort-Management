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

use App\Enums\PaymentStatus;

class OwnerResortManagementController extends Controller
{
    public function __construct(
        protected ResortManagementService $resortService
    ) {}

    public function bookings(Request $request): View
    {
        $roomTypes = $this->resortService->getAllRoomTypes();
        $exclusiveRentals = $this->resortService->getAllExclusiveRentals();
        
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'date' => $request->input('date'),
        ];

        $bookings = $this->resortService->getBookings($filters);

        return view('owner.resort-management.bookings', compact('roomTypes', 'exclusiveRentals', 'bookings'));
    }

    public function storeBooking(StoreBookingRequest $request): RedirectResponse
    {
        $booking = $this->resortService->createWalkInBooking($request->validated());

        if (isset($booking->checkout_url)) {
            return redirect($booking->checkout_url);
        }

        return redirect()->route('resort-management.bookings')
            ->with('success', 'Walk-in booking created successfully.');
    }

    public function paymentSuccess(Request $request, $booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        
        // In a real scenario, we verify the session status from PayMongo API here
        // or rely on webhooks. For simplicity in this demo, we mark as PAID.
        // Ideally: $status = $this->paymentService->getCheckoutSession($booking->payment_id)['status'];
        
        $booking->payment_status = PaymentStatus::PAID;
        $booking->save();

        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if (\Illuminate\Support\Facades\Auth::check() && $user->can('access-resort-management')) {
             return redirect()->route('resort-management.bookings')
                ->with('success', 'Payment successful! Booking confirmed.');
        }

        return view('payment.success', compact('booking'));
    }

    public function paymentCancel(Request $request, $booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        
        // Mark as failed or just leave as unpaid?
        // Usually if user cancels, it's just unpaid.
        // We can delete the booking if it was just created for this session?
        // Or keep it as "Pending Payment".
        // Let's keep it but notify user.
        
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if (\Illuminate\Support\Facades\Auth::check() && $user->can('access-resort-management')) {
            return redirect()->route('resort-management.bookings')
                ->with('error', 'Payment was cancelled. Booking is still unpaid.');
        }

        return view('payment.cancel', compact('booking'));
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

    public function checkIn(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'resort_unit_id' => 'nullable|exists:resort_units,id',
        ]);

        if ($this->resortService->checkInBooking($booking, $request->resort_unit_id)) {
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

    public function availableUnits(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $units = $this->resortService->getAvailableUnits(
            $request->room_type_id,
            $request->check_in,
            $request->check_out
        );

        return response()->json($units);
    }
}
