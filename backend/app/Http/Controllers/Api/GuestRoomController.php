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
         
            $isAvailable = true; // Placeholder for exclusive availability logic
        }

        return response()->json(['available' => $isAvailable]);
    }
}
