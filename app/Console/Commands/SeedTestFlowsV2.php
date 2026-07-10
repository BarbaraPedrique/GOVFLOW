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

class SeedTestFlowsV2 extends Command
{
    protected $signature = 'app:seed-test-flows-v2 {--user_id= : ID del usuario admin}';
    protected $description = 'Crea 2 flujos de prueba completados para verificar la relacion 1/1';

    public function handle()
    {
        $userId = $this->option('user_id');
        $admin = $userId
            ? User::find($userId)
            : User::whereHas('role', fn($q) => $q->whereIn('slug', ['super_admin', 'administrador']))->first();

        if (!$admin) {
            $this->error('No se encontro un usuario admin/super_admin.');
            return 1;
        }

        $now = Carbon::now();

        $flows = [
            [
                'codigo' => 'TEST-01',
                'nombre' => 'Solicitud de Vacaciones',
                'pasos' => [
                    ['nombre' => 'Aprobar vacaciones', 'descripcion' => 'Revisar y aprobar la solicitud de vacaciones del empleado'],
                ],
            ],
            [
                'codigo' => 'TEST-02',
                'nombre' => 'Compra de Suministros',
                'pasos' => [
                    ['nombre' => 'Validar factura', 'descripcion' => 'Revisar la factura y autorizar el pago'],
                ],
            ],
        ];

        foreach ($flows as $fi) {
            $flujo = FlujoTrabajo::create([
                'user_id' => $admin->id,
                'codigo' => $fi['codigo'],
                'nombre' => $fi['nombre'],
                'departamento' => 'General',
                'estado' => 'Completado',
                'fecha_completado' => $now,
                'pasos' => $fi['pasos'],
                'diseno' => ['version' => 'v1.0', 'trigger_evento' => '', 'trigger_descripcion' => ''],
            ]);

            $ejecucion = FlujoEjecucion::create([
                'flujo_trabajo_id' => $flujo->id,
                'flujo_codigo' => $flujo->codigo,
                'flujo_nombre' => $flujo->nombre,
                'estado' => 'completada',
                'paso_actual_index' => 0,
            ]);

            foreach ($fi['pasos'] as $pi => $pd) {
                $asignacion = FlujoPasoAsignacion::create([
                    'flujo_ejecucion_id' => $ejecucion->id,
                    'paso_index' => $pi,
                    'paso_nombre' => $pd['nombre'],
                    'asignado_a' => $admin->id,
                    'estado' => 'completado',
                    'fecha_limite' => (clone $now)->addDay(),
                    'fecha_completado' => $now,
                    'completado_por' => $admin->id,
                    'revisor_id' => $admin->id,
                    'revision_estado' => 'aprobado',
                    'revisado_por' => $admin->id,
                    'revisado_en' => $now,
                ]);

                FlujoPasoEjecutor::create([
                    'flujo_paso_asignacion_id' => $asignacion->id,
                    'user_id' => $admin->id,
                    'estado' => 'completado',
                    'completado_en' => $now,
                ]);
            }

            $this->info("Flujo '{$flujo->codigo}' creado con 1 paso (1/1).");
        }

        $this->info("\n--- Listo! ---");
        $this->info("Ve a Flujos de Trabajo > Mis Flujos para verificar la relacion 1/1.");

        return 0;
    }
}
