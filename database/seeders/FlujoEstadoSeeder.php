<?php

namespace Database\Seeders;

use App\Models\FlujoEstado;
use App\Models\FlujoTrabajo;
use Illuminate\Database\Seeder;

class FlujoEstadoSeeder extends Seeder
{
    public function run(): void
    {
        $flujos = FlujoTrabajo::all();

        $estadosPorFlujo = [
            [
                'nombre' => 'Solicitud Inicial',
                'orden' => 1,
                'actores' => [
                    ['tipo' => 'usuario', 'nombre' => 'Juan Pérez', 'rol' => 'Administrador', 'foto' => null],
                ],
                'actividades' => [
                    ['nombre' => 'Carga de Expediente Digital', 'descripcion' => 'Subida y ordenamiento de la documentación legal del solicitante', 'tipo' => 'carga_datos'],
                    ['nombre' => 'Subir Borrador', 'descripcion' => 'Subir borrador del documento inicial', 'tipo' => 'subir_documento'],
                ],
                'reglas' => [
                    ['nombre' => 'Restricciones de Gobernanza', 'descripcion' => 'Validar que la documentación cumpla con normativas vigentes', 'tipo' => 'gobernanza'],
                ],
                'rutas' => [
                    ['destino' => 'Revisión Técnica', 'condicion' => 'Documentación completa y validada', 'accion' => 'Enviar a revisión'],
                    ['destino' => 'Rechazado', 'condicion' => 'Documentación incompleta o inválida', 'accion' => 'Rechazar solicitud'],
                ],
            ],
            [
                'nombre' => 'Revisión Técnica',
                'orden' => 2,
                'actores' => [
                    ['tipo' => 'sistema', 'nombre' => 'AI-Engine Core', 'rol' => 'Software Bot'],
                    ['tipo' => 'usuario', 'nombre' => 'Carlos Mendoza', 'rol' => 'Equipo Gerencia', 'foto' => null],
                ],
                'actividades' => [
                    ['nombre' => 'Validación OCR Automática', 'descripcion' => 'Verificación inteligente de identidad y firmas mediante software', 'tipo' => 'revision'],
                    ['nombre' => 'Checklist de Calidad', 'descripcion' => 'Verificación de lista de requisitos de calidad', 'tipo' => 'checklist'],
                ],
                'reglas' => [
                    ['nombre' => 'Validaciones de Negocio', 'descripcion' => 'Verificar reglas de negocio aplicables al caso', 'tipo' => 'negocio'],
                    ['nombre' => 'Restricciones de Gobernanza', 'descripcion' => 'Cumplimiento de normativas sectoriales', 'tipo' => 'gobernanza'],
                ],
                'rutas' => [
                    ['destino' => 'Auditoría y Firma', 'condicion' => 'Validación técnica superada', 'accion' => 'Aprobar revisión'],
                    ['destino' => 'Rechazado', 'condicion' => 'No cumple requisitos técnicos', 'accion' => 'Rechazar'],
                ],
            ],
            [
                'nombre' => 'Auditoría y Firma',
                'orden' => 3,
                'actores' => [
                    ['tipo' => 'usuario', 'nombre' => 'Carlos Mendoza', 'rol' => 'Gerente', 'foto' => null],
                ],
                'actividades' => [
                    ['nombre' => 'Auditoría Final', 'descripcion' => 'Aprobación técnica definitiva y firma electrónica para la emisión', 'tipo' => 'revision'],
                    ['nombre' => 'Checklist de Calidad', 'descripcion' => 'Verificación final de checklist de calidad', 'tipo' => 'checklist'],
                ],
                'reglas' => [
                    ['nombre' => 'Restricciones de Gobernanza', 'descripcion' => 'Validación de cumplimiento normativo final', 'tipo' => 'gobernanza'],
                ],
                'rutas' => [
                    ['destino' => 'Notificación y Cierre', 'condicion' => 'Firma electrónica completada', 'accion' => 'Cerrar y notificar'],
                    ['destino' => 'Rechazado', 'condicion' => 'No aprobado en auditoría', 'accion' => 'Rechazar'],
                ],
            ],
            [
                'nombre' => 'Notificación y Cierre',
                'orden' => 4,
                'actores' => [
                    ['tipo' => 'sistema', 'nombre' => 'Módulo de Correo', 'rol' => 'Sistema Mailer'],
                ],
                'actividades' => [
                    ['nombre' => 'Notificación y Envío', 'descripcion' => 'Distribución del producto finalizado mediante canales automatizados', 'tipo' => 'subir_documento'],
                ],
                'reglas' => [],
                'rutas' => [],
            ],
            [
                'nombre' => 'Rechazado',
                'orden' => 5,
                'actores' => [
                    ['tipo' => 'sistema', 'nombre' => 'Sistema de Notificaciones', 'rol' => 'Bot Automático'],
                ],
                'actividades' => [
                    ['nombre' => 'Notificación de Rechazo', 'descripcion' => 'Envío de notificación de rechazo al solicitante', 'tipo' => 'checklist'],
                ],
                'reglas' => [],
                'rutas' => [
                    ['destino' => 'Solicitud Inicial', 'condicion' => 'Solicitante corrige documentación', 'accion' => 'Reintentar'],
                ],
            ],
        ];

        foreach ($flujos as $flujo) {
            foreach ($estadosPorFlujo as $estado) {
                FlujoEstado::firstOrCreate(
                    ['flujo_trabajo_id' => $flujo->id, 'nombre' => $estado['nombre']],
                    [
                        'orden' => $estado['orden'],
                        'actores' => $estado['actores'],
                        'actividades' => $estado['actividades'],
                        'reglas' => $estado['reglas'],
                        'rutas' => $estado['rutas'],
                    ]
                );
            }
        }
    }
}
