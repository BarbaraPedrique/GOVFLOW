<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flujo_ejecuciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flujo_trabajo_id')->constrained('flujos_trabajo')->cascadeOnDelete();
            $table->enum('estado', ['en_espera', 'en_progreso', 'completada', 'rechazada'])->default('en_espera');
            $table->unsignedSmallInteger('paso_actual_index')->default(0);
            $table->timestamps();
        });

        Schema::create('flujo_paso_asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flujo_ejecucion_id')->constrained('flujo_ejecuciones')->cascadeOnDelete();
            $table->unsignedSmallInteger('paso_index');
            $table->string('paso_nombre');
            $table->foreignId('asignado_a')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completado', 'rechazado'])->default('pendiente');
            $table->dateTime('fecha_limite')->nullable();
            $table->dateTime('fecha_completado')->nullable();
            $table->string('archivo')->nullable();
            $table->text('mensaje')->nullable();
            $table->foreignId('completado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flujo_paso_asignaciones');
        Schema::dropIfExists('flujo_ejecuciones');
    }
};
