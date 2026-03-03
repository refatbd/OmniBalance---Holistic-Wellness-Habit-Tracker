@extends('layouts.app')

@section('content')
<style>
    nav.fixed.bottom-0 { display: none !important; }
</style>

<div class="flex flex-col items-center justify-center min-h-[80vh] p-6 text-center">
    
    <div class="w-24 h-24 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mb-8 shadow-inner border-4 border-white dark:border-gray-800">
        <span class="text-5xl">🌱</span>
    </div>

    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4 leading-tight">
        {{ __('messages.welcome_title') }}
    </h1>
    
    <p class="text-gray-500 dark:text-gray-400 mb-10 text-base max-w-sm">
        {{ __('messages.welcome_subtitle') }}
    </p>

    <div class="w-full space-y-4 max-w-xs">
        @if(\App\Models\Setting::get('enable_registration', '1') == '1')
            <a href="{{ route('register') }}" class="block w-full bg-primary hover:bg-emerald-600 text-white font-bold py-4 rounded-2xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                {{ __('messages.get_started') }}
            </a>
        @endif
        
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ __('messages.already_have_account') }}</p>
            <a href="{{ route('login') }}" class="block w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white border-2 border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary font-bold py-3.5 rounded-2xl transition-all shadow-sm">
                {{ __('messages.login') }}
            </a>
        </div>
    </div>
</div>
@endsection