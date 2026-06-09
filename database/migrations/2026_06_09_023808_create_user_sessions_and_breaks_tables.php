<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('logged_in_at')->nullable();
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamps();
        });

        Schema::create('session_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_session_id')->constrained('user_sessions')->cascadeOnDelete();
            $table->timestamp('break_start')->nullable();
            $table->timestamp('break_end')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_breaks');
        Schema::dropIfExists('user_sessions');
    }
};
