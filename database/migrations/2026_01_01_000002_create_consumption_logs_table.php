<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumption_logs', function (Blueprint $table) {
            $table->id();
            
            // Explicitly define the column types and constraints
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            
            $table->date('consumed_date');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'item_id', 'consumed_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumption_logs');
    }
};