<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $appName           = Setting::get('app_name', 'Nutrition Tracker');
        $enableRegistration = Setting::get('enable_registration', '1');

        // Build the cron URL with the secret token embedded
        $cronSecret = config('app.cron_secret');
        $cronUrl    = route('cron.run') . '?token=' . $cronSecret;

        return view('admin.settings', compact('appName', 'enableRegistration', 'cronUrl', 'cronSecret'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name'            => 'required|string|max:100',
            'enable_registration' => 'required|in:0,1',
        ]);

        Setting::updateOrCreate(['key' => 'app_name'],            ['value' => $request->app_name]);
        Setting::updateOrCreate(['key' => 'enable_registration'], ['value' => $request->enable_registration]);

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Regenerate the CRON_SECRET token and write it to .env
     */
    public function regenerateCronSecret()
    {
        $newSecret = Str::random(48);
        $this->setEnvValue('CRON_SECRET', $newSecret);

        return back()->with('success', 'Cron secret regenerated. Copy your new URLs below.');
    }

    // Helper: safely write a key=value pair to the .env file
    private function setEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');

        if (!File::exists($path)) {
            return;
        }

        $envContent   = file_get_contents($path);
        $escapedValue = str_replace("'", "\'", $value);
        $newLine      = "{$key}='{$escapedValue}'";

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
