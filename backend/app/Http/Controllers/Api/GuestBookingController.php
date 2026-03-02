<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResortManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\PaymentMethod;

class GuestBookingController extends Controller
{
    public function __construct(
        protected ResortManagementService $resortService
    ) {}

    public function index(Request $request): JsonResponse
    {
        // Get bookings for the authenticated user
        $user = $request->user();        
        $bookings = $user->bookings()->with(['roomType', 'exclusiveResortRental'])->latest()->get();

        return response()->json($bookings);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Validate request
        $validated = $request->validate([
            'booking_type' => ['required', 'in:room,exclusive'],
            'room_type_id' => ['required_if:booking_type,room', 'nullable', 'exists:room_types,id'],
            'exclusive_resort_rental_id' => ['required_if:booking_type,exclusive', 'nullable', 'exists:exclusive_resort_rentals,id'],
            'resort_unit_id' => ['nullable', 'exists:resort_units,id'],
            'pricing_tier_id' => ['nullable', 'exists:room_type_pricing_tiers,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'pax_count' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:paymongo,cash'], // Enforce enum values
        ]);

        // Add user details
        $data = array_merge($validated, [
            'user_id' => $user->id,
            'guest_name' => $user->name,
            'guest_email' => $user->email,
            'guest_phone' => $user->phone_number ?? '0000000000', // Fallback
        ]);

        try {
            $booking = $this->resortService->createWalkInBooking($data); // Reuse logic, handles PayMongo

            return response()->json([
                'message' => 'Booking created successfully',
                'booking' => $booking,
                'checkout_url' => $booking->checkout_url ?? null,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create booking',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
