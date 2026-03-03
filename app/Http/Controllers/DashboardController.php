<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ConsumptionLog;
use App\Models\WaterLog;
use App\Models\WeightLog;
use App\Models\PrayerLog;       // NEW
use App\Models\ExerciseLog;     // NEW
use App\Models\DailyMetric;     // NEW
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Use the user's preferred timezone for "Today"
        $userTimezone = Auth::user()->timezone ?? config('app.timezone');
        $dateString = $request->query('date', Carbon::today($userTimezone)->toDateString());
        $currentDate = Carbon::parse($dateString, $userTimezone);

        // Fetch ONLY the authenticated user's ACTIVE items
        $items = Auth::user()->items()->where('is_active', true)->get();

        $logs = ConsumptionLog::where('user_id', Auth::id())
            ->whereDate('consumed_date', $currentDate->toDateString())
            ->pluck('item_id')
            ->toArray();

        // Calculate progress for the dynamic bar
        $totalItems = $items->count();
        
        // Count only the logs that belong to currently active items
        $completedItems = 0;
        foreach ($logs as $logItemId) {
            if ($items->contains('id', $logItemId)) {
                $completedItems++;
            }
        }

        // --- NEW: Macro Calculation ---
        $macros = ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fats' => 0];
        foreach ($items as $item) {
            if (in_array($item->id, $logs)) {
                // We assume 1 qty for calculation simplicity here
                $macros['calories'] += $item->calories;
                $macros['protein'] += $item->protein;
                $macros['carbs'] += $item->carbs;
                $macros['fats'] += $item->fats;
            }
        }

        // --- NEW: Fetch Water and Weight for the selected date ---
        $waterLog = WaterLog::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => $currentDate->toDateString()],
            ['glasses' => 0]
        );
        $weightLog = WeightLog::where('user_id', Auth::id())->whereDate('date', $currentDate->toDateString())->first();

        // --- NEW: Fetch Prayers, Exercises, and Metrics ---
        $prayerLog = PrayerLog::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => $currentDate->toDateString()]
        );
        
        $exerciseLogs = ExerciseLog::where('user_id', Auth::id())
            ->whereDate('date', $currentDate->toDateString())
            ->get();
            
        $dailyMetric = DailyMetric::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => $currentDate->toDateString()]
        );

        // --- NEW: Fetch User's Saved Quick-Add Activities ---
        $savedActivities = Auth::user()->activities()->get();

        // --- NEW: Group items by category for the view ---
        $groupedItems = $items->groupBy('category');

        return view('dashboard.index', [
            'groupedItems' => $groupedItems, // Passing grouped items to the view
            'items' => $items, // Keeping original collection just in case
            'logs' => $logs,
            'currentDate' => $currentDate,
            'prevDate' => $currentDate->copy()->subDay()->toDateString(),
            'nextDate' => $currentDate->copy()->addDay()->toDateString(),
            'totalItems' => $totalItems,
            'completedItems' => $completedItems,
            'macros' => $macros,
            'waterGlasses' => $waterLog->glasses,
            'weight' => $weightLog ? $weightLog->weight : null,
            'prayerLog' => $prayerLog,
            'exerciseLogs' => $exerciseLogs,
            'dailyMetric' => $dailyMetric,
            'savedActivities' => $savedActivities, // PASSED TO VIEW HERE
        ]);
    }

    public function toggleLog(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'date' => 'required|date|before_or_equal:today', // Prevent future dates
            'quantity' => 'nullable|integer|min:1'
        ]);

        $quantityToConsume = $request->input('quantity', 1);

        try {
            DB::beginTransaction();

            // Ensure the item belongs to the user
            $item = Item::where('id', $request->item_id)->where('user_id', Auth::id())->firstOrFail();
            
            $log = ConsumptionLog::where('item_id', $item->id)
                ->where('user_id', Auth::id())
                ->whereDate('consumed_date', $request->date)
                ->first();

            if ($log) {
                // UNDO
                $restoredQuantity = $log->quantity;
                $log->delete();
                $item->increment('stock', $restoredQuantity);
                $status = 'unchecked';
            } else {
                // DO
                if ($item->stock < $quantityToConsume) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Not enough stock! You only have {$item->stock} left."]);
                }

                ConsumptionLog::create([
                    'user_id' => Auth::id(),
                    'item_id' => $item->id,
                    'consumed_date' => $request->date,
                    'quantity' => $quantityToConsume
                ]);
                
                $item->decrement('stock', $quantityToConsume);
                $status = 'checked';
            }

            DB::commit();

            // Recalculate progress for the response based ONLY on active items
            $activeItemIds = Auth::user()->items()->where('is_active', true)->pluck('id');
            
            $currentDateLogs = ConsumptionLog::where('user_id', Auth::id())
                ->whereIn('item_id', $activeItemIds)
                ->whereDate('consumed_date', $request->date)
                ->count();
                
            $totalUserItems = $activeItemIds->count();

            return response()->json([
                'success' => true, 
                'status' => $status, 
                'current_stock' => $item->stock,
                'completed_count' => $currentDateLogs,
                'total_count' => $totalUserItems
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    // --- AJAX Toggle Water ---
    public function toggleWater(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today', // Prevent future dates
            'action' => 'required|in:add,remove'
        ]);

        $waterLog = WaterLog::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => $request->date],
            ['glasses' => 0]
        );

        if ($request->action === 'add' && $waterLog->glasses < 8) { // Assuming 8 is max
            $waterLog->increment('glasses');
        } elseif ($request->action === 'remove' && $waterLog->glasses > 0) {
            $waterLog->decrement('glasses');
        }

        return response()->json(['success' => true, 'glasses' => $waterLog->glasses]);
    }

    // --- Log Weight ---
    public function logWeight(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today', // Prevent future dates
            'weight' => 'required|numeric|min:10|max:500'
        ]);

        WeightLog::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => $request->date],
            ['weight' => $request->weight]
        );

        return back()->with('success', 'Weight logged successfully!');
    }

    // --- NEW: Toggle Prayers ---
    public function togglePrayer(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'prayer' => 'required|in:fajr,dhuhr,asr,maghrib,isha,tahajjud',
            'status' => 'required|boolean'
        ]);

        $prayerLog = PrayerLog::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => $request->date]
        );

        $prayerLog->update([
            $request->prayer => $request->status
        ]);

        return response()->json(['success' => true]);
    }

    // --- NEW: Log Exercise ---
    public function logExercise(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'exercise_name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1|max:600'
        ]);

        ExerciseLog::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'exercise_name' => $request->exercise_name,
            'duration_minutes' => $request->duration_minutes,
        ]);

        return back()->with('success', 'Exercise logged successfully!');
    }

    public function deleteExercise($id)
    {
        $exercise = ExerciseLog::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $exercise->delete();
        return back()->with('success', 'Exercise removed.');
    }

    // --- NEW: Update Daily Metrics (Fasting, Sleep, Mood) ---
    public function updateMetrics(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'is_fasting' => 'nullable|boolean',
            'sleep_hours' => 'nullable|numeric|min:0|max:24',
            'mood' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000'
        ]);

        $metric = DailyMetric::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => $request->date]
        );

        // Update only the fields provided in the request
        $metric->update($request->only(['is_fasting', 'sleep_hours', 'mood', 'notes']));

        // If it's an AJAX request (like toggling fast), return JSON
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Daily metrics updated!');
    }

    // --- Existing Analytics Logic ---
    public function analytics()
    {
        $userId = Auth::id();
        $userTimezone = Auth::user()->timezone ?? config('app.timezone');

        $totalItemsTracked = ConsumptionLog::where('user_id', $userId)->count();
        $uniqueDaysTracked = ConsumptionLog::where('user_id', $userId)->distinct('consumed_date')->count('consumed_date');
        
        // Streak calculation using user's local "Today"
        $streak = 0;
        $date = Carbon::today($userTimezone);
        
        while(true) {
            $hasLog = ConsumptionLog::where('user_id', $userId)->whereDate('consumed_date', $date->toDateString())->exists();
            if ($hasLog) {
                $streak++;
                $date->subDay();
            } else {
                if ($date->isToday($userTimezone)) {
                    $date->subDay();
                    if(ConsumptionLog::where('user_id', $userId)->whereDate('consumed_date', $date->toDateString())->exists()){
                        $streak++;
                        $date->subDay();
                        continue;
                    }
                }
                break;
            }
        }

        return view('user.analytics', compact('totalItemsTracked', 'uniqueDaysTracked', 'streak'));
    }
}