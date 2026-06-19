<?php

namespace App\Console\Commands;

use App\Models\FlujoEjecucion;
use App\Models\FlujoPasoAsignacion;
use App\Models\FlujoPasoEjecutor;
use App\Models\FlujoTrabajo;
use App\Models\LogAuditoria;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SeedTestFlows extends Command
{
    protected $signature = 'app:seed-test-flows';
    protected $description = 'Crea flujos de prueba con ejecuciones completadas para verificar reportes';

    public function handle()
    {
        $user = User::whereHas('role', fn($q) => $q->whereIn('slug', ['super_admin', 'administrador']))->first();
        if (!$user) {
            $this->error('No hay usuarios admin/super_admin. Ejecuta primero app:create-test-users.');
            return 1;
        }

        $now = Carbon::now();

        for ($i = 1; $i <= 3; $i++) {
            $flujo = FlujoTrabajo::create([
                'user_id' => $user->id,
                'codigo' => 'TEST-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
                'nombre' => "Flujo de prueba #{$i} — " . ($i === 1 ? 'Solicitud de permisos' : ($i === 2 ? 'Aprobación de documentos' : 'Revisión de informes')),
                'departamento' => 'General',
                'estado' => 'Completado',
                'fecha_completado' => $now->subDays($i - 1),
                'pasos' => [
                    ['nombre' => 'Recepción', 'descripcion' => 'Recibir la solicitud', 'checklist' => [['item' => 'Verificar datos del solicitante'], ['item' => 'Confirmar documentación completa']]],
                    ['nombre' => 'Validación', 'descripcion' => 'Validar la información', 'checklist' => [['item' => 'Cruzar datos con el sistema'], ['item' => 'Verificar requisitos']]],
                    ['nombre' => 'Aprobación final', 'descripcion' => 'Aprobar o rechazar', 'checklist' => [['item' => 'Revisar informe final'], ['item' => 'Emitir resolución']]],
                ],
                'diseno' => ['version' => 'v1.0', 'trigger_evento' => '', 'trigger_descripcion' => ''],
            ]);

            $ejecucion = FlujoEjecucion::create([
                'flujo_trabajo_id' => $flujo->id,
                'flujo_codigo' => $flujo->codigo,
                'flujo_nombre' => $flujo->nombre,
                'estado' => 'completada',
                'paso_actual_index' => 2,
                'created_at' => $now->subDays($i + 2),
                'updated_at' => $now->subDays($i - 1),
            ]);

            $pasosData = [
                ['nombre' => 'Recepción', 'dias' => 2, 'limite_h' => 48],
                ['nombre' => 'Validación', 'dias' => 1, 'limite_h' => 24],
                ['nombre' => 'Aprobación final', 'dias' => 0, 'limite_h' => 12],
            ];

            foreach ($pasosData as $pi => $pd) {
                $completadoEn = (clone $now)->subDays($i - 1)->subDays(2 - $pi);
                $fechaLimite = (clone $now)->subDays($i + 2)->addHours($pd['limite_h']);

                $asignacion = FlujoPasoAsignacion::create([
                    'flujo_ejecucion_id' => $ejecucion->id,
                    'paso_index' => $pi,
                    'paso_nombre' => $pd['nombre'],
                    'asignado_a' => $user->id,
                    'estado' => 'completado',
                    'fecha_limite' => $fechaLimite,
                    'fecha_completado' => $completadoEn,
                    'completado_por' => $user->id,
                    'revisor_id' => $user->id,
                    'revision_estado' => 'aprobado',
                    'revisado_por' => $user->id,
                    'revisado_en' => $completadoEn,
                    'created_at' => (clone $now)->subDays($i + 2),
                    'updated_at' => $completadoEn,
                ]);

                FlujoPasoEjecutor::create([
                    'flujo_paso_asignacion_id' => $asignacion->id,
                    'user_id' => $user->id,
                    'estado' => 'completado',
                    'completado_en' => $completadoEn,
                    'created_at' => (clone $now)->subDays($i + 2),
                    'updated_at' => $completadoEn,
                ]);
            }

            LogAuditoria::registrar('crear', FlujoTrabajo::class, $flujo->id,
                "Flujo de prueba «{$flujo->nombre}» creado",
                ['codigo' => $flujo->codigo, 'nombre' => $flujo->nombre, 'departamento' => 'General', 'estado' => 'Activo']
            );

            LogAuditoria::registrar('disenar_flujo', FlujoTrabajo::class, $flujo->id,
                "Diseño del flujo «{$flujo->nombre}» guardado con 3 pasos"
            );

            $this->info("Flujo '{$flujo->codigo}' creado con ejecución completada.");
        }

        $this->info("\n--- Datos de prueba creados ---");
        $this->info("3 flujos con ejecuciones completadas en los últimos días.");
        $this->info("Ve a Reportes y genera un reporte semanal o mensual para verificarlo.");
        $this->info("Luego puedes eliminar los flujos desde el Diseñador y el reporte SEGUIRÁ mostrando las ejecuciones.");

        return 0;
    }
}
