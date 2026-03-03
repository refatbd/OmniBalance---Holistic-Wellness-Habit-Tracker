@extends('layouts.app')

@section('content')
<div class="p-4 pb-24">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Account Settings</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your personal information and preferences.</p>
    </div>

    <div class="mb-6 mt-4">
        <button type="button" onclick="sendTestNotification(event)" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3.5 rounded-2xl transition-all shadow-md flex justify-center items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            Send Test Notification
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 p-3 rounded-xl mb-6 text-sm text-center font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 p-3 rounded-xl mb-6 text-sm text-center font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Basic Details</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
                </div>
            </div>

            <hr class="border-gray-100 dark:border-gray-700">

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Preferences</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Timezone</label>
                    <select name="timezone" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors appearance-none">
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" {{ old('timezone', $user->timezone) == $tz ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', $tz) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-gray-500 mt-1 italic">This ensures your daily tracker resets correctly at midnight in your local time.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Water Reminder Interval</label>
                    <select name="water_reminder_interval" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors appearance-none">
                        <option value="">Disabled</option>
                        <option value="30" {{ old('water_reminder_interval', $user->water_reminder_interval) == '30' ? 'selected' : '' }}>Every 30 Minutes</option>
                        <option value="60" {{ old('water_reminder_interval', $user->water_reminder_interval) == '60' ? 'selected' : '' }}>Every 1 Hour</option>
                        <option value="120" {{ old('water_reminder_interval', $user->water_reminder_interval) == '120' ? 'selected' : '' }}>Every 2 Hours</option>
                    </select>
                </div>

                <div class="mt-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="receive_bedtime_notifications" value="1" class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary dark:bg-gray-700 dark:border-gray-600" {{ old('receive_bedtime_notifications', $user->receive_bedtime_notifications) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Allow reminders during bedtime (10 PM - 8 AM)</span>
                    </label>
                </div>

                <div class="pt-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preferred Language</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="language" value="en" class="peer hidden" {{ old('language', $user->language) == 'en' ? 'checked' : '' }}>
                            <div class="text-center py-2 px-4 border border-gray-200 dark:border-gray-600 rounded-xl peer-checked:border-primary peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 peer-checked:text-primary dark:text-gray-300 transition-all font-bold">
                                English
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="language" value="bn" class="peer hidden" {{ old('language', $user->language) == 'bn' ? 'checked' : '' }}>
                            <div class="text-center py-2 px-4 border border-gray-200 dark:border-gray-600 rounded-xl peer-checked:border-primary peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 peer-checked:text-primary dark:text-gray-300 transition-all font-bold">
                                বাংলা
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 dark:border-gray-700">

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Security (Leave blank to keep current)</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                    <input type="password" name="current_password" placeholder="••••••••"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <input type="password" name="new_password" placeholder="Min 8 chars"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" placeholder="••••••••"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-emerald-600 text-white font-bold py-4 rounded-2xl transition-all shadow-lg hover:shadow-xl transform active:scale-[0.98] mt-4">
                Save All Changes
            </button>
        </form>
    </div>
</div>

<script>
    function sendTestNotification(event) {
        const btn = event.currentTarget;
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Sending...';
        btn.disabled = true;

        fetch('{{ route("profile.test-notification") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            // Replaced native alert with SweetAlert2
            Swal.fire({
                title: data.success ? 'Success!' : 'Notification Sent',
                text: data.message,
                icon: data.success ? 'success' : 'info',
                confirmButtonColor: '#10b981'
            });
            btn.innerHTML = originalText;
            btn.disabled = false;
        })
        .catch(err => {
            // Replaced native alert with SweetAlert2
            Swal.fire({
                title: 'Error',
                text: 'An error occurred while sending the test notification.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endsection