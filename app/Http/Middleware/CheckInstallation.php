<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CheckInstallation
{
    public function handle(Request $request, Closure $next)
    {
        $installedFile = storage_path('installed');
        $isInstallerRoute = $request->is('install*');

        // Dual check: the marker file must exist AND an admin user must exist in the DB.
        // This prevents the installer from being re-accessible if the file is accidentally deleted.
        $isInstalled = $this->isInstalled($installedFile);

        // If not installed and trying to access main app, redirect to installer
        if (!$isInstalled && !$isInstallerRoute) {
            return redirect()->route('installer.index');
        }

        // If already installed and trying to access installer, redirect to dashboard
        if ($isInstalled && $isInstallerRoute) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }

    /**
     * Determine if the application is fully installed.
     * Checks both the marker file AND the presence of an admin user in the database.
     */
    private function isInstalled(string $installedFile): bool
    {
        // Primary check: marker file
        if (!File::exists($installedFile)) {
            return false;
        }

        // Secondary check: confirm an admin user actually exists.
        // This protects against file deletion accidents or deployments that reset storage.
        try {
            if (Schema::hasTable('users')) {
                return \App\Models\User::where('role', 'admin')->exists();
            }
        } catch (\Exception $e) {
            // If DB is unreachable, fall back to file check only
            return true;
        }

        return false;
    }
}
