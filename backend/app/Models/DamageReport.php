<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamageReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'resort_unit_id',
        'reported_by',
        'item_name',
        'description',
        'severity',
        'status',
        'cost_estimate',
        'images',
        'resolved_at',
    ];

    protected $casts = [
        'cost_estimate' => 'decimal:2',
        'images' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function resortUnit(): BelongsTo
    {
        return $this->belongsTo(ResortUnit::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
