<?php

namespace App\Models;

use App\Enums\UnitCleaningStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResortUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'name',
        'status',
        'cleaning_status',
        'notes',
    ];

    protected $casts = [
        'cleaning_status' => UnitCleaningStatus::class,
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function housekeepingTasks(): HasMany
    {
        return $this->hasMany(HousekeepingTask::class);
    }

    public function pricingTiers(): HasMany
    {
        return $this->hasMany(RoomTypePricingTier::class);
    }
}
