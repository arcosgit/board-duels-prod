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
        Schema::create('game_accepts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2_id')->constrained('users')->onDelete('cascade');
            $table->string('user1_status')->default('pending');
            $table->string('user2_status')->default('pending');
            $table->string('time');
            $table->string('size_board');
            $table->string('user1_current')->default('x');
            $table->string('user2_current')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_accepts');
    }
};
