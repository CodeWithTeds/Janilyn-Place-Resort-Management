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

        // Prepare input data and ensure booking_type is clean
        $input = $request->all();
        if (isset($input['booking_type'])) {
            $type = $input['booking_type'];
            if (is_array($type)) {
                $type = $type[0] ?? null;
            }
            if (is_string($type)) {
                $input['booking_type'] = strtolower(trim($type));
            }
        } elseif (isset($input['exclusive_resort_rental_id'])) {
            $input['booking_type'] = 'exclusive';
        }
        $request->replace($input);

        // Prepare rules
        $rules = [
            'booking_type' => ['required', 'in:room,exclusive'],
            'room_type_id' => ['required_if:booking_type,room', 'nullable', 'exists:room_types,id'],
            'exclusive_resort_rental_id' => ['required_if:booking_type,exclusive', 'nullable', 'exists:exclusive_resort_rentals,id'],
            'resort_unit_id' => ['nullable', 'exists:resort_units,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'pax_count' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:paymongo,cash'],
        ];

        // Conditional validation for pricing_tier_id
        if ($request->input('booking_type') === 'exclusive') {
            $rules['pricing_tier_id'] = ['nullable', 'exists:exclusive_resort_rental_pricing_tiers,id'];
        } else {
            $rules['pricing_tier_id'] = ['nullable', 'exists:room_type_pricing_tiers,id'];
        }

        $validated = $request->validate($rules);

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
