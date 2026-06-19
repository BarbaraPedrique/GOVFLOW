<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flujo_ejecuciones', function (Blueprint $table) {
            $table->dropForeign(['flujo_trabajo_id']);
            $table->foreignId('flujo_trabajo_id')->nullable()->change();
            $table->foreign('flujo_trabajo_id')->references('id')->on('flujos_trabajo')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('flujo_ejecuciones', function (Blueprint $table) {
            $table->dropForeign(['flujo_trabajo_id']);
            $table->foreignId('flujo_trabajo_id')->change();
            $table->foreign('flujo_trabajo_id')->references('id')->on('flujos_trabajo')->cascadeOnDelete();
        });
    }
};
