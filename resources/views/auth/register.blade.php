@extends('layouts.app')

@section('content')
<style>
    nav.fixed.bottom-0 { display: none !important; }
</style>

<div class="flex items-center justify-center min-h-[80vh] p-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('messages.register') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create an account to track your habits</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 p-3 rounded-xl mb-6 text-sm text-center font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.name_label') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.email_label') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Timezone</label>
                <select name="timezone" id="timezone-select" required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors appearance-none">
                    @foreach(timezone_identifiers_list() as $timezone)
                        <option value="{{ $timezone }}" {{ old('timezone', 'UTC') == $timezone ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', $timezone) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.password_label') }}</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.confirm_password_label') }}</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl transition-colors shadow-md mt-6">
                {{ __('messages.register') }}
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-primary hover:underline">{{ __('messages.already_have_account') }} {{ __('messages.login') }}</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Only auto-detect if there is no previous validation error attempting to retain old input
        @if(!old('timezone'))
            try {
                // Get the user's timezone from the browser
                const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                const select = document.getElementById('timezone-select');
                
                if (userTimezone && select) {
                    // Loop through options and select the matching timezone
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value === userTimezone) {
                            select.selectedIndex = i;
                            break;
                        }
                    }
                }
            } catch (error) {
                console.error("Could not automatically detect timezone.", error);
            }
        @endif
    });
</script>
@endsection