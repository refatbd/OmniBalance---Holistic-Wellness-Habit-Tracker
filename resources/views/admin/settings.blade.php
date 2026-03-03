@extends('layouts.app')

@section('content')
<div class="p-4 pb-24">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Global Settings</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure application behavior</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 p-3 rounded-xl mb-4 text-sm text-center">
            {{ session('success') }}
        </div>
    @endif

    {{-- App Settings --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-5 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Application Name</label>
                <input type="text" name="app_name" value="{{ old('app_name', $appName) }}" required
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary focus:outline-none transition-colors">
                <p class="text-[10px] text-gray-500 mt-1">This will update the header and welcome page.</p>
            </div>

            <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">User Registration</label>

                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="radio" name="enable_registration" value="1" class="text-primary focus:ring-primary h-4 w-4" {{ old('enable_registration', $enableRegistration) == '1' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enabled (Open)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="enable_registration" value="0" class="text-red-500 focus:ring-red-500 h-4 w-4" {{ old('enable_registration', $enableRegistration) == '0' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Disabled (Private)</span>
                    </label>
                </div>
                <p class="text-[10px] text-gray-500 mt-2">If disabled, the sign-up button will disappear and new users cannot register.</p>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl transition-colors shadow-md mt-4">
                Save Settings
            </button>
        </form>
    </div>

    {{-- Cron Job Setup --}}
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">⏱️ Cron Job Setup</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Required to send automated push notifications and water reminders. Your URL is pre-secured with a secret token.</p>
            </div>
            {{-- Regenerate token button --}}
            <form action="{{ route('admin.settings.regenerate-cron') }}" method="POST" class="shrink-0"
                  onsubmit="confirmRegenerate(event, this)">
                @csrf
                <button type="submit"
                        class="text-xs bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-800/40 font-semibold px-3 py-2 rounded-lg transition-colors whitespace-nowrap">
                    🔄 Regenerate Token
                </button>
            </form>
        </div>

        @if(empty($cronSecret))
            <div class="p-5">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 rounded-xl p-4 text-sm">
                    ⚠️ <strong>CRON_SECRET is not set</strong> in your <code>.env</code> file. The cron endpoint is currently <strong>disabled</strong>.
                    Click <strong>Regenerate Token</strong> above to auto-generate one.
                </div>
            </div>
        @else
        <div class="p-5 space-y-6">

            {{-- Method 1: Direct URL (for cPanel URL cron) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Method 1: URL Cron (cPanel / Hosting Panel)</label>
                <p class="text-[11px] text-gray-500 mb-2">Paste this URL into your hosting panel's "URL Cron" or "HTTP Cron" field. Run <strong>every minute (* * * * *)</strong>.</p>
                <div class="flex shadow-sm rounded-lg overflow-hidden">
                    <input type="text" readonly value="{{ $cronUrl }}" id="urlCron"
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border-y border-l border-gray-300 dark:border-gray-600 text-xs font-mono text-gray-600 dark:text-gray-300 focus:outline-none">
                    <button type="button" onclick="copyToClipboard('urlCron', this)"
                            class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 text-xs font-bold transition-colors border-y border-r border-gray-300 dark:border-gray-600 whitespace-nowrap">
                        Copy URL
                    </button>
                </div>
            </div>

            {{-- Method 2: cURL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Method 2: Using cURL</label>
                <p class="text-[11px] text-gray-500 mb-2">Add this to your server's crontab. Run <strong>every minute (* * * * *)</strong>.</p>
                <div class="flex shadow-sm rounded-lg overflow-hidden">
                    <input type="text" readonly value="* * * * * curl -s &quot;{{ $cronUrl }}&quot; > /dev/null 2>&1" id="curlCron"
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border-y border-l border-gray-300 dark:border-gray-600 text-xs font-mono text-gray-600 dark:text-gray-300 focus:outline-none">
                    <button type="button" onclick="copyToClipboard('curlCron', this)"
                            class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 text-xs font-bold transition-colors border-y border-r border-gray-300 dark:border-gray-600 whitespace-nowrap">
                        Copy Command
                    </button>
                </div>
            </div>

            {{-- Method 3: Wget --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Method 3: Using Wget</label>
                <p class="text-[11px] text-gray-500 mb-2">Alternative if cURL is not available. Run <strong>every minute (* * * * *)</strong>.</p>
                <div class="flex shadow-sm rounded-lg overflow-hidden">
                    <input type="text" readonly value="* * * * * wget -q -O - &quot;{{ $cronUrl }}&quot; > /dev/null 2>&1" id="wgetCron"
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border-y border-l border-gray-300 dark:border-gray-600 text-xs font-mono text-gray-600 dark:text-gray-300 focus:outline-none">
                    <button type="button" onclick="copyToClipboard('wgetCron', this)"
                            class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 text-xs font-bold transition-colors border-y border-r border-gray-300 dark:border-gray-600 whitespace-nowrap">
                        Copy Command
                    </button>
                </div>
            </div>

            {{-- Method 4: PHP CLI --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Method 4: PHP CLI (Recommended for VPS/Dedicated)</label>
                <p class="text-[11px] text-gray-500 mb-2">Calls artisan directly — no HTTP overhead, most reliable. Run <strong>every minute (* * * * *)</strong>.</p>
                <div class="flex shadow-sm rounded-lg overflow-hidden">
                    <input type="text" readonly value="* * * * * php {{ base_path('artisan') }} schedule:run >> /dev/null 2>&1" id="cliCron"
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-900 border-y border-l border-gray-300 dark:border-gray-600 text-xs font-mono text-gray-600 dark:text-gray-300 focus:outline-none">
                    <button type="button" onclick="copyToClipboard('cliCron', this)"
                            class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-4 py-2 text-xs font-bold transition-colors border-y border-r border-gray-300 dark:border-gray-600 whitespace-nowrap">
                        Copy Command
                    </button>
                </div>
                <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1">⚠️ This method does not require the secret token as it runs locally on your server.</p>
            </div>

            {{-- Security notice --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-3 text-xs text-blue-700 dark:text-blue-300">
                🔒 <strong>Your cron URL is protected</strong> with a unique secret token. Anyone without the token will get a 403 error.
                If you suspect your token has been compromised, use the <strong>Regenerate Token</strong> button above — then update your cron job with the new URL.
            </div>

        </div>
        @endif
    </div>
</div>

<script>
    function copyToClipboard(inputId, buttonElement) {
        const input = document.getElementById(inputId);
        // Use modern clipboard API with fallback
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value).then(() => showSuccess(buttonElement));
        } else {
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            showSuccess(buttonElement);
        }
    }

    function showSuccess(btn) {
        const originalText = btn.innerText;
        btn.innerText = 'Copied! ✔';
        btn.classList.add('bg-emerald-500', 'text-white', 'dark:bg-emerald-500', 'border-emerald-500');
        setTimeout(() => {
            btn.innerText = originalText;
            btn.classList.remove('bg-emerald-500', 'text-white', 'dark:bg-emerald-500', 'border-emerald-500');
        }, 2000);
    }

    // NEW: SweetAlert function for regenerate confirmation
    function confirmRegenerate(event, form) {
        event.preventDefault(); // Stop immediate form submission
        
        Swal.fire({
            title: 'Regenerate Token?',
            text: "Your existing cron jobs will stop working until you update them with the new URL.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b', // amber-500
            cancelButtonColor: '#6b7280', // gray-500
            confirmButtonText: 'Yes, regenerate it'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submit the form if confirmed
            }
        });
    }
</script>
@endsection