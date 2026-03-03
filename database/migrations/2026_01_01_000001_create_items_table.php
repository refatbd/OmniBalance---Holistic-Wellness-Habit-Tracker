<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            // Explicitly define the column type and constraint
            $table->unsignedBigInteger('user_id'); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('icon')->nullable();
            $table->string('name');
            $table->string('instruction')->nullable();
            $table->string('timing')->nullable();
            $table->integer('stock')->default(0);
            $table->string('unit')->default('পিস');
            
            // --- NEW: Added for low stock alert feature ---
            $table->integer('low_stock_threshold')->default(5);
            $table->timestamp('last_low_stock_alert_at')->nullable(); // Prevents spamming alerts every minute
            
            // --- NEW: Added for enable/disable feature ---
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};