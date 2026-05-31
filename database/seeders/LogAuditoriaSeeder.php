<?php

namespace Database\Seeders;

use App\Models\FlujoTrabajo;
use App\Models\LogAuditoria;
use App\Models\User;
use Illuminate\Database\Seeder;

class LogAuditoriaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@govflow.com')->first();
        $test = User::where('email', 'test@example.com')->first();
        $users = [$admin, $test];
        $flujos = FlujoTrabajo::all();

        $acciones = ['crear', 'actualizar', 'actualizar', 'actualizar', 'eliminar'];

        foreach ($flujos as $flujo) {
            $user = $users[array_rand($users)];

            LogAuditoria::create([
                'user_id' => $user?->id,
                'accion' => 'crear',
                'entidad_type' => 'App\Models\FlujoTrabajo',
                'entidad_id' => $flujo->id,
                'descripcion' => "Creación del flujo {$flujo->codigo}: {$flujo->nombre}",
                'metadata' => [
                    'nombre' => $flujo->nombre,
                    'departamento' => $flujo->departamento,
                    'estado_inicial' => 'Borrador',
                ],
                'created_at' => now()->subDays(rand(30, 60)),
            ]);

            foreach (['nombre', 'departamento', 'estado'] as $i => $campo) {
                if (rand(0, 1)) {
                    $userAlt = $users[array_rand($users)];
                    LogAuditoria::create([
                        'user_id' => $userAlt?->id,
                        'accion' => 'actualizar',
                        'entidad_type' => 'App\Models\FlujoTrabajo',
                        'entidad_id' => $flujo->id,
                        'descripcion' => "Actualización de {$campo} en {$flujo->codigo}",
                        'metadata' => [
                            $campo => [
                                'old' => $campo === 'estado' ? 'Borrador' : 'Valor anterior',
                                'new' => $campo === 'estado' ? $flujo->estado : $flujo->$campo,
                            ],
                        ],
                        'created_at' => now()->subDays(rand(1, 29)),
                    ]);
                }
            }

            LogAuditoria::create([
                'user_id' => $user?->id,
                'accion' => 'actualizar',
                'entidad_type' => 'App\Models\FlujoTrabajo',
                'entidad_id' => $flujo->id,
                'descripcion' => "Asignación de actor al estado Solicitud Inicial",
                'metadata' => [
                    'actor_asignado' => ['old' => 'Sin asignar', 'new' => $user?->name ?? 'Admin'],
                    'estado' => 'Solicitud Inicial',
                ],
                'created_at' => now()->subDays(rand(1, 15)),
            ]);

            if ($flujo->estado === 'Completado') {
                LogAuditoria::create([
                    'user_id' => $user?->id,
                    'accion' => 'actualizar',
                    'entidad_type' => 'App\Models\FlujoTrabajo',
                    'entidad_id' => $flujo->id,
                    'descripcion' => "Flujo {$flujo->codigo} completado",
                    'metadata' => [
                        'estado' => ['old' => 'Activo', 'new' => 'Completado'],
                        'fecha_completado' => $flujo->fecha_completado?->format('Y-m-d'),
                    ],
                    'created_at' => $flujo->fecha_completado?->startOfDay() ?? now()->subDays(5),
                ]);
            }
        }

        for ($i = 0; $i < 5; $i++) {
            $user = $users[array_rand($users)];
            LogAuditoria::create([
                'user_id' => $user?->id,
                'accion' => 'crear',
                'entidad_type' => 'App\Models\Tarea',
                'entidad_id' => null,
                'descripcion' => 'Creación de tarea de prueba',
                'metadata' => ['titulo' => 'Tarea generada automáticamente'],
                'created_at' => now()->subHours(rand(1, 72)),
            ]);
        }

        LogAuditoria::create([
            'user_id' => $admin?->id,
            'accion' => 'actualizar',
            'entidad_type' => 'App\Models\User',
            'entidad_id' => $admin?->id,
            'descripcion' => 'Actualización de perfil de usuario',
            'metadata' => [
                'apodo' => ['old' => '', 'new' => 'AdminPrincipal'],
                'descripcion' => ['old' => '', 'new' => 'Administrador del sistema GOVFLOW'],
            ],
            'created_at' => now()->subDays(2),
        ]);
    }
}
