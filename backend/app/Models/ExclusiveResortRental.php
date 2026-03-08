<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExclusiveResortRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'min_pax',
        'max_pax',
        'base_price_weekday',
        'base_price_weekend',
        'extra_person_charge',
        'cooking_fee',
        'features',
        'image_path',
    ];

    protected $casts = [
        'min_pax' => 'integer',
        'max_pax' => 'integer',
        'base_price_weekday' => 'decimal:2',
        'base_price_weekend' => 'decimal:2',
        'extra_person_charge' => 'decimal:2',
        'cooking_fee' => 'decimal:2',
        'features' => 'array',
    ];

    protected $appends = [
        'price_range_min',
        'price_range_max',
        'capacity_overnight_min',
        'capacity_overnight_max',
        'image',
    ];

    public function pricingTiers(): HasMany
    {
        return $this->hasMany(ExclusiveResortRentalPricingTier::class);
    }

    public function getPriceRangeMinAttribute(): ?float
    {
        $tiers = $this->relationLoaded('pricingTiers') ? $this->pricingTiers : $this->pricingTiers()->get();
        if ($tiers->isEmpty()) {
            $baseMin = min((float) $this->base_price_weekday, (float) $this->base_price_weekend);
            return $baseMin ?: null;
        }
        $minWeekday = $tiers->min('price_weekday');
        $minWeekend = $tiers->min('price_weekend');
        return (float) min($minWeekday ?? INF, $minWeekend ?? INF);
    }

    public function getPriceRangeMaxAttribute(): ?float
    {
        $tiers = $this->relationLoaded('pricingTiers') ? $this->pricingTiers : $this->pricingTiers()->get();
        if ($tiers->isEmpty()) {
            $baseMax = max((float) $this->base_price_weekday, (float) $this->base_price_weekend);
            return $baseMax ?: null;
        }
        $maxWeekday = $tiers->max('price_weekday');
        $maxWeekend = $tiers->max('price_weekend');
        return (float) max($maxWeekday ?? 0, $maxWeekend ?? 0);
    }

    public function getCapacityOvernightMinAttribute(): ?int
    {
        $tiers = $this->relationLoaded('pricingTiers') ? $this->pricingTiers : $this->pricingTiers()->get();
        if ($tiers->isEmpty()) {
            return $this->min_pax ?: null;
        }
        return (int) $tiers->min('min_guests');
    }

    public function getCapacityOvernightMaxAttribute(): ?int
    {
        $tiers = $this->relationLoaded('pricingTiers') ? $this->pricingTiers : $this->pricingTiers()->get();
        if ($tiers->isEmpty()) {
            return $this->max_pax ?: null;
        }
        return (int) $tiers->max('max_guests');
    }

    public function getImageAttribute(): ?string
    {
        return $this->image_path;
    }
}
