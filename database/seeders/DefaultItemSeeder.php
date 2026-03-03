<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class DefaultItemSeeder extends Seeder
{
    // Make this static so we can call it from AuthController during registration
    public static function getDefaultItems()
    {
        return [
            // Icon, Name, Instruction, Timing, Unit, Category, Cal, Protein, Carbs, Fats, Active, Low Stock Threshold
            ['icon' => '🧄', 'name' => 'রসুন', 'instruction' => '১–২ কোয়া কুচি করে ৫ মিনিট রেখে চিবিয়ে বা গিলে', 'timing' => 'সকালে খালি পেটে', 'unit' => 'কোয়া', 'category' => 'General', 'calories' => 4, 'protein' => 0.2, 'carbs' => 1.0, 'fats' => 0.0, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🍌', 'name' => 'কলা', 'instruction' => 'সরাসরি চিবিয়ে', 'timing' => 'সকাল বা ব্যায়ামের পরে', 'unit' => 'পিস', 'category' => 'Meals', 'calories' => 105, 'protein' => 1.3, 'carbs' => 27.0, 'fats' => 0.3, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🥕', 'name' => 'গাজর', 'instruction' => 'কাঁচা চিবিয়ে / জুস করে', 'timing' => 'দুপুরে ভালো', 'unit' => 'পিস', 'category' => 'General', 'calories' => 41, 'protein' => 0.9, 'carbs' => 10.0, 'fats' => 0.2, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🥒', 'name' => 'শসা', 'instruction' => 'কাঁচা চিবিয়ে / সালাদ', 'timing' => 'দুপুরে', 'unit' => 'পিস', 'category' => 'General', 'calories' => 15, 'protein' => 0.7, 'carbs' => 3.6, 'fats' => 0.1, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🌿', 'name' => 'জিনসিং', 'instruction' => 'গুঁড়া হলে কুসুম গরম পানিতে', 'timing' => 'সকালে', 'unit' => 'চামচ', 'category' => 'Supplements', 'calories' => 5, 'protein' => 0.0, 'carbs' => 1.0, 'fats' => 0.0, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🍎', 'name' => 'বেদানা', 'instruction' => 'দানা চিবিয়ে বা জুস', 'timing' => 'সকাল/দুপুর', 'unit' => 'পিস', 'category' => 'General', 'calories' => 83, 'protein' => 1.7, 'carbs' => 19.0, 'fats' => 1.2, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🌴', 'name' => 'খেজুর (আজওয়া/সুকারি/মরিয়ম)', 'instruction' => '২–৩টা সরাসরি', 'timing' => 'সকালে', 'unit' => 'পিস', 'category' => 'Meals', 'calories' => 66, 'protein' => 0.4, 'carbs' => 18.0, 'fats' => 0.0, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🥚', 'name' => 'হাঁসের ডিম', 'instruction' => 'সেদ্ধ করে (দিনে ১টা যথেষ্ট)', 'timing' => 'যেকোনো সময়', 'unit' => 'পিস', 'category' => 'Meals', 'calories' => 130, 'protein' => 9.0, 'carbs' => 1.0, 'fats' => 9.6, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🍯', 'name' => 'মধু', 'instruction' => 'কুসুম গরম পানিতে', 'timing' => 'সকাল বা রাতে', 'unit' => 'চামচ', 'category' => 'General', 'calories' => 64, 'protein' => 0.1, 'carbs' => 17.0, 'fats' => 0.0, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🌱', 'name' => 'চিয়া সিড', 'instruction' => '১ চামচ ১ গ্লাস পানিতে ২০–৩০ মিনিট ভিজিয়ে', 'timing' => 'যেকোনো সময়', 'unit' => 'চামচ', 'category' => 'Supplements', 'calories' => 58, 'protein' => 2.0, 'carbs' => 5.0, 'fats' => 4.0, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🌱', 'name' => 'তকমা', 'instruction' => '১৫–২০ মিনিট ভিজিয়ে', 'timing' => 'যেকোনো সময়', 'unit' => 'চামচ', 'category' => 'Supplements', 'calories' => 20, 'protein' => 0.5, 'carbs' => 4.0, 'fats' => 0.1, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🌿', 'name' => 'কালোজিরা', 'instruction' => '½ চা চামচ চিবিয়ে বা মধুর সাথে', 'timing' => 'যেকোনো সময়', 'unit' => 'চামচ', 'category' => 'Supplements', 'calories' => 7, 'protein' => 0.3, 'carbs' => 1.0, 'fats' => 0.5, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🌾', 'name' => 'ইসবগুল', 'instruction' => '১ চামচ পানিতে মিশিয়ে সাথে সাথে (আলাদা)', 'timing' => 'রাতে', 'unit' => 'চামচ', 'category' => 'Medication', 'calories' => 15, 'protein' => 0.0, 'carbs' => 4.0, 'fats' => 0.0, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🍇', 'name' => 'কিসমিস', 'instruction' => 'রাতে ভিজিয়ে', 'timing' => 'সকালে', 'unit' => 'পিস', 'category' => 'General', 'calories' => 2, 'protein' => 0.1, 'carbs' => 0.5, 'fats' => 0.0, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🪨', 'name' => 'শিলাজিত', 'instruction' => 'মটর দানার সমান কুসুম গরম পানিতে', 'timing' => 'যেকোনো সময়', 'unit' => 'গ্রাম', 'category' => 'Supplements', 'calories' => 0, 'protein' => 0.0, 'carbs' => 0.0, 'fats' => 0.0, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🌰', 'name' => 'তালমাখনা', 'instruction' => 'হালকা ভেজে', 'timing' => 'বিকেল/রাতে', 'unit' => 'চামচ', 'category' => 'Supplements', 'calories' => 15, 'protein' => 0.8, 'carbs' => 2.0, 'fats' => 0.5, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🌰', 'name' => 'আখরোট', 'instruction' => '২টা রাতে ভিজিয়ে', 'timing' => 'সকালে', 'unit' => 'পিস', 'category' => 'Meals', 'calories' => 52, 'protein' => 1.2, 'carbs' => 1.1, 'fats' => 5.0, 'is_active' => true, 'low_stock_threshold' => 5],
            ['icon' => '🌿', 'name' => 'মেথি পাউডার', 'instruction' => '½ চা চামচ পানিতে ভিজিয়ে', 'timing' => 'সকালে', 'unit' => 'চামচ', 'category' => 'Supplements', 'calories' => 12, 'protein' => 0.8, 'carbs' => 2.0, 'fats' => 0.2, 'is_active' => true, 'low_stock_threshold' => 5],
        ];
    }

    public function run(): void
    {
        // Handled dynamically during registration/installation
    }
}