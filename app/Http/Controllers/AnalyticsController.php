<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsumptionLog;
use App\Models\WeightLog;
use App\Models\PrayerLog;      // Added for Habit Tracker
use App\Models\ExerciseLog;    // Added for Habit Tracker
use App\Models\DailyMetric;    // Added for Habit Tracker
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Added for PDF Generation

class AnalyticsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $userTimezone = Auth::user()->timezone ?? config('app.timezone');
        
        $totalItemsTracked = ConsumptionLog::where('user_id', $userId)->count();
        $uniqueDaysTracked = ConsumptionLog::where('user_id', $userId)->distinct('consumed_date')->count('consumed_date');
        
        // Streak calculation (same as before)
        $streak = 0;
        $date = Carbon::today($userTimezone);
        while (true) {
            $hasLog = ConsumptionLog::where('user_id', $userId)->whereDate('consumed_date', $date->toDateString())->exists();
            if ($hasLog) {
                $streak++; $date->subDay();
            } else {
                if ($date->isToday($userTimezone)) {
                    $date->subDay();
                    if (ConsumptionLog::where('user_id', $userId)->whereDate('consumed_date', $date->toDateString())->exists()) {
                        $streak++; $date->subDay();
                        continue;
                    }
                }
                break; 
            }
        }

        // --- Calendar Data (Current Month) ---
        $startOfMonth = Carbon::today($userTimezone)->startOfMonth();
        $endOfMonth = Carbon::today($userTimezone)->endOfMonth();
        
        $dailyLogs = ConsumptionLog::where('user_id', $userId)
            ->whereBetween('consumed_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->groupBy(function($log) {
                return Carbon::parse($log->consumed_date)->format('Y-m-d');
            });

        $totalUserItems = Auth::user()->items()->count();
        $calendarData = [];
        
        for ($i = 0; $i < $endOfMonth->day; $i++) {
            $currentDay = $startOfMonth->copy()->addDays($i)->format('Y-m-d');
            $count = isset($dailyLogs[$currentDay]) ? count($dailyLogs[$currentDay]) : 0;
            
            // Status: 0 = none, 1 = partial (yellow), 2 = complete (green)
            $status = 0;
            if ($count > 0) $status = 1;
            if ($totalUserItems > 0 && $count == $totalUserItems) $status = 2;
            
            $calendarData[$currentDay] = $status;
        }

        // --- Weight Trend Data ---
        $weightLogs = WeightLog::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date')
            ->get();

        // --- NEW: Prayer & Exercise Analytics for Current Month ---
        $totalExerciseMinutes = ExerciseLog::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('duration_minutes');

        $prayerLogs = PrayerLog::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get();
            
        $totalPrayersOffered = 0;
        foreach ($prayerLogs as $pLog) {
            $totalPrayersOffered += ($pLog->fajr + $pLog->dhuhr + $pLog->asr + $pLog->maghrib + $pLog->isha + $pLog->tahajjud);
        }

        return view('user.analytics', compact(
            'totalItemsTracked', 'uniqueDaysTracked', 'streak', 
            'calendarData', 'weightLogs', 'startOfMonth', 'endOfMonth',
            'totalExerciseMinutes', 'totalPrayersOffered'
        ));
    }

    // --- Export CSV ---
    public function exportCsv()
    {
        $userId = Auth::id();
        $logs = ConsumptionLog::with('item')
            ->where('user_id', $userId)
            ->orderBy('consumed_date', 'desc')
            ->get();

        $csvFileName = 'nutrition_export_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Wrap the CSV generation inside the callback
        $callback = function() use ($logs) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for proper Excel viewing of Bengali characters
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Date', 'Item Name', 'Category', 'Quantity', 'Calories', 'Protein (g)']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->consumed_date ? $log->consumed_date->format('Y-m-d') : 'N/A',
                    $log->item ? $log->item->name : 'N/A',
                    $log->item ? $log->item->category : 'N/A',
                    $log->quantity,
                    $log->item ? $log->item->calories : 0,
                    $log->item ? $log->item->protein : 0
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // --- Export PDF ---
    public function exportPdf()
    {
        $userId = Auth::id();
        $userTimezone = Auth::user()->timezone ?? config('app.timezone');
        $startOfMonth = Carbon::today($userTimezone)->startOfMonth();
        $endOfMonth = Carbon::today($userTimezone)->endOfMonth();

        $logs = ConsumptionLog::with('item')
            ->where('user_id', $userId)
            ->whereBetween('consumed_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('consumed_date', 'asc')
            ->get();

        $weightLogs = WeightLog::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date', 'asc')
            ->get();

        // --- NEW: Fetching New Habit Logs for PDF ---
        $prayerLogs = PrayerLog::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date', 'asc')
            ->get();

        $exerciseLogs = ExerciseLog::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date', 'asc')
            ->get();

        $dailyMetrics = DailyMetric::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date', 'asc')
            ->get();

        $user = Auth::user();

        // Load the view and pass all the data
        $pdf = Pdf::loadView('exports.monthly_report', compact(
            'logs', 'weightLogs', 'startOfMonth', 'endOfMonth', 'user',
            'prayerLogs', 'exerciseLogs', 'dailyMetrics'
        ));
        
        $fileName = 'nutrition_report_' . $startOfMonth->format('M_Y') . '.pdf';

        return $pdf->download($fileName);
    }
}