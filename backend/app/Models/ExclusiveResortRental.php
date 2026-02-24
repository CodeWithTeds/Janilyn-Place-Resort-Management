<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExclusiveResortRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price_range_min',
        'price_range_max',
        'capacity_overnight_min',
        'capacity_overnight_max',
        'capacity_day_min',
        'capacity_day_max',
        'cooking_fee',
        'features',
        'image_path',
    ];

    protected $casts = [
        'price_range_min' => 'decimal:2',
        'price_range_max' => 'decimal:2',
        'capacity_overnight_min' => 'integer',
        'capacity_overnight_max' => 'integer',
        'capacity_day_min' => 'integer',
        'capacity_day_max' => 'integer',
        'cooking_fee' => 'decimal:2',
        'features' => 'array',
    ];
}
