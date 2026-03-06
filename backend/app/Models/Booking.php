<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

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
        'has_cooking_fee',
        'total_price',
        'status',
        'notes',
        'payment_status',
        'payment_id',
        'payment_method',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'pax_count' => 'integer',
        'has_cooking_fee' => 'boolean',
        'total_price' => 'decimal:2',
        'status' => BookingStatus::class,
        'payment_status' => \App\Enums\PaymentStatus::class,
        'payment_method' => \App\Enums\PaymentMethod::class,
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

    public function feedbacks(): HasMany
    {
        return $this->hasMany(BookingFeedback::class);
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
