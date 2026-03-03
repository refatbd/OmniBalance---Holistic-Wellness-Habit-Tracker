@extends('layouts.app')

@section('content')
<div class="p-4 pb-24">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Analytics</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Your progress overview</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('analytics.export.csv') }}" class="px-3 py-1.5 bg-gray-800 dark:bg-gray-100 text-white dark:text-gray-900 text-xs font-bold rounded-lg shadow-sm hover:opacity-90 transition">
                Export CSV
            </a>
            <a href="{{ route('analytics.export.pdf') }}" class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg shadow-sm hover:opacity-90 transition">
                Export PDF
            </a>
        </div>
    </div>

    <div class="bg-gradient-to-br from-emerald-400 to-primary rounded-3xl p-6 text-white shadow-lg mb-6 flex flex-col items-center justify-center text-center relative overflow-hidden">
        <div class="text-5xl mb-2">🔥</div>
        <h3 class="text-lg font-medium opacity-90">{{ __('messages.current_streak') }}</h3>
        <div class="text-5xl font-extrabold mt-1">{{ $streak }}</div>
        <p class="text-sm font-medium opacity-80 mt-1">{{ __('messages.days_in_row') }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl p-4 shadow-sm border border-indigo-100 dark:border-indigo-800 text-center">
            <div class="text-2xl mb-1">🕌</div>
            <div class="text-xl font-extrabold text-indigo-700 dark:text-indigo-400">{{ $totalPrayersOffered ?? 0 }}</div>
            <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-300 uppercase mt-1">Prayers Logged</div>
            <div class="text-[9px] text-gray-500 mt-1">This month</div>
        </div>

        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-2xl p-4 shadow-sm border border-orange-100 dark:border-orange-800 text-center">
            <div class="text-2xl mb-1">🏃‍♂️</div>
            <div class="text-xl font-extrabold text-orange-700 dark:text-orange-400">{{ $totalExerciseMinutes ?? 0 }}</div>
            <div class="text-[10px] font-bold text-orange-600 dark:text-orange-300 uppercase mt-1">Active Minutes</div>
            <div class="text-[9px] text-gray-500 mt-1">This month</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 mb-4">{{ $startOfMonth->format('F Y') }} History</h3>
        
        <div class="grid grid-cols-7 gap-2 text-center text-xs mb-2 text-gray-400">
            <div>S</div><div>M</div><div>T</div><div>W</div><div>T</div><div>F</div><div>S</div>
        </div>
        
        <div class="grid grid-cols-7 gap-2">
            @php
                // Pad empty days at the start of the month
                $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)
            @endphp
            
            @for($i = 0; $i < $startDayOfWeek; $i++)
                <div class="aspect-square rounded-md"></div>
            @endfor

            @foreach($calendarData as $date => $status)
                @php
                    $bgColor = 'bg-gray-100 dark:bg-gray-700'; // status 0
                    if($status == 1) $bgColor = 'bg-yellow-400';
                    if($status == 2) $bgColor = 'bg-primary';
                @endphp
                <div class="aspect-square rounded-md flex items-center justify-center {{ $bgColor }} text-[10px] font-medium text-gray-600 dark:text-gray-300 {{ $status > 0 ? 'text-white dark:text-white' : '' }}" title="{{ $date }}">
                    {{ \Carbon\Carbon::parse($date)->day }}
                </div>
            @endforeach
        </div>
        <div class="flex justify-center gap-4 mt-4 text-[10px] text-gray-500">
            <div class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-gray-100 dark:bg-gray-700"></div> No Data</div>
            <div class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-yellow-400"></div> Partial</div>
            <div class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-primary"></div> Perfect</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 mb-4">Body Weight (kg)</h3>
        
        <form action="{{ route('weight.log') }}" method="POST" class="flex gap-2 mb-4">
            @csrf
            <input type="date" name="date" value="{{ \Carbon\Carbon::today(auth()->user()->timezone)->toDateString() }}" 
                   max="{{ \Carbon\Carbon::today(auth()->user()->timezone)->toDateString() }}"
                   class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white flex-1">
            <input type="number" step="0.1" name="weight" placeholder="Weight" required class="px-3 py-2 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white w-24">
            <button type="submit" class="bg-primary text-white px-4 rounded-lg text-sm font-bold">Log</button>
        </form>

        <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
            @foreach($weightLogs->reverse() as $log)
                <div class="flex justify-between items-center text-sm py-2 border-b last:border-0 dark:border-gray-700">
                    <span class="text-gray-500 dark:text-gray-400">{{ $log->date->format('M d, Y') }}</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $log->weight }} kg</span>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection