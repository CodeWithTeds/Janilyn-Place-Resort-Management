<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'image_path',
        'min_pax',
        'max_pax',
        'base_price_weekday',
        'base_price_weekend',
        'extra_person_charge',
        'cooking_fee',
        'bedroom_count',
        'max_day_guests',
        'is_package',
        'amenities',
    ];

    protected $casts = [
        'is_package' => 'boolean',
        'min_pax' => 'integer',
        'max_pax' => 'integer',
        'bedroom_count' => 'integer',
        'max_day_guests' => 'integer',
        'base_price_weekday' => 'decimal:2',
        'base_price_weekend' => 'decimal:2',
        'extra_person_charge' => 'decimal:2',
        'cooking_fee' => 'decimal:2',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ResortUnit::class);
    }
}
