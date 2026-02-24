<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'resort_unit_id',
        'inspector_id',
        'type',
        'status',
        'checklist_data',
        'notes',
        'images',
    ];

    protected $casts = [
        'checklist_data' => 'array',
        'images' => 'array',
    ];

    public function resortUnit(): BelongsTo
    {
        return $this->belongsTo(ResortUnit::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
