@extends('layouts.app')

@section('content')
<style>
    nav.fixed.bottom-0 { display: none !important; }
</style>

<div class="flex items-center justify-center min-h-[80vh] p-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-primary mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Welcome Back</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Please sign in to access your tracker</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 p-3 rounded-xl mb-6 text-sm text-center font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@example.com"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary dark:bg-gray-700 dark:border-gray-600">
                <label for="remember" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Remember me</label>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl transition-colors shadow-md mt-4">
                Sign In
            </button>
        </form>
    </div>
</div>
@endsection