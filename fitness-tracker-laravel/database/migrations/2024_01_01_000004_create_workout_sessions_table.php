<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('session_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('workout_id');
            $table->index('user_id');
            $table->index('session_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_sessions');
    }
};

