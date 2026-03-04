<?php

namespace App\Services;

use App\Repositories\RoomTypeRepository;
use App\Repositories\BookingRepository;
use App\Repositories\ExclusiveResortRentalRepository;
use App\Models\RoomType;
use App\Models\ExclusiveResortRental;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Models\ResortUnit;

class ResortManagementService
{
    public function __construct(
        protected RoomTypeRepository $roomTypeRepository,
        protected BookingRepository $bookingRepository,
        protected ExclusiveResortRentalRepository $exclusiveResortRentalRepository,
        protected PaymentService $paymentService
    ) {}

    public function getAllRoomTypes(): Collection
    {
        return $this->roomTypeRepository->getAll();
    }

    public function getAllExclusiveRentals(): Collection
    {
        return $this->exclusiveResortRentalRepository->getAll();
    }

    public function calculateTotalPrice(RoomType $room, string $checkIn, string $checkOut, int $pax, ?int $resortUnitId = null, ?int $manualPricingTierId = null): float
    {
        $start = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);
        $days = $start->diffInDays($end);
        
        if ($days <= 0) {
            return 0;
        }

        // Initialize tier as null
        $tier = null;

        // 0. Check for Manual Pricing Tier ID
        if ($manualPricingTierId) {
            $tier = \App\Models\RoomTypePricingTier::find($manualPricingTierId);
            // Verify it belongs to the room type
            if ($tier && $tier->room_type_id !== $room->id) {
                $tier = null; // Invalid tier for this room type
            }
            // Verify it belongs to the unit if a unit is selected (and tier is unit-specific)
            if ($tier && $resortUnitId && $tier->resort_unit_id && $tier->resort_unit_id !== $resortUnitId) {
                $tier = null; // Invalid tier for this unit
            }
        }

        if (!$tier) {
            // 1. Check for Unit-Specific Pricing Tier if a unit is selected
            if ($resortUnitId) {
                $unit = ResortUnit::with('pricingTiers')->find($resortUnitId);
                if ($unit) {
                    $tier = $unit->pricingTiers->first(function($tier) use ($pax) {
                        return $pax >= $tier->min_guests && $pax <= $tier->max_guests;
                    });
                }
            }

            // 2. If no unit-specific tier found (or no unit selected), check Room Type Pricing Tiers
            if (!$tier) {
                // Filter room type tiers that are NOT bound to a specific unit (global room type tiers)
                // Assuming room type tiers have resort_unit_id as null
                $tier = $room->pricingTiers->first(function($tier) use ($pax) {
                    return $tier->resort_unit_id === null && $pax >= $tier->min_guests && $pax <= $tier->max_guests;
                });

                // 3. Handle Extra Person Logic (Deluxe Room & Guest House only, Max 1 extra)
                if (!$tier && in_array(strtoupper($room->category), ['DELUXE ROOM', 'GUEST HOUSE'])) {
                    // Find the tier with the highest max_guests
                    $maxTier = $room->pricingTiers->where('resort_unit_id', null)->sortByDesc('max_guests')->first();
                    
                    if ($maxTier) {
                        $extraPax = $pax - $maxTier->max_guests;
                        // Allow only 1 extra person
                        if ($extraPax === 1) {
                            $tier = $maxTier;
                        }
                    }
                }
            }
        }

        $totalPrice = 0;
        $currentDate = $start->copy();

        for ($i = 0; $i < $days; $i++) {
            // Check if weekend (Friday, Saturday, Sunday)
            // Note: Requirement says "Weekday Rates (Monday-Thursday)". 
            // So Friday, Saturday, Sunday are Weekend/Standard rates.
            $isWeekend = in_array($currentDate->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]);
            
            if ($tier) {
                // Use tier prices
                if ($isWeekend) {
                    $totalPrice += $tier->price_weekend;
                } else {
                    $totalPrice += $tier->price_weekday;
                }
            } else {
                if ($isWeekend) {
                    $totalPrice += $room->base_price_weekend;
                } else {
                    $totalPrice += $room->base_price_weekday;
                }
            }
            
