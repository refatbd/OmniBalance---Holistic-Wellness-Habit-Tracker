<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Set default application settings
        Setting::updateOrCreate(['key' => 'app_name'], ['value' => 'OmniBalance']);
        Setting::updateOrCreate(['key' => 'enable_registration'], ['value' => '1']);
        
        // Admin user creation is now handled by the Installer Step 3.
    }
}