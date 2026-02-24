<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'room_type_id',
        'exclusive_resort_rental_id',
        'resort_unit_id',
        'check_in',
        'check_out',
        'pax_count',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'pax_count' => 'integer',
        'total_price' => 'decimal:2',
        'status' => BookingStatus::class,
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function exclusiveResortRental(): BelongsTo
    {
        return $this->belongsTo(ExclusiveResortRental::class);
    }

    public function resortUnit(): BelongsTo
    {
        return $this->belongsTo(ResortUnit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOverlapping($query, $checkIn, $checkOut)
    {
        return $query->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::PENDING])
                     ->where(function ($q) use ($checkIn, $checkOut) {
                         $q->where('check_in', '<', $checkOut)
                           ->where('check_out', '>', $checkIn);
                     });
    }
}
