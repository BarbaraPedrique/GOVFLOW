<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('gerente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('equipo_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('rol', ['lider_equipo', 'empleado', 'administrador', 'gerente'])->default('empleado');
            $table->timestamps();

            $table->unique(['equipo_id', 'user_id']);
        });

        Schema::table('tareas', function (Blueprint $table) {
            $table->foreignId('equipo_id')->nullable()->after('user_id')->constrained('equipos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropForeign(['equipo_id']);
            $table->dropColumn('equipo_id');
        });

        Schema::dropIfExists('equipo_user');
        Schema::dropIfExists('equipos');
    }
};
