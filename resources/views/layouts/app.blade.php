<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::get('app_name', __('messages.app_name')) }}</title>
    
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#10b981">
    
    @vite(['assets/css/app.css', 'assets/js/app.js'])
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased pb-20 selection:bg-primary selection:text-white">

    <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50">
        <div class="max-w-md mx-auto px-4 py-3 flex justify-between items-center">
            <h1 class="text-xl font-bold text-primary truncate max-w-[150px]">
                {{ \App\Models\Setting::get('app_name', __('messages.app_name')) }}
            </h1>
            
            <div class="flex items-center space-x-2">
                <button id="install-app-btn" class="hidden text-xs font-bold bg-primary text-white px-3 py-1.5 rounded-lg shadow-sm transition-all hover:bg-emerald-600">
                    Install App
                </button>

                <a href="{{ route('lang.switch', app()->getLocale() === 'bn' ? 'en' : 'bn') }}" class="text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600">
                    {{ app()->getLocale() === 'bn' ? 'EN' : 'BN' }}
                </a>
                
                <button id="theme-toggle" class="text-gray-500 dark:text-gray-400 focus:outline-none rounded-lg p-1.5 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                    <svg id="theme-toggle-dark-icon" class="hidden w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path></svg>
                </button>

                @auth
                <a href="{{ route('profile') }}" class="text-gray-500 dark:text-gray-400 p-1.5 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 flex">
                    @csrf
                    <button type="submit" class="text-gray-500 dark:text-gray-400 hover:text-red-500 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-1.5 ml-1" title="{{ __('messages.logout') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-md mx-auto w-full min-h-[85vh]">
        @yield('content')
    </main>

    <div id="offline-indicator" class="hidden fixed bottom-20 left-1/2 transform -translate-x-1/2 z-[60] transition-opacity duration-300 w-11/12 max-w-sm">
        <div class="bg-red-500 text-white px-4 py-3 rounded-xl shadow-lg flex items-center space-x-3 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
            </svg>
            <span>You are currently offline. Changes will sync later.</span>
        </div>
    </div>

    @auth
        @if(auth()->user()->role === 'user')
        <nav class="fixed bottom-0 w-full bg-white dark:bg-gray-800 border-t dark:border-gray-700 shadow-lg z-50">
            <div class="max-w-md mx-auto flex justify-around">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-3 px-2 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-gray-500' }} w-full text-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-[10px] font-medium">{{ __('messages.daily') }}</span>
                </a>
                
                <a href="{{ route('items.index') }}" class="flex flex-col items-center py-3 px-2 {{ request()->routeIs('items.*') ? 'text-primary' : 'text-gray-500' }} w-full text-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span class="text-[10px] font-medium">{{ __('messages.items_stock') }}</span>
                </a>

                <a href="{{ route('activities.index') }}" class="flex flex-col items-center py-3 px-2 {{ request()->routeIs('activities.*') ? 'text-primary' : 'text-gray-500' }} w-full text-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-[10px] font-medium">Activities</span>
                </a>

                <a href="{{ route('analytics') }}" class="flex flex-col items-center py-3 px-2 {{ request()->routeIs('analytics') ? 'text-primary' : 'text-gray-500' }} w-full text-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span class="text-[10px] font-medium">{{ __('messages.analytics') }}</span>
                </a>
            </div>
        </nav>
        @else
        <nav class="fixed bottom-0 w-full bg-white dark:bg-gray-800 border-t dark:border-gray-700 shadow-lg z-50">
            <div class="max-w-md mx-auto flex justify-around">
                <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center py-3 px-4 {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-gray-500' }} w-full text-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span class="text-[10px] font-medium">Dashboard</span>
                </a>
                <a href="{{ route('users.index') }}" class="flex flex-col items-center py-3 px-4 {{ request()->routeIs('users.*') ? 'text-primary' : 'text-gray-500' }} w-full text-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="text-[10px] font-medium">Users</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="flex flex-col items-center py-3 px-4 {{ request()->routeIs('admin.settings') ? 'text-primary' : 'text-gray-500' }} w-full text-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-[10px] font-medium">Settings</span>
                </a>
            </div>
        </nav>
        @endif
    @endauth

    <script>
        // --- Theme Toggle Logic ---
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');
        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });

        // --- PWA Service Worker & Install Prompt Logic ---
        let deferredPrompt;
        const installBtn = document.getElementById('install-app-btn');

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // Register the Service Worker for PWA functionality
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    
                    @auth
                    // Push Notification Subscription
                    if ('PushManager' in window) {
                        Notification.requestPermission().then(function(permission) {
                            if (permission === 'granted') {
                                subscribeUser(registration);
                            }
                        });
                    }
                    @endauth

                }).catch(function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }

        // Listen for the Chrome specific install prompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing automatically on mobile
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI to notify the user they can install the PWA
            if(installBtn) installBtn.classList.remove('hidden');
        });

        // Handle the Install Button Click
        if(installBtn) {
            installBtn.addEventListener('click', async () => {
                // Hide the button
                installBtn.classList.add('hidden');
                if (deferredPrompt) {
                    // Show the native browser install prompt
                    deferredPrompt.prompt();
                    // Wait for the user to respond to the prompt
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`User response to the install prompt: ${outcome}`);
                    // Clear the deferred prompt variable, it can only be used once
                    deferredPrompt = null;
                }
            });
        }

        // Hide button if app is successfully installed
        window.addEventListener('appinstalled', (evt) => {
            console.log('App successfully installed!');
            if(installBtn) installBtn.classList.add('hidden');
        });

        @auth
        function subscribeUser(swRegistration) {
            const publicVapidKey = "{{ config('webpush.vapid.public_key') }}";
            if (!publicVapidKey) return; 
            
            // Helper function to format the key
            const urlBase64ToUint8Array = (base64String) => {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicVapidKey)
            })
            .then(function(subscription) {
                // Send subscription to backend
                fetch('{{ route("push.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(subscription)
                });
            })
            .catch(function(err) {
                console.log('Failed to subscribe the user: ', err);
            });
        }
        @endauth

        // --- OFFLINE STATUS INDICATOR LOGIC ---
        const offlineIndicator = document.getElementById('offline-indicator');

        function updateNetworkStatus() {
            if (navigator.onLine) {
                // Device is online
                offlineIndicator.classList.add('hidden');
            } else {
                // Device is offline
                offlineIndicator.classList.remove('hidden');
            }
        }

        // Listen for the browser's native online/offline events
        window.addEventListener('online', updateNetworkStatus);
        window.addEventListener('offline', updateNetworkStatus);

        // Run an initial check when the page first loads
        updateNetworkStatus();

    </script>
</body>
</html>