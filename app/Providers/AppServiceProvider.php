<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Force the public path to be the root directory
        $this->app->usePublicPath(base_path());
    }

    public function boot(): void
    {
        // Fix DomPDF public path issue for root-directory hosting
        config(['dompdf.public_path' => base_path()]);
    }
}