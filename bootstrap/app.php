<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckInstallation;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetUserTimezone;
use App\Http\Middleware\SecurityHeaders;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register aliases
        $middleware->alias([
            'check.installation' => CheckInstallation::class,
            'installer.check'    => CheckInstallation::class,
            'is_admin'           => IsAdmin::class,
            'set.locale'         => SetLocale::class,
            'set.timezone'       => SetUserTimezone::class,
        ]);

        // Append to web group: timezone + security headers on every response
        $middleware->appendToGroup('web', [
            SetUserTimezone::class,
            SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Required for root-directory hosting (no /public subfolder)
$app->usePublicPath(base_path());

return $app;
