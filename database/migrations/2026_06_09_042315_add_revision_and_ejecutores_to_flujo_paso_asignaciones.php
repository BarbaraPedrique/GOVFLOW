<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flujo_paso_asignaciones', function (Blueprint $table) {
            $table->foreignId('revisor_id')->nullable()->after('completado_por')->constrained('users')->nullOnDelete();
            $table->enum('revision_estado', ['pendiente', 'en_revision', 'aprobado', 'rechazado'])->default('pendiente')->after('revisor_id');
            $table->text('revision_comentario')->nullable()->after('revision_estado');
            $table->foreignId('revisado_por')->nullable()->after('revision_comentario')->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_en')->nullable()->after('revisado_por');
        });

        Schema::create('flujo_paso_ejecutores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flujo_paso_asignacion_id')->constrained('flujo_paso_asignaciones')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('estado', ['pendiente', 'completado'])->default('pendiente');
            $table->timestamp('completado_en')->nullable();
            $table->string('archivo')->nullable();
            $table->text('mensaje')->nullable();
            $table->timestamps();
            $table->unique(['flujo_paso_asignacion_id', 'user_id'], 'paso_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flujo_paso_ejecutores');

        Schema::table('flujo_paso_asignaciones', function (Blueprint $table) {
            $table->dropForeign(['revisor_id']);
            $table->dropForeign(['revisado_por']);
            $table->dropColumn([
                'revisor_id',
                'revision_estado',
                'revision_comentario',
                'revisado_por',
                'revisado_en',
            ]);
        });
    }
};
