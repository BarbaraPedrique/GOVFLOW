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
        Schema::table('tareas', function (Blueprint $table) {
            $table->time('hora_inicio')->nullable()->after('fecha_vencimiento');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->integer('receso')->nullable()->after('hora_fin');
        });
    }

    public function down(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio', 'hora_fin', 'receso']);
        });
    }
};
