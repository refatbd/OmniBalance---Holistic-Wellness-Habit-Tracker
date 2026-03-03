<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'icon', 
        'name', 
        'category', // Added for item categorization
        'instruction', 
        'timing', 
        'stock', 
        'unit',
        'calories', // Added for nutritional tracking
        'protein',  // Added for nutritional tracking
        'carbs',    // Added for nutritional tracking
        'fats',      // Added for nutritional tracking
        'is_active', // Added for enable/disable toggle
        'low_stock_threshold', // Added for low stock alert feature
        'last_low_stock_alert_at', // Added to track alert frequency
    ];

    // Cast columns automatically
    protected $casts = [
        'is_active' => 'boolean',
        'last_low_stock_alert_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(ConsumptionLog::class);
    }
}