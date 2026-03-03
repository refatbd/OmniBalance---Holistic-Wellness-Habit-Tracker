<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->date('date');
            
            $table->boolean('is_fasting')->default(false);
            $table->decimal('sleep_hours', 4, 1)->nullable(); // e.g., 7.5
            $table->string('mood')->nullable(); // e.g., 'Great', 'Good', 'Okay', 'Bad'
            $table->text('notes')->nullable(); // Daily journal
            
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_metrics');
    }
};