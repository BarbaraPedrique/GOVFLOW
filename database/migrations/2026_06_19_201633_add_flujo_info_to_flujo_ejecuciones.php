<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flujo_ejecuciones', function (Blueprint $table) {
            $table->string('flujo_codigo')->nullable()->after('flujo_trabajo_id');
            $table->string('flujo_nombre')->nullable()->after('flujo_codigo');
        });
    }

    public function down(): void
    {
        Schema::table('flujo_ejecuciones', function (Blueprint $table) {
            $table->dropColumn(['flujo_codigo', 'flujo_nombre']);
        });
    }
};