            $currentDate->addDay();
        }

        // Extra person charge (Applicable if we are using a tier AND pax exceeds that tier's max)
        if ($tier && $pax > $tier->max_guests) {
            $extraPax = $pax - $tier->max_guests;
            $totalPrice += ($extraPax * $room->extra_person_charge * $days);
        }
        // Fallback for old logic (if no tier matched, though tiers are required now)
        elseif (!$tier && $pax > $room->max_pax) {
            $extraPax = $pax - $room->max_pax;
            $totalPrice += ($extraPax * $room->extra_person_charge * $days);
        }

        // Cooking fee
        $totalPrice += $room->cooking_fee;

        return $totalPrice;
    }

    public function calculateExclusiveRentalPrice(ExclusiveResortRental $rental, string $checkIn, string $checkOut, int $pax): float
    {
        $start = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);
        $days = $start->diffInDays($end);

        if ($days <= 0) {
            return 0;
        }

        // Find applicable pricing tier based on pax count
        $tier = $rental->pricingTiers->first(function($tier) use ($pax) {
            return $pax >= $tier->min_guests && $pax <= $tier->max_guests;
        });

        $totalPrice = 0;
        $currentDate = $start->copy();

        for ($i = 0; $i < $days; $i++) {
            $isWeekend = in_array($currentDate->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]);
            
            if ($tier) {
                if ($isWeekend) {
                    $totalPrice += $tier->price_weekend;
                } else {
                    $totalPrice += $tier->price_weekday;
                }
            } else {
                // Fallback if no tier matches (e.g., pax > max tier or pax < min tier)
                // Use base price + extra person charge if applicable
                // For now, let's use base prices from the rental model itself
                if ($isWeekend) {
                    $basePrice = $rental->base_price_weekend;
                } else {
                    $basePrice = $rental->base_price_weekday;
                }

                $totalPrice += $basePrice;

                // Calculate extra person charge if pax exceeds max_pax
                if ($pax > $rental->max_pax) {
                    $extraPax = $pax - $rental->max_pax;
                    $totalPrice += ($extraPax * $rental->extra_person_charge);
                }
            }
            
            $currentDate->addDay();
        }

        // Cooking fee
        $totalPrice += $rental->cooking_fee;

        return $totalPrice;
    }

    public function getAvailableRoomTypes($checkIn, $checkOut): Collection
    {
        // Get all room types with their unit counts
        $roomTypes = RoomType::withCount('units')->get();

        // Get booking counts per room type for the period
        $bookingsCounts = Booking::overlapping($checkIn, $checkOut)
            ->selectRaw('room_type_id, count(*) as count')
            ->groupBy('room_type_id')
            ->pluck('count', 'room_type_id');

        // Filter room types where bookings < units
        // If no units are defined (units_count == 0), we treat it as capacity 1 (legacy/simple mode)
        return $roomTypes->filter(function ($roomType) use ($bookingsCounts) {
            $bookedCount = $bookingsCounts->get($roomType->id, 0);
            $totalUnits = $roomType->units_count;
            $capacity = $totalUnits > 0 ? $totalUnits : 1;
            
            return $bookedCount < $capacity;
        })->values(); // Reset keys
    }

    public function getAvailableUnits($roomTypeId, $checkIn, $checkOut): Collection
    {
        // Get all units for the room type
        $units = ResortUnit::where('room_type_id', $roomTypeId)
            ->where('status', 'available') // Only consider active units
            ->get();

        // Get bookings overlapping the period for this room type
        $occupiedUnitIds = Booking::overlapping($checkIn, $checkOut)
            ->where('room_type_id', $roomTypeId)
            ->whereNotNull('resort_unit_id')
            ->pluck('resort_unit_id')
            ->unique();

        return $units->whereNotIn('id', $occupiedUnitIds)->values();
    }

    public function createWalkInBooking(array $data): Booking
    {
        // 1. Availability Check (Strict)
        if (isset($data['booking_type']) && $data['booking_type'] === 'room') {
            $checkIn = $data['check_in'];
            $checkOut = $data['check_out'];
            $roomTypeId = $data['room_type_id'];
            $resortUnitId = $data['resort_unit_id'] ?? null;

            // Check specific unit availability if selected
            if ($resortUnitId) {
                // If specific unit selected, check if THAT unit is booked
                $isUnitOccupied = Booking::where('resort_unit_id', $resortUnitId)
                    ->where('status', '!=', BookingStatus::CANCELLED->value)
                    ->where(function ($query) use ($checkIn, $checkOut) {
                         $query->where('check_in', '<', $checkOut)
                               ->where('check_out', '>', $checkIn);
                    })
                    ->exists();

                if ($isUnitOccupied) {
                    throw new \Exception('The selected unit is already booked for these dates.');
                }
            } else {
                // Check general room type availability (capacity)
                $roomType = RoomType::withCount('units')->find($roomTypeId);
                $totalUnits = $roomType->units_count;
                
                // Count bookings for this room type in this period
                $overlappingBookingsCount = Booking::where('room_type_id', $roomTypeId)
                    ->where('status', '!=', BookingStatus::CANCELLED->value)
                    ->where(function ($query) use ($checkIn, $checkOut) {
                         $query->where('check_in', '<', $checkOut)
                               ->where('check_out', '>', $checkIn);
                    })
                    ->count();

                if ($overlappingBookingsCount >= $totalUnits) {
                    throw new \Exception('No rooms available for the selected dates.');
                }
            }
        } elseif (isset($data['booking_type']) && $data['booking_type'] === 'exclusive') {
             // Exclusive rental check (assuming single instance or similar logic)
             // ...
        }

        $totalPrice = 0;

        if (isset($data['booking_type']) && $data['booking_type'] === 'exclusive') {
            $rental = $this->exclusiveResortRentalRepository->getAll()->where('id', $data['exclusive_resort_rental_id'])->first(); 
            if (!$rental) {
                 $rental = ExclusiveResortRental::find($data['exclusive_resort_rental_id']);
            }
            
            $totalPrice = $this->calculateExclusiveRentalPrice(
                $rental,
                $data['check_in'],
                $data['check_out'],
                $data['pax_count'] ?? $rental->capacity_overnight_min
            );
        } else {
            $room = $this->roomTypeRepository->find($data['room_type_id']);
            
            $totalPrice = $this->calculateTotalPrice(
                $room, 
                $data['check_in'], 
                $data['check_out'], 
                $data['pax_count'] ?? $room->min_pax,
                $data['resort_unit_id'] ?? null,
                $data['pricing_tier_id'] ?? null
            );
        }

        // Handle Payment
        $paymentStatus = PaymentStatus::UNPAID;
        $paymentId = null;
        $checkoutUrl = null;

        if (isset($data['payment_method'])) {
            if ($data['payment_method'] === PaymentMethod::PAYMONGO->value) {
                // For PayMongo, we create the booking as UNPAID first, then redirect to checkout
                $paymentStatus = PaymentStatus::UNPAID;
            } elseif ($data['payment_method'] === PaymentMethod::CASH->value) {
                $paymentStatus = PaymentStatus::PAID;
            }
        }

        $bookingStatus = ($paymentStatus === PaymentStatus::PAID) ? BookingStatus::CONFIRMED : BookingStatus::PENDING;

        $bookingData = array_merge($data, [
            'total_price' => $totalPrice,
            'status' => $bookingStatus, 
            'payment_status' => $paymentStatus,
            'payment_id' => $paymentId,
            'payment_method' => $data['payment_method'] ?? null,
        ]);

        $booking = $this->bookingRepository->create($bookingData);

        // If PayMongo, generate checkout session now using the booking ID for reference
        if (isset($data['payment_method']) && $data['payment_method'] === PaymentMethod::PAYMONGO->value) {
            try {
                $description = "Booking #{$booking->id} - " . ($data['guest_name'] ?? 'Guest');
                $checkoutSession = $this->paymentService->createCheckoutSession([
                    'amount' => $totalPrice,
                    'description' => $description,
                    'name' => $data['guest_name'],
                    'email' => $data['guest_email'] ?? 'no-email@example.com',
                    'phone' => $data['guest_phone'] ?? '0000000000',
                    'reference_number' => (string) $booking->id,
                    'success_url' => route('resort-management.bookings.payment-success', ['booking_id' => $booking->id]),
                    'cancel_url' => route('resort-management.bookings.payment-cancel', ['booking_id' => $booking->id]),
                ]);
                
                $booking->payment_id = $checkoutSession['id'];
                $booking->save();

                // Attach checkout URL to the booking object for the controller to use
                $booking->checkout_url = $checkoutSession['checkout_url'];
            } catch (\Exception $e) {
                // If checkout creation fails, we might want to fail the booking or just warn?
                // Let's keep the booking but maybe set status to pending payment?
                // For now, let's just log and user can try again later (though UI doesn't support retry yet)
                // Or throw to show error
                throw $e;
            }
        }

        return $booking;
    }

    public function getPendingBookings(): Collection
    {
        return $this->bookingRepository->getByStatus(BookingStatus::PENDING);
    }

    public function getBookings(array $filters = [], int $perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->bookingRepository->getFilteredBookings($filters, $perPage);
    }

    public function getBookingsForMonth($year, $month): Collection
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        return $this->bookingRepository->getBookingsForPeriod($startDate, $endDate);
    }

    public function checkInBooking(Booking $booking, $resortUnitId = null): bool
    {
        if ($booking->status === BookingStatus::CONFIRMED || $booking->status === BookingStatus::PENDING) {
            if ($resortUnitId) {
                $booking->resort_unit_id = $resortUnitId;
                $booking->save();
            }
            return $this->updateBookingStatus($booking, BookingStatus::CHECKED_IN);
        }
        return false;
    }

    public function checkOutBooking(Booking $booking): bool
    {
        if ($booking->status === BookingStatus::CHECKED_IN || $booking->status === BookingStatus::CONFIRMED) {
            return $this->updateBookingStatus($booking, BookingStatus::COMPLETED);
        }
        return false;
    }

    public function getCheckInsToday(): Collection
    {
        return $this->bookingRepository->getCheckInsForDate(Carbon::today());
    }

    public function getCheckOutsToday(): Collection
    {
        return $this->bookingRepository->getCheckOutsForDate(Carbon::today());
    }

    public function getCancellations(): Collection
    {
        return $this->bookingRepository->getCancellations();
    }

    public function updateBookingStatus(Booking $booking, BookingStatus $status): bool
    {
        return $this->bookingRepository->updateStatus($booking->id, $status);
    }
}
