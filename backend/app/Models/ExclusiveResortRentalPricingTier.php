<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExclusiveResortRentalPricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'exclusive_resort_rental_id',
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

    public function exclusiveResortRental(): BelongsTo
    {
        return $this->belongsTo(ExclusiveResortRental::class);
    }
}
