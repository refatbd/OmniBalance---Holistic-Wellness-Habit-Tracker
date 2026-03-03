<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultActivitySeeder extends Seeder
{
    // Make this static so we can call it from AuthController during registration
    public static function getDefaultActivities()
    {
        return [
            ['icon' => '🚶‍♂️', 'name' => 'Brisk Walking', 'default_duration' => 30],
            ['icon' => '🧘‍♀️', 'name' => 'Kegel Exercises', 'default_duration' => 5],
            ['icon' => '🧘', 'name' => 'Yoga / Stretching', 'default_duration' => 15],
            ['icon' => '🫁', 'name' => 'Deep Breathing / Meditation', 'default_duration' => 10],
            ['icon' => '🏋️', 'name' => 'Home Workout', 'default_duration' => 20],
            ['icon' => '📖', 'name' => 'Reading (Book/Quran)', 'default_duration' => 20],
            ['icon' => '✍️', 'name' => 'Journaling / Reflection', 'default_duration' => 10],
            ['icon' => '🚴‍♂️', 'name' => 'Cycling', 'default_duration' => 30],
        ];
    }

    public function run(): void
    {
        // Handled dynamically during registration
    }
}