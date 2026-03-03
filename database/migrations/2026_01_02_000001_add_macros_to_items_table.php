<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Adding columns for macronutrients per unit/serving
            // Using decimal for precision in nutritional values
            $table->decimal('calories', 8, 2)->default(0)->after('unit');
            $table->decimal('protein', 8, 2)->default(0)->after('calories');
            $table->decimal('carbs', 8, 2)->default(0)->after('protein');
            $table->decimal('fats', 8, 2)->default(0)->after('carbs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['calories', 'protein', 'carbs', 'fats']);
        });
    }
};