<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flujos_trabajo', function (Blueprint $table) {
            $table->date('fecha_limite')->nullable()->after('estado');
            $table->date('fecha_completado')->nullable()->after('fecha_limite');
        });
    }

    public function down(): void
    {
        Schema::table('flujos_trabajo', function (Blueprint $table) {
            $table->dropColumn(['fecha_limite', 'fecha_completado']);
        });
    }
};
