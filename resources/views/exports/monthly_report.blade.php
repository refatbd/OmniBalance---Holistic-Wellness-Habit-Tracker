<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Nutrition & Habit Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #10b981; padding-bottom: 10px; }
        .title { font-size: 24px; font-weight: bold; color: #10b981; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .section-title { font-size: 16px; margin-bottom: 10px; font-weight: bold; color: #444; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Nutrition & Habit Tracker Report</div>
        <div>User: {{ $user->name }} | Email: {{ $user->email }}</div>
        <div>Period: {{ $startOfMonth->format('F 1, Y') }} - {{ $endOfMonth->format('F t, Y') }}</div>
    </div>

    <div class="section-title">Consumption History</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Calories</th>
                <th>Protein (g)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->consumed_date->format('M d, Y') }}</td>
                <td>{{ $log->item->name }}</td>
                <td>{{ $log->item->category }}</td>
                <td>{{ $log->quantity }}</td>
                <td>{{ $log->item->calories }}</td>
                <td>{{ $log->item->protein }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No consumption data logged this month.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Body Weight Tracking</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Weight (kg)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($weightLogs as $log)
            <tr>
                <td>{{ $log->date->format('M d, Y') }}</td>
                <td>{{ $log->weight }} kg</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center;">No weight data logged this month.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Exercise History</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Activity</th>
                <th>Duration (Minutes)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exerciseLogs as $log)
            <tr>
                <td>{{ $log->date->format('M d, Y') }}</td>
                <td>{{ $log->exercise_name }}</td>
                <td>{{ $log->duration_minutes }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">No exercise data logged this month.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Daily Metrics (Sleep, Mood & Fasting)</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Fasting?</th>
                <th>Sleep (Hours)</th>
                <th>Mood</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyMetrics as $metric)
            <tr>
                <td>{{ $metric->date->format('M d, Y') }}</td>
                <td>{{ $metric->is_fasting ? 'Yes' : 'No' }}</td>
                <td>{{ $metric->sleep_hours ?? '-' }}</td>
                <td>{{ $metric->mood ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">No daily metrics logged this month.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>