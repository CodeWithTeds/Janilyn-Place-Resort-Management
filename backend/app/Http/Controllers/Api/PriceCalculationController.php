<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResortManagementService;
use App\Models\RoomType;
use App\Models\ExclusiveResortRental;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PriceCalculationController extends Controller
{
    public function __construct(
        protected ResortManagementService $resortService
    ) {}

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:room,exclusive',
            'id' => 'required|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'pax_count' => 'required|integer|min:1',
            'resort_unit_id' => 'nullable|integer',
            'pricing_tier_id' => 'nullable|integer',
        ]);

        try {
            $totalPrice = 0;

            if ($validated['type'] === 'room') {
                $roomType = RoomType::findOrFail($validated['id']);
                $totalPrice = $this->resortService->calculateTotalPrice(
                    $roomType,
                    $validated['check_in'],
                    $validated['check_out'],
                    $validated['pax_count'],
                    $validated['resort_unit_id'] ?? null,
                    $validated['pricing_tier_id'] ?? null
                );
            } else {
                $rental = ExclusiveResortRental::findOrFail($validated['id']);
                $totalPrice = $this->resortService->calculateExclusiveRentalPrice(
                    $rental,
                    $validated['check_in'],
                    $validated['check_out'],
                    $validated['pax_count']
                );
            }

            return response()->json(['total_price' => $totalPrice]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
