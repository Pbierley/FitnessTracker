<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token')->unique();
            $table->dateTime('expires_at');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('token');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_tokens');
    }
};

