@extends('layouts.app')

@section('content')
<div class="p-4 pb-24">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Admin Dashboard</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Platform overview and statistics.</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-500 rounded-full flex items-center justify-center text-xl mb-3">
                👥
            </div>
            <div class="text-3xl font-bold text-gray-800 dark:text-white mb-1">{{ $totalUsers }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Users</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 text-purple-500 rounded-full flex items-center justify-center text-xl mb-3">
                🛡️
            </div>
            <div class="text-3xl font-bold text-gray-800 dark:text-white mb-1">{{ $totalAdmins }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Admins</div>
        </div>

        <div class="col-span-2 bg-gradient-to-r from-emerald-500 to-teal-400 rounded-2xl p-5 shadow-md flex justify-between items-center text-white">
            <div>
                <div class="text-sm font-medium opacity-90">Total Consumption Logs</div>
                <div class="text-3xl font-extrabold mt-1">{{ $totalLogs }}</div>
                <div class="text-xs opacity-75 mt-1">Across all users</div>
            </div>
            <div class="text-5xl opacity-80">📈</div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-3">System Information</h3>
        <ul class="space-y-3 text-sm">
            <li class="flex justify-between border-b dark:border-gray-700 pb-2">
                <span class="text-gray-500 dark:text-gray-400">Laravel Version:</span>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ app()->version() }}</span>
            </li>
            <li class="flex justify-between border-b dark:border-gray-700 pb-2">
                <span class="text-gray-500 dark:text-gray-400">PHP Version:</span>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ phpversion() }}</span>
            </li>
            <li class="flex justify-between pb-2">
                <span class="text-gray-500 dark:text-gray-400">Timezone:</span>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ config('app.timezone') }}</span>
            </li>
        </ul>
    </div>
</div>
@endsection