@extends('installer.layout')

@section('content')
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Step 2: Database Setup</h2>

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm text-center">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('installer.process') }}" method="POST" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
            <input type="text" name="db_host" value="127.0.0.1" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Database Port</label>
            <input type="text" name="db_port" value="3306" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
            <input type="text" name="db_name" required placeholder="nutrition_db" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Database Username</label>
            <input type="text" name="db_user" required placeholder="root" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Database Password</label>
            <input type="password" name="db_pass" placeholder="Leave empty if none" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <button type="submit" class="w-full mt-6 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 rounded-lg transition-colors">
            Connect & Install
        </button>
    </form>
@endsection