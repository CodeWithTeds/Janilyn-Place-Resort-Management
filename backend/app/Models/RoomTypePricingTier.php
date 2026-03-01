<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomTypePricingTier extends Model
{
    protected $fillable = [
        'room_type_id',
        'resort_unit_id',
        'min_guests',
        'max_guests',
        'price_weekday',
        'price_weekend',
    ];

    protected $casts = [
        'min_guests' => 'integer',
        'max_guests' => 'integer',
        'price_weekday' => 'decimal:2',
        'price_weekend' => 'decimal:2',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function resortUnit(): BelongsTo
    {
        return $this->belongsTo(ResortUnit::class);
    }
}
