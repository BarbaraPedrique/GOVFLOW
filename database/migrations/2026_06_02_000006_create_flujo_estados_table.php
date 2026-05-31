<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flujo_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flujo_trabajo_id')->constrained('flujos_trabajo')->cascadeOnDelete();
            $table->string('nombre');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->json('actores')->nullable();
            $table->json('actividades')->nullable();
            $table->json('reglas')->nullable();
            $table->json('rutas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flujo_estados');
    }
};
