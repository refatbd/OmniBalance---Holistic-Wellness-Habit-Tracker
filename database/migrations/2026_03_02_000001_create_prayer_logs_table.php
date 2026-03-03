<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->date('date');
            
            // Boolean columns for each prayer (0 = not prayed, 1 = prayed)
            $table->boolean('fajr')->default(false);
            $table->boolean('dhuhr')->default(false);
            $table->boolean('asr')->default(false);
            $table->boolean('maghrib')->default(false);
            $table->boolean('isha')->default(false);
            $table->boolean('tahajjud')->default(false);
            
            $table->timestamps();

            // A user should only have one prayer record per day
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_logs');
    }
};