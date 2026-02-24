<?php

namespace App\Models;

use App\Enums\HousekeepingPriority;
use App\Enums\HousekeepingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HousekeepingTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'resort_unit_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'status' => HousekeepingStatus::class,
        'priority' => HousekeepingPriority::class,
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function resortUnit(): BelongsTo
    {
        return $this->belongsTo(ResortUnit::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
