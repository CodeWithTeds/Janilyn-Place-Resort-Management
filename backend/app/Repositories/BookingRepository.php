<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class BookingRepository
{
    public function getAll(): Collection
    {
        return Booking::with(['roomType', 'user'])->latest()->get();
    }

    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function updateStatus(int $id, BookingStatus $status): bool
    {
        $booking = Booking::find($id);
        if ($booking) {
            $booking->status = $status;
            return $booking->save();
        }
        return false;
    }

    public function getByStatus(BookingStatus $status): Collection
    {
        return Booking::with(['roomType', 'user'])
            ->where('status', $status)
            ->latest()
            ->get();
    }

    public function getCheckInsForDate(Carbon $date): Collection
    {
        return Booking::with(['roomType', 'user'])
            ->whereDate('check_in', $date)
            ->where('status', BookingStatus::CONFIRMED)
            ->get();
    }

    public function getCheckOutsForDate(Carbon $date): Collection
    {
        return Booking::with(['roomType', 'user'])
            ->whereDate('check_out', $date)
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::COMPLETED]) // Assuming confirmed bookings need to check out
            ->get();
    }

    public function getCancellations(): Collection
    {
        // Assuming cancellations are either status CANCELLED or requested (if we had a requested status)
        // For now, let's just return CANCELLED bookings to show history, 
        // or if there was a "cancellation_requested" status. 
        // The requirement mentions "Cancellation Requests", so maybe we need a REQUESTED_CANCELLATION status.
        // But for simplicity based on current Enums, let's just fetch CANCELLED ones or PENDING ones that might be rejected.
        // Let's stick to returning all for the controller to filter or just cancelled ones.
        return Booking::with(['roomType', 'user'])
            ->where('status', BookingStatus::CANCELLED)
            ->latest()
            ->get();
    }

    public function getFilteredBookings(array $filters = [], int $perPage = 10)
    {
        $query = Booking::with(['roomType', 'user']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('check_in', $filters['date']);
        }

        // Prioritize today's check-ins, then upcoming, then past
        $query->orderByRaw("CASE WHEN DATE(check_in) = CURDATE() THEN 0 WHEN DATE(check_in) > CURDATE() THEN 1 ELSE 2 END, check_in ASC");

        return $query->paginate($perPage);
    }
}
