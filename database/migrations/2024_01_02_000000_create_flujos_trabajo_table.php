<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flujos_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // WF-1024
            $table->string('nombre');
            $table->string('departamento');
            $table->enum('estado', ['Activo', 'Borrador', 'Completado', 'Pausado'])->default('Borrador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flujos_trabajo');
    }
};
