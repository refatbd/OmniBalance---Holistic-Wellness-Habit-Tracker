<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password'); // 'admin' or 'user'
            $table->boolean('is_suspended')->default(false)->after('role');
            $table->string('language')->default('bn')->after('is_suspended'); // Store preferred language
            $table->string('timezone')->default('UTC')->after('language'); // Store preferred timezone
            
            // --- NEW FIELDS FOR WATER REMINDER & PREFERENCES ---
            $table->integer('water_reminder_interval')->nullable()->after('timezone'); 
            $table->boolean('receive_bedtime_notifications')->default(false)->after('water_reminder_interval');
            $table->timestamp('last_water_reminder_at')->nullable()->after('receive_bedtime_notifications');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 
                'is_suspended', 
                'language', 
                'timezone', 
                'water_reminder_interval', 
                'receive_bedtime_notifications',
                'last_water_reminder_at'
            ]);
        });
    }
};