<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->date('date');
            $table->string('exercise_name'); // e.g., Walking, Gym, Running
            $table->integer('duration_minutes');
            $table->integer('calories_burned')->nullable(); // Optional
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_logs');
    }
};