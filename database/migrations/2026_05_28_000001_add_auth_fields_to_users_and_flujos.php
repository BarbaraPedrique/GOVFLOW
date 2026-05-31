<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'super_admin',   'slug' => 'super_admin',   'display_name' => 'Super Admin',      'description' => 'Acceso total al sistema, puede gestionar roles'],
            ['name' => 'administrador', 'slug' => 'administrador', 'display_name' => 'Administrador',    'description' => 'Acceso total al sistema excepto gestión de roles'],
            ['name' => 'gerente',       'slug' => 'gerente',       'display_name' => 'Gerente',          'description' => 'Gestiona flujos de trabajo'],
            ['name' => 'lider_equipo',  'slug' => 'lider_equipo',  'display_name' => 'Líder de Equipo',  'description' => 'Supervisa equipos de empleados'],
            ['name' => 'empleado',      'slug' => 'empleado',      'display_name' => 'Empleado',         'description' => 'Usuario consultor'],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->string('status')->default('pendiente')->after('role_id');
            $table->string('apodo')->nullable()->after('name');
            $table->date('fecha_nacimiento')->nullable()->after('email');
            $table->text('descripcion')->nullable()->after('fecha_nacimiento');
            $table->string('foto')->nullable()->after('descripcion');
        });

        Schema::create('flujos_trabajo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('departamento');
            $table->enum('estado', ['Activo', 'Borrador', 'Completado', 'Pausado'])->default('Borrador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flujos_trabajo');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'status', 'apodo', 'fecha_nacimiento', 'descripcion', 'foto']);
        });

        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
