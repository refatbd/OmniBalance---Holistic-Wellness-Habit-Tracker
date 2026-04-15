<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class InstallerController extends Controller
{
    // Step 1: Show welcome and check server requirements
    public function index()
    {
        // By the time this runs, index.php has already ensured .env exists and APP_KEY is set.
        // Nothing to do here except display the requirements check.
        $requirements = [
            'php'          => version_compare(PHP_VERSION, '8.1.0', '>='),
            'pdo'          => extension_loaded('pdo'),
            'mbstring'     => extension_loaded('mbstring'),
            'openssl'      => extension_loaded('openssl'),
            'env_writable' => File::isWritable(base_path('.env')),
        ];

        return view('installer.index', compact('requirements'));
    }

    // Step 2: Show database configuration form
    public function database()
    {
        return view('installer.database');
    }

    // Step 3: Process database credentials and write them to the .env file
    public function processDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required',
            'db_port' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
        ]);

        // Attempt a test connection to the database
        try {
            $pdo = new \PDO(
                sprintf('mysql:host=%s;port=%s;dbname=%s', $request->db_host, $request->db_port, $request->db_name),
                $request->db_user,
                $request->db_pass
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not connect to the database. Please check your credentials.');
        }

        // Update the .env file with the valid credentials
        $this->setEnvValue('DB_HOST', $request->db_host);
        $this->setEnvValue('DB_PORT', $request->db_port);
        $this->setEnvValue('DB_DATABASE', $request->db_name);
        $this->setEnvValue('DB_USERNAME', $request->db_user);
        $this->setEnvValue('DB_PASSWORD', $request->db_pass ?: '');

        // Auto-detect and set APP_URL and Timezone
        $this->setEnvValue('APP_URL', rtrim($request->getSchemeAndHttpHost(), '/'));
        $this->setEnvValue('APP_TIMEZONE', date_default_timezone_get());

        // Forcefully delete the config cache file so the next step loads the new .env
        $configCachePath = base_path('bootstrap/cache/config.php');
        if (file_exists($configCachePath)) {
            unlink($configCachePath);
        }

        return redirect()->route('installer.admin');
    }

    // Step 4: Show Super Admin Setup Form
    public function adminSetup()
    {
        return view('installer.admin');
    }

    // Step 5: Process Super Admin, run migrations, and finalize
    public function processAdminSetup(Request $request)
    {
        $request->validate([
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|string|email|max:255',
            'admin_password' => 'required|string|min:8',
            'timezone'       => 'required|string|timezone',
        ]);

        try {
            // Delete config cache again just in case
            $configCachePath = base_path('bootstrap/cache/config.php');
            if (file_exists($configCachePath)) {
                unlink($configCachePath);
            }

            // Purge the old DB connection so Laravel uses the fresh .env data
            DB::purge('mysql');

            // Run migrations and seeder
            Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);

            // Create the Super Admin User
            User::create([
                'name'     => $request->admin_name,
                'email'    => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role'     => 'admin',
                'language' => 'en',
                'timezone' => $request->timezone,
            ]);

            // Always generate a fresh APP_KEY during installation so no pre-generated key is ever shipped with the zip
            Artisan::call('key:generate', ['--force' => true]);

            // Auto-generate a secure CRON_SECRET token so the admin never has to do it manually
            $existingCronSecret = env('CRON_SECRET', '');
            if (empty($existingCronSecret)) {
                $cronSecret = Str::random(48);
                $this->setEnvValue('CRON_SECRET', $cronSecret);
            }

            // Clear application cache AFTER tables are created
            Artisan::call('cache:clear');

            // Create the installed marker file
            File::put(storage_path('installed'), 'installed_on_' . date('Y-m-d_H:i:s'));

            return redirect('/login')->with('success', 'Application installed successfully! Please login as Admin.');
        } catch (\Exception $e) {
            return back()->with('error', 'Installation failed: ' . $e->getMessage());
        }
    }

    // Helper method to safely write values to the .env file
    private function setEnvValue($key, $value)
    {
        $path = base_path('.env');

        // If .env doesn't exist, create it from .env.example
        if (!File::exists($path)) {
            File::copy(base_path('.env.example'), $path);
        }

        $envContent = file_get_contents($path);

        // Use single quotes to prevent PHP dotenv from breaking passwords with $ or # signs
        $escapedValue = str_replace("'", "\'", $value);
        $newLine = "{$key}='{$escapedValue}'";

        if (preg_match("/^{$key}=/m", $envContent)) {
            $envContent = preg_replace_callback("/^{$key}=.*/m", function () use ($newLine) {
                return $newLine;
            }, $envContent);
        } else {
            $envContent .= "\n" . $newLine;
        }

        file_put_contents($path, $envContent);
    }
}
