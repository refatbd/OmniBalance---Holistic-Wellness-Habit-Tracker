<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Check if application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// ─── Installer bootstrap: ensure APP_KEY exists before Laravel boots ──────────
// Laravel's session/cookie middleware requires APP_KEY on the very first request.
// If .env is missing or has no key, generate both right here — before the
// framework starts, so the installer's first page load never throws MissingAppKeyException.
(function () {
    $envPath     = __DIR__ . '/.env';
    $examplePath = __DIR__ . '/.env.example';

    // Create .env from the example template if it doesn't exist yet
    if (!file_exists($envPath) && file_exists($examplePath)) {
        copy($examplePath, $envPath);
    }

    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);

        // Check if APP_KEY is blank (e.g. "APP_KEY=" or "APP_KEY=''")
        if (preg_match('/^APP_KEY=\s*$/m', $envContent) || preg_match('/^APP_KEY=\'\'$/m', $envContent)) {
            // Generate a secure random key using the same format as artisan key:generate
            $key    = 'base64:' . base64_encode(random_bytes(32));
            $newEnv = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $envContent);
            file_put_contents($envPath, $newEnv);

            // Bust the config cache so the new key is used immediately
            $configCache = __DIR__ . '/bootstrap/cache/config.php';
            if (file_exists($configCache)) {
                unlink($configCache);
            }
        }
    }
})();
// ─────────────────────────────────────────────────────────────────────────────

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/bootstrap/app.php')
    ->handleRequest(Request::capture());