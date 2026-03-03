<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;
use App\Models\Item;
use App\Notifications\DailyReminder;
use App\Notifications\LowStockAlert;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Console Commands
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Automated Scheduler for Water Reminders
|--------------------------------------------------------------------------
*/

Schedule::call(function () {
    // Get all users who have an active push subscription AND a reminder interval set
    $users = User::whereNotNull('water_reminder_interval')
                 ->whereHas('pushSubscriptions')
                 ->get();

    foreach ($users as $user) {
        // Get the current time in the user's specific timezone
        $userTime = Carbon::now($user->timezone);
        
        // Check if it is currently bedtime (10 PM to 8 AM local time)
        $isBedtime = $userTime->hour >= 22 || $userTime->hour < 8;
        
        // If it's bedtime AND the user DOES NOT want bedtime notifications, skip them
        if ($isBedtime && !$user->receive_bedtime_notifications) {
            continue; 
        }

        $lastReminder = $user->last_water_reminder_at ? Carbon::parse($user->last_water_reminder_at) : null;
        $interval = $user->water_reminder_interval;

        // If a reminder has never been sent, OR the interval has passed since the last one
        if (!$lastReminder || $lastReminder->diffInMinutes(now()) >= $interval) {
            
            // Send the push notification
            $user->notify(new DailyReminder());
            
            // Update the timestamp of the last sent notification
            $user->update(['last_water_reminder_at' => now()]);
        }
    }
})->everyMinute();

/*
|--------------------------------------------------------------------------
| Automated Scheduler for Low Stock Alerts
|--------------------------------------------------------------------------
*/

Schedule::call(function () {
    // Find all active items where stock is less than or equal to their threshold
    // We use whereColumn to compare two columns in the database directly
    $items = Item::with('user')
                 ->where('is_active', true)
                 ->whereColumn('stock', '<=', 'low_stock_threshold')
                 ->get();

    foreach ($items as $item) {
        $user = $item->user;

        // Skip if user doesn't exist or doesn't have an active push subscription
        if (!$user || $user->pushSubscriptions()->count() === 0) {
            continue;
        }

        $lastAlert = $item->last_low_stock_alert_at ? Carbon::parse($item->last_low_stock_alert_at) : null;

        // If an alert has never been sent for this low-stock event, OR it has been more than 24 hours since the last alert
        if (!$lastAlert || $lastAlert->diffInHours(now()) >= 24) {
            
            // Send the low stock push notification
            $user->notify(new LowStockAlert($item));
            
            // Update the timestamp so they aren't spammed again for 24 hours
            $item->update(['last_low_stock_alert_at' => now()]);
        }
    }
})->everyMinute();