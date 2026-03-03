<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'fajr', 'dhuhr', 'asr', 'maghrib', 'isha', 'tahajjud'
    ];

    protected $casts = [
        'date' => 'date',
        'fajr' => 'boolean',
        'dhuhr' => 'boolean',
        'asr' => 'boolean',
        'maghrib' => 'boolean',
        'isha' => 'boolean',
        'tahajjud' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}