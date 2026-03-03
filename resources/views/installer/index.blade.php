@extends('installer.layout')

@section('content')
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Step 1: Server Requirements</h2>
    
    <ul class="space-y-3 mb-6">
        <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-700">PHP >= 8.1.0</span>
            @if($requirements['php'])
                <span class="text-emerald-500 font-bold">✔ OK</span>
            @else
                <span class="text-red-500 font-bold">✖ Failed</span>
            @endif
        </li>
        <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-700">PDO Extension</span>
            @if($requirements['pdo'])
                <span class="text-emerald-500 font-bold">✔ OK</span>
            @else
                <span class="text-red-500 font-bold">✖ Failed</span>
            @endif
        </li>
        <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-700">.env Writable</span>
            @if($requirements['env_writable'])
                <span class="text-emerald-500 font-bold">✔ OK</span>
            @else
                <span class="text-red-500 font-bold">✖ Failed</span>
            @endif
        </li>
    </ul>

    @php
        $canContinue = !in_array(false, $requirements);
    @endphp

    @if($canContinue)
        <a href="{{ route('installer.database') }}" class="block w-full text-center bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 rounded-lg transition-colors">
            Continue to Database Setup
        </a>
    @else
        <div class="p-4 bg-red-100 text-red-700 rounded-lg text-sm text-center">
            Please resolve the failed requirements to continue.
        </div>
    @endif
@endsection