<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController; // NEW: Imported ActivityController

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Web Cron Route (Token-Protected)
|--------------------------------------------------------------------------
| Protected with a secret token defined in .env as CRON_SECRET.
| Call via: GET /cron/run?token=YOUR_SECRET
| Or better yet, use a real server cron: php artisan schedule:run
|--------------------------------------------------------------------------
*/
Route::get('/cron/run', function () {
    $secret = config('app.cron_secret');

    // If no secret is configured, deny all access
    if (empty($secret)) {
        abort(403, 'Cron access is disabled. Set CRON_SECRET in your .env file.');
    }

    if (request('token') !== $secret) {
        abort(403, 'Invalid cron token.');
    }

    Artisan::call('schedule:run');
    return response('Cron executed successfully.', 200);
})->name('cron.run');

/*
|--------------------------------------------------------------------------
| Language Switcher
|--------------------------------------------------------------------------
*/
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'bn'])) {
        session()->put('locale', $locale);
        if (auth()->check()) {
            auth()->user()->update(['language' => $locale]);
        }
    }
    return redirect()->back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Installer Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'install', 'middleware' => 'installer.check'], function () {
    Route::get('/', [InstallerController::class, 'index'])->name('installer.index');
    Route::get('/database', [InstallerController::class, 'database'])->name('installer.database');
    Route::post('/database', [InstallerController::class, 'processDatabase'])->name('installer.process');
    Route::get('/admin', [InstallerController::class, 'adminSetup'])->name('installer.admin');
    Route::post('/admin', [InstallerController::class, 'processAdminSetup'])->name('installer.admin.process');
});

/*
|--------------------------------------------------------------------------
| Public & Auth Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['check.installation', 'set.locale', 'set.timezone']], function () {

    // Public Welcome Page
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

    // Guest Auth — rate limited to 10 attempts per minute per IP
    Route::group(['middleware' => ['guest', 'throttle:10,1']], function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    });

    // Authenticated User Routes
    Route::group(['middleware' => 'auth'], function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // User Dashboard & Items
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/toggle-log', [DashboardController::class, 'toggleLog'])->name('log.toggle');

        // Profile Routes (View and Update)
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::post('/profile/test-notification', [ProfileController::class, 'testNotification'])->name('profile.test-notification');

        // Water & Weight
        Route::post('/toggle-water', [DashboardController::class, 'toggleWater'])->name('water.toggle');
        Route::post('/log-weight', [DashboardController::class, 'logWeight'])->name('weight.log');

        // Habit Tracker
        Route::post('/toggle-prayer', [DashboardController::class, 'togglePrayer'])->name('prayer.toggle');
        Route::post('/log-exercise', [DashboardController::class, 'logExercise'])->name('exercise.log');
        Route::post('/delete-exercise/{id}', [DashboardController::class, 'deleteExercise'])->name('exercise.delete');
        Route::post('/update-metrics', [DashboardController::class, 'updateMetrics'])->name('metrics.update');

        // --- NEW: Saved Activities / Quick Add Management ---
        Route::resource('activities', ActivityController::class);

        Route::resource('items', ItemController::class);
        Route::patch('items/{id}/toggle-active', [ItemController::class, 'toggleActive'])->name('items.toggle');
        Route::post('items/{id}/stock', [StockController::class, 'update'])->name('stock.update');

        // Analytics & Exports
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/export/csv', [AnalyticsController::class, 'exportCsv'])->name('analytics.export.csv');
        Route::get('/analytics/export/pdf', [AnalyticsController::class, 'exportPdf'])->name('analytics.export.pdf');

        // PWA Push Subscriptions
        Route::post('/push-subscriptions', [\App\Http\Controllers\PushSubscriptionController::class, 'update'])->name('push.subscribe');
    });

    // Admin Routes
    Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'is_admin']], function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::resource('users', UserController::class);
        Route::post('users/{user}/suspend', [UserController::class, 'toggleSuspend'])->name('admin.users.suspend');
        Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::post('/settings/regenerate-cron-secret', [SettingController::class, 'regenerateCronSecret'])->name('admin.settings.regenerate-cron');
    });
});