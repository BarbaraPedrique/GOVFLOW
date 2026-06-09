<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE equipo_user MODIFY COLUMN rol ENUM('lider_equipo', 'empleado', 'administrador', 'gerente') NOT NULL DEFAULT 'empleado'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE equipo_user MODIFY COLUMN rol ENUM('lider_equipo', 'empleado') NOT NULL DEFAULT 'empleado'");
    }
};
