<?php

namespace App\Services;

use App\Repositories\RoomTypeRepository;
use App\Repositories\BookingRepository;
use App\Models\RoomType;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ResortManagementService
{
    public function __construct(
        protected RoomTypeRepository $roomTypeRepository,
        protected BookingRepository $bookingRepository
    ) {}

    public function getAllRoomTypes(): Collection
    {
        return $this->roomTypeRepository->getAll();
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
        // Assuming base price covers min_pax or max_pax? 
        // Requirement: "Good for 2pax", "Good for 4-10pax". 
        // "300/pax (Additional)".
        // So if pax > max_pax (or some base capacity), charge extra.
        // Let's assume room->max_pax is the limit before extra charge? 
        // Or "Good for 2pax" means base covers 2. 
        // Let's assume room->min_pax is the base capacity covered by price.
        
        if ($pax > $room->min_pax) {
            $extraPax = $pax - $room->min_pax;
            $totalPrice += ($extraPax * $room->extra_person_charge * $days);
        }

        // Cooking fee
        $totalPrice += $room->cooking_fee;

        return $totalPrice;
    }

    public function createWalkInBooking(array $data): Booking
    {
        $room = $this->roomTypeRepository->find($data['room_type_id']);
        
        $totalPrice = $this->calculateTotalPrice(
            $room, 
            $data['check_in'], 
            $data['check_out'], 
            $data['pax_count'] ?? $room->min_pax
        );

        $bookingData = array_merge($data, [
            'total_price' => $totalPrice,
            'status' => BookingStatus::CONFIRMED, // Walk-ins are usually immediate
        ]);

        return $this->bookingRepository->create($bookingData);
    }

    public function getPendingBookings(): Collection
    {
        return $this->bookingRepository->getByStatus(BookingStatus::PENDING);
    }

    public function getBookings(array $filters = [], int $perPage = 10)
    {
        return $this->bookingRepository->getFilteredBookings($filters, $perPage);
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
