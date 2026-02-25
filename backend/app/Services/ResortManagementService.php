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

    public function calculateTotalPrice(RoomType $room, string $checkIn, string $checkOut, int $pax): float
    {
        $start = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);
        $days = $start->diffInDays($end);
        
        if ($days <= 0) {
            return 0;
        }

        $totalPrice = 0;
        $currentDate = $start->copy();

        for ($i = 0; $i < $days; $i++) {
            // Check if weekend (Friday, Saturday, Sunday)
            // Note: Requirement says "Weekday Rates (Monday-Thursday)". 
            // So Friday, Saturday, Sunday are Weekend/Standard rates.
            $isWeekend = in_array($currentDate->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY]);
            
            if ($isWeekend) {
                $totalPrice += $room->base_price_weekend;
            } else {
                $totalPrice += $room->base_price_weekday;
            }
            
            $currentDate->addDay();
        }

        // Extra person charge
        if ($pax > $room->min_pax) {
            $extraPax = $pax - $room->min_pax;
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

        // For exclusive rental, price is a range in DB (price_range_min, price_range_max).
        // Usually max price is for max pax, min price for min pax.
        // Let's assume a simple logic: if pax <= min_pax, use min_price.
        // If pax >= max_pax, use max_price.
        // If between, interpolate? Or just use max price if pax > min?
        // Let's use max price for now if pax > min_overnight_capacity, else min price.
        // Or simpler: Just take the min price as base for now, as exact logic isn't fully defined.
        // Actually, let's look at the screenshot logic if available or just use max price as safe bet?
        // "P10,000-P12,000/Night"
        // Let's just use the max price for calculation safety, or maybe average?
        // Let's stick to: use min_price if pax <= capacity_overnight_min, else max_price.
        
        $pricePerNight = $rental->price_range_min;
        if ($rental->capacity_overnight_min && $pax > $rental->capacity_overnight_min) {
            $pricePerNight = $rental->price_range_max;
        }

        $totalPrice = $pricePerNight * $days;

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
                $data['pax_count'] ?? $room->min_pax
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

        $bookingData = array_merge($data, [
            'total_price' => $totalPrice,
            'status' => BookingStatus::CONFIRMED, // Walk-ins are usually immediate
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
                    'success_url' => route('owner.resort-management.bookings.payment-success', ['booking_id' => $booking->id]),
                    'cancel_url' => route('owner.resort-management.bookings.payment-cancel', ['booking_id' => $booking->id]),
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

    public function getBookings(array $filters = [], int $perPage = 10)
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
