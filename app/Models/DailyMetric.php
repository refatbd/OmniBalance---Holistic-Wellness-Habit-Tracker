<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'is_fasting', 'sleep_hours', 'mood', 'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'is_fasting' => 'boolean',
        'sleep_hours' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}