<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flujos_trabajo', function (Blueprint $table) {
            $table->json('diseno')->nullable()->after('pasos');
        });
    }

    public function down(): void
    {
        Schema::table('flujos_trabajo', function (Blueprint $table) {
            $table->dropColumn('diseno');
        });
    }
};
