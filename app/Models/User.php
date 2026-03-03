<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_suspended',
        'language',
        'timezone',
        'water_reminder_interval', // Added for custom water reminders
        'receive_bedtime_notifications', // Added for bedtime notification preference
        'last_water_reminder_at',   // Added for tracking last notification sent
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'receive_bedtime_notifications' => 'boolean', // Cast to boolean
            'last_water_reminder_at' => 'datetime', // Cast to datetime for easier manipulation
        ];
    }

    // Relationships
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function logs()
    {
        return $this->hasMany(ConsumptionLog::class);
    }

    // Existing Relationships for Water and Weight
    public function waterLogs()
    {
        return $this->hasMany(WaterLog::class);
    }

    public function weightLogs()
    {
        return $this->hasMany(WeightLog::class);
    }

    // --- NEW: Relationships for Habit & Life Tracking ---
    public function prayerLogs()
    {
        return $this->hasMany(PrayerLog::class);
    }

    public function exerciseLogs()
    {
        return $this->hasMany(ExerciseLog::class);
    }

    public function dailyMetrics()
    {
        return $this->hasMany(DailyMetric::class);
    }

    // --- NEW: Relationship for Quick Add / Default Activities ---
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    // Helpers
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}