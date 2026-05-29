<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('accion');
            $table->string('entidad_type');
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->text('descripcion');
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entidad_type', 'entidad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_auditoria');
    }
};
