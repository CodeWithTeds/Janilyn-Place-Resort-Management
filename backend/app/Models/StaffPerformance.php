<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPerformance extends Model
{
    use HasFactory;

    protected $table = 'staff_performance';

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'rating',
        'feedback',
        'review_date',
    ];

    protected $casts = [
        'review_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
