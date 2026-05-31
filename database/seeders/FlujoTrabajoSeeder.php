<?php

namespace Database\Seeders;

use App\Models\FlujoTrabajo;
use App\Models\User;
use Illuminate\Database\Seeder;

class FlujoTrabajoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@govflow.com')->first();
        $test = User::where('email', 'test@example.com')->first();

        $flujos = [
            [
                'codigo' => 'WF-1024',
                'nombre' => 'Aprobación de Presupuesto Q3',
                'departamento' => 'Finanzas',
                'estado' => 'Activo',
                'user_id' => $admin?->id,
                'fecha_limite' => '2026-07-15',
                'fecha_completado' => null,
            ],
            [
                'codigo' => 'WF-1025',
                'nombre' => 'Contratación Personal IT',
                'departamento' => 'Recursos Humanos',
                'estado' => 'Completado',
                'user_id' => $admin?->id,
                'fecha_limite' => '2026-04-30',
                'fecha_completado' => '2026-04-25',
            ],
            [
                'codigo' => 'WF-1026',
                'nombre' => 'Revisión Legal Contratos',
                'departamento' => 'Legal',
                'estado' => 'Completado',
                'user_id' => $test?->id,
                'fecha_limite' => '2026-05-15',
                'fecha_completado' => '2026-05-20',
            ],
            [
                'codigo' => 'WF-1027',
                'nombre' => 'Adquisición de Equipos',
                'departamento' => 'Operaciones',
                'estado' => 'Activo',
                'user_id' => $test?->id,
                'fecha_limite' => '2026-08-01',
                'fecha_completado' => null,
            ],
            [
                'codigo' => 'WF-1028',
                'nombre' => 'Auditoría Anual',
                'departamento' => 'Cumplimiento',
                'estado' => 'Completado',
                'user_id' => $admin?->id,
                'fecha_limite' => '2026-03-01',
                'fecha_completado' => '2026-02-28',
            ],
            [
                'codigo' => 'WF-1029',
                'nombre' => 'Implementación Sistema Seguridad',
                'departamento' => 'TI',
                'estado' => 'Completado',
                'user_id' => $admin?->id,
                'fecha_limite' => '2026-05-01',
                'fecha_completado' => '2026-04-28',
            ],
            [
                'codigo' => 'WF-1030',
                'nombre' => 'Capacitación Personal Nuevo',
                'departamento' => 'Recursos Humanos',
                'estado' => 'Completado',
                'user_id' => $test?->id,
                'fecha_limite' => '2026-06-10',
                'fecha_completado' => '2026-06-15',
            ],
            [
                'codigo' => 'WF-1031',
                'nombre' => 'Migración de Servidores',
                'departamento' => 'TI',
                'estado' => 'Pausado',
                'user_id' => $admin?->id,
                'fecha_limite' => '2026-09-01',
                'fecha_completado' => null,
            ],
        ];

        foreach ($flujos as $flujo) {
            FlujoTrabajo::firstOrCreate(['codigo' => $flujo['codigo']], $flujo);
        }
    }
}
