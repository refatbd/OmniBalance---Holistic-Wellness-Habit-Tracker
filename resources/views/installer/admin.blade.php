@extends('installer.layout')

@section('content')
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Step 3: Super Admin Setup</h2>
    
    <p class="text-sm text-gray-600 mb-6">Database connected successfully! Now, create the main administrator account to manage the application.</p>

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm text-center">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('installer.admin.process') }}" method="POST" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Admin Name</label>
            <input type="text" name="admin_name" value="Admin" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Admin Email</label>
            <input type="email" name="admin_email" required placeholder="admin@example.com" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="admin_password" required placeholder="Min 8 characters" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
            <select name="timezone" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none appearance-none bg-white">
                @foreach(timezone_identifiers_list() as $tz)
                    <option value="{{ $tz }}" {{ $tz === 'Asia/Dhaka' ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', $tz) }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="w-full mt-6 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 rounded-lg transition-colors">
            Complete Installation
        </button>
    </form>
@endsection