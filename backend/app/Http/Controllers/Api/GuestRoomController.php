<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResortManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GuestRoomController extends Controller
{
    public function __construct(
        protected ResortManagementService $resortService
    ) {}

    public function index(): JsonResponse
    {
        $roomTypes = $this->resortService->getAllRoomTypes();
        $exclusiveRentals = $this->resortService->getAllExclusiveRentals();

        // Attach image URLs if not present (assuming models have 'image' attribute or accessor)
        // For now, let's assume standard serialization works.
        // We might need to append full URLs if stored as paths.

        return response()->json([
            'room_types' => $roomTypes,
            'exclusive_rentals' => $exclusiveRentals,
        ]);
    }

    public function showRoom(string $id): JsonResponse
    {
        $room = $this->resortService->getAllRoomTypes()->find($id);

        if (!$room) {
            return response()->json(['message' => 'Room type not found'], 404);
        }

        return response()->json($room);
    }

    public function showRental(string $id): JsonResponse
    {
        $rental = $this->resortService->getAllExclusiveRentals()->find($id);

        if (!$rental) {
            return response()->json(['message' => 'Exclusive rental not found'], 404);
        }

        return response()->json($rental);
    }

    public function getAvailableUnits(Request $request): JsonResponse
    {
        $request->validate([
            'room_type_id' => 'required',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = $request->check_in;
        $checkOut = $request->check_out;

        $units = \App\Models\ResortUnit::where('room_type_id', $request->room_type_id)
            ->whereDoesntHave('bookings', function ($query) use ($checkIn, $checkOut) {
                $query->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<', $checkOut)
                            ->where('check_out', '>', $checkIn);
                    });
            })
            ->get();

        return response()->json($units);
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'type' => 'required|in:room,exclusive',
            'id' => 'required',
        ]);

        if ($request->type === 'room') {
            $availableRooms = $this->resortService->getAvailableRoomTypes(
                $request->check_in,
                $request->check_out
            );
            $isAvailable = $availableRooms->contains('id', $request->id);
        } else {
            $rental = \App\Models\ExclusiveResortRental::find($request->id);
            if (!$rental) {
                return response()->json(['available' => false]);
            }
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;
            $category = strtoupper($rental->category ?? '');

            // Same rental cannot overlap
            $sameRentalOverlap = \App\Models\Booking::where('exclusive_resort_rental_id', $rental->id)
                ->whereIn('status', [\App\Enums\BookingStatus::CONFIRMED->value, \App\Enums\BookingStatus::PENDING->value])
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->where('check_in', '<', $checkOut)
                          ->where('check_out', '>', $checkIn);
                })
                ->exists();
            if ($sameRentalOverlap) {
                return response()->json(['available' => false]);
            }

            if ($category === 'ENTIRE RESORT') {
                $anyOverlap = \App\Models\Booking::whereIn('status', [\App\Enums\BookingStatus::CONFIRMED->value, \App\Enums\BookingStatus::PENDING->value])
                    ->where(function ($query) use ($checkIn, $checkOut) {
                        $query->where('check_in', '<', $checkOut)
                              ->where('check_out', '>', $checkIn);
                    })
                    ->exists();
                $isAvailable = !$anyOverlap;
            } elseif ($category === 'RESORT RENTAL') {
                $apartmentRoomTypeIds = \App\Models\RoomType::whereRaw('UPPER(category) = ?', ['APARTMENT STYLE'])->pluck('id');
                if ($apartmentRoomTypeIds->isNotEmpty()) {
                    $overlapApartments = \App\Models\Booking::whereIn('status', [\App\Enums\BookingStatus::CONFIRMED->value, \App\Enums\BookingStatus::PENDING->value])
                        ->whereIn('room_type_id', $apartmentRoomTypeIds)
                        ->where(function ($query) use ($checkIn, $checkOut) {
                            $query->where('check_in', '<', $checkOut)
                                  ->where('check_out', '>', $checkIn);
                        })
                        ->exists();
                    $isAvailable = !$overlapApartments;
                } else {
                    $isAvailable = true;
                }
            } elseif ($category === 'BAR AREA RENTAL') {
                $units = $this->resortService->getAvailableUnitsForCategory('APARTMENT STYLE', $checkIn, $checkOut);
                $isAvailable = $units->isNotEmpty();
            } else {
                $isAvailable = true;
            }
        }

        return response()->json(['available' => $isAvailable]);
    }

    public function getAvailableApartmentUnits(Request $request): JsonResponse
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $units = $this->resortService->getAvailableUnitsForCategory('APARTMENT STYLE', $request->check_in, $request->check_out);
        return response()->json($units->map(function ($u) {
            return ['id' => $u->id, 'name' => $u->name];
        })->values());
    }
}
