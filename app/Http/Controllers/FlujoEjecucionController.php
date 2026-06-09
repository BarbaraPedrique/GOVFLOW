<?php

namespace App\Http\Controllers;

use App\Models\FlujoEjecucion;
use App\Models\FlujoPasoAsignacion;
use App\Models\FlujoPasoEjecutor;
use App\Models\Notificacion;
use App\Models\Tarea;
use App\Models\FlujoTrabajo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlujoEjecucionController extends Controller
{
    public function iniciar($flujoId)
    {
        $flujo = FlujoTrabajo::findOrFail($flujoId);

        // Only super_admin, administrador, and gerente can start flows
        $roleSlug = Auth::user()->role?->slug;
        if (!in_array($roleSlug, ['super_admin', 'administrador', 'gerente'])) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para iniciar flujos.'], 403);
            }
            return back()->with('error', 'No tienes permiso para iniciar flujos.');
        }

        // Prevent duplicate ejecuciones
        if (FlujoEjecucion::where('flujo_trabajo_id', $flujo->id)
            ->whereIn('estado', ['en_progreso', 'completada'])
            ->exists()) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Este flujo ya está en ejecución.'], 400);
            }
            return back()->with('error', 'Este flujo ya está en ejecución.');
        }

        $pasos = $flujo->pasos ?? [];
        if (empty($pasos)) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'El flujo no tiene pasos definidos. Guarda el diseño primero.'], 400);
            }
            return back()->with('error', 'El flujo no tiene pasos definidos.');
        }

        // Validate every step has a revisor
        foreach ($pasos as $i => $paso) {
            if (empty($paso['revisor_id'])) {
                $errMsg = "El paso " . ($i + 1) . " ('{$paso['nombre']}') no tiene un revisor asignado. Todos los pasos requieren un revisor.";
                if (request()->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errMsg], 400);
                }
                return back()->with('error', $errMsg);
            }
        }

        // Validate last step reviewer is a team admin/gerente or global admin+
        $ultimoPaso = !empty($pasos) ? $pasos[count($pasos) - 1] : null;
        if ($ultimoPaso && !empty($ultimoPaso['revisor_id'])) {
            $ultimoRevisorId = (int) $ultimoPaso['revisor_id'];
            $revisor = User::with('role')->find($ultimoRevisorId);
            $revisorRole = $revisor?->role?->slug;

            // Global admin+ can always review last step
            $esAdminGlobal = in_array($revisorRole, ['super_admin', 'administrador']);

            // Check team-level roles
            $esAdminEquipo = false;
            if ($flujo->equipo_id && $revisor) {
                $pivotRol = \DB::table('equipo_user')
                    ->where('equipo_id', $flujo->equipo_id)
                    ->where('user_id', $ultimoRevisorId)
                    ->value('rol');
                $esAdminEquipo = in_array($pivotRol, ['administrador', 'gerente']);
            }

            if (!$esAdminGlobal && !$esAdminEquipo) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "El último paso debe ser revisado por un administrador o gerente del equipo."
                    ], 400);
                }
                return back()->with('error', "El último paso debe ser revisado por un administrador o gerente del equipo.");
            }
        }

        $ejecucion = FlujoEjecucion::create([
            'flujo_trabajo_id' => $flujo->id,
            'estado' => 'en_progreso',
            'paso_actual_index' => 0,
        ]);

        foreach ($pasos as $i => $paso) {
            $fechaLimite = null;
            if (!empty($paso['fecha_limite_horas'])) {
                $fechaLimite = now()->addHours((int) $paso['fecha_limite_horas']);
            }

            // Determine assignees
            $asignadosIds = $paso['asignados_ids'] ?? [];
            if (empty($asignadosIds) && !empty($paso['asignacion_usuario_id'])) {
                $asignadosIds = [(int) $paso['asignacion_usuario_id']];
            }
            if (empty($asignadosIds) && !empty($paso['asignacion_rol'])) {
                $userRol = \App\Models\User::whereHas('role', fn($q) => $q->where('slug', $paso['asignacion_rol']))
                    ->inRandomOrder()->first();
                if ($userRol) $asignadosIds = [$userRol->id];
            }

            $asignadoA = !empty($asignadosIds) ? $asignadosIds[0] : null;

            $revisorId = !empty($paso['revisor_id']) ? (int) $paso['revisor_id'] : null;

            $pasoAsignacion = FlujoPasoAsignacion::create([
                'flujo_ejecucion_id' => $ejecucion->id,
                'paso_index' => $i,
                'paso_nombre' => $paso['nombre'] ?? 'Paso ' . ($i + 1),
                'asignado_a' => $asignadoA,
                'revisor_id' => $revisorId,
                'revision_estado' => 'pendiente',
                'estado' => $i === 0 ? 'en_progreso' : 'pendiente',
                'fecha_limite' => $fechaLimite,
            ]);

            // Create ejecutores for each assignee
            foreach ($asignadosIds as $uid) {
                FlujoPasoEjecutor::create([
                    'flujo_paso_asignacion_id' => $pasoAsignacion->id,
                    'user_id' => $uid,
                    'estado' => ($i === 0) ? 'pendiente' : 'pendiente',
                ]);
            }

            // Notify first step assignees
            if ($i === 0 && !empty($asignadosIds)) {
                $tiempo = $paso['fecha_limite_horas'] ?? '—';
                foreach ($asignadosIds as $uid) {
                    Notificacion::create([
                        'user_id' => $uid,
                        'tipo' => 'flujo_paso',
                        'titulo' => "Nuevo paso asignado: {$paso['nombre']}",
                        'mensaje' => "Se te ha asignado el paso '{$paso['nombre']}' del flujo '{$flujo->nombre}'. Tiempo: {$tiempo}h.",
                        'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>',
                        'color' => 'text-blue-500',
                        'url' => route('flujos'),
                    ]);
                }
            }
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('flujos')->with('success', 'Flujo iniciado correctamente.');
    }

    public function completarPaso(Request $request, FlujoPasoAsignacion $pasoAsignacion)
    {
        $user = Auth::user();

        // Find the ejecutor record for this user
        $ejecutor = FlujoPasoEjecutor::where('flujo_paso_asignacion_id', $pasoAsignacion->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$ejecutor && $user->role?->slug !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para completar este paso.'], 403);
        }

        if ($ejecutor && $ejecutor->estado === 'completado') {
            return response()->json(['success' => false, 'message' => 'Ya completaste este paso.'], 400);
        }

        $pasoInfo = ($pasoAsignacion->ejecucion->flujoTrabajo->pasos ?? [])[$pasoAsignacion->paso_index] ?? [];

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('flujo_archivos', 'public');
        }

        if ($ejecutor) {
            $ejecutor->update([
                'estado' => 'completado',
                'completado_en' => now(),
                'archivo' => $archivoPath,
                'mensaje' => $request->mensaje,
            ]);

            // Create Tareas from checklist items for this ejecutor
            $checklist = $pasoInfo['checklist'] ?? [];
            foreach ($checklist as $item) {
                if (!empty($item['item'])) {
                    Tarea::create([
                        'user_id' => $ejecutor->user_id,
                        'titulo' => $item['item'],
                        'descripcion' => "Checklist del paso '{$pasoAsignacion->paso_nombre}' en flujo '{$pasoAsignacion->ejecucion->flujoTrabajo->nombre}'",
                        'prioridad' => $pasoInfo['prioridad'] ?? 'media',
                        'categoria' => 'Flujo',
                        'status' => 'pendiente',
                    ]);
                }
            }
        }

        // Also update the main asignacion record for backward compat
        $pasoAsignacion->update([
            'archivo' => $archivoPath ?: $pasoAsignacion->archivo,
            'mensaje' => $request->mensaje ?? $pasoAsignacion->mensaje,
        ]);

        // Check if all ejecutores for this step are completed
        if ($pasoAsignacion->todosEjecutoresCompletados()) {
            // Move to revision
            $pasoAsignacion->update([
                'estado' => 'en_progreso',
                'revision_estado' => 'en_revision',
            ]);

            // Notify the reviewer that the paso is ready for review
            if ($pasoAsignacion->revisor_id) {
                Notificacion::create([
                    'user_id' => $pasoAsignacion->revisor_id,
                    'tipo' => 'flujo_revision',
                    'titulo' => "Paso listo para revisión: {$pasoAsignacion->paso_nombre}",
                    'mensaje' => "Todos los asignados completaron el paso '{$pasoAsignacion->paso_nombre}'. Revisa y aprueba o rechaza.",
                    'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>',
                    'color' => 'text-amber-500',
                    'url' => route('flujos'),
                ]);
            }
        }

        return response()->json(['success' => true, 'todos_completados' => $pasoAsignacion->todosEjecutoresCompletados()]);
    }

    public function revisarPaso(Request $request, FlujoPasoAsignacion $pasoAsignacion)
    {
        $user = Auth::user();

        if ($pasoAsignacion->revisor_id !== $user->id && $user->role?->slug !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'No eres el revisor de este paso.'], 403);
        }

        $request->validate([
            'accion' => 'required|in:aprobar,rechazar',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $accion = $request->accion;
        $comentario = $request->comentario;

        $pasoAsignacion->update([
            'revision_estado' => $accion === 'aprobar' ? 'aprobado' : 'rechazado',
            'revision_comentario' => $comentario,
            'revisado_por' => $user->id,
            'revisado_en' => now(),
        ]);

        if ($accion === 'aprobar') {
            $this->aprobarPaso($pasoAsignacion);
        } else {
            // Rechazado: reset ejecutores to pendiente
            FlujoPasoEjecutor::where('flujo_paso_asignacion_id', $pasoAsignacion->id)
                ->update(['estado' => 'pendiente', 'completado_en' => null]);

            // Notify assignees
            $ejecutores = FlujoPasoEjecutor::where('flujo_paso_asignacion_id', $pasoAsignacion->id)->get();
            foreach ($ejecutores as $ejec) {
                Notificacion::create([
                    'user_id' => $ejec->user_id,
                    'tipo' => 'flujo_rechazado',
                    'titulo' => "Paso rechazado: {$pasoAsignacion->paso_nombre}",
                    'mensaje' => "El revisor rechazó el paso '{$pasoAsignacion->paso_nombre}'. Comentario: " . ($comentario ?: 'Sin comentario') . ". Corrige y vuelve a enviar.",
                    'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>',
                    'color' => 'text-rose-500',
                    'url' => route('flujos'),
                ]);
            }
        }

        return response()->json(['success' => true, 'accion' => $accion]);
    }

    private function aprobarPaso(FlujoPasoAsignacion $pasoAsignacion)
    {
        $ejecucion = $pasoAsignacion->ejecucion;
        $flujo = $ejecucion->flujoTrabajo;
        $pasos = $flujo->pasos ?? [];
        $esUltimo = ($pasoAsignacion->paso_index + 1) >= count($pasos);

        $pasoAsignacion->update([
            'estado' => 'completado',
            'fecha_completado' => now(),
        ]);

        if ($esUltimo) {
            $ejecucion->update([
                'estado' => 'completada',
                'paso_actual_index' => $pasoAsignacion->paso_index,
            ]);
            $flujo->update(['estado' => 'Completado', 'fecha_completado' => now()]);

            Notificacion::create([
                'user_id' => $flujo->user_id,
                'tipo' => 'flujo_completado',
                'titulo' => "Flujo '{$flujo->nombre}' completado",
                'mensaje' => "Todos los pasos del flujo '{$flujo->nombre}' han sido completados y aprobados.",
                'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'color' => 'text-emerald-500',
                'url' => route('flujos'),
            ]);

            return;
        }

        // Move to next step
        $siguienteIndex = $pasoAsignacion->paso_index + 1;
        $ejecucion->update(['paso_actual_index' => $siguienteIndex]);

        // Mark next paso as en_progreso and its ejecutores
        $siguientePaso = FlujoPasoAsignacion::where('flujo_ejecucion_id', $ejecucion->id)
            ->where('paso_index', $siguienteIndex)->first();

        if ($siguientePaso) {
            $siguientePaso->update(['estado' => 'en_progreso']);
            $siguienteInfo = $pasos[$siguienteIndex] ?? [];

            FlujoPasoEjecutor::where('flujo_paso_asignacion_id', $siguientePaso->id)
                ->update(['estado' => 'pendiente']);

            $ejecutores = FlujoPasoEjecutor::where('flujo_paso_asignacion_id', $siguientePaso->id)->get();
            foreach ($ejecutores as $ejec) {
                $tiempo = $siguienteInfo['fecha_limite_horas'] ?? '—';
                $descripcion = $siguienteInfo['descripcion'] ?? '';
                Notificacion::create([
                    'user_id' => $ejec->user_id,
                    'tipo' => 'flujo_paso',
                    'titulo' => "Nuevo paso: {$siguientePaso->paso_nombre}",
                    'mensaje' => "El paso anterior fue aprobado. Ahora debes realizar '{$siguientePaso->paso_nombre}'. {$descripcion} Tiempo: {$tiempo}h.",
                    'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>',
                    'color' => 'text-emerald-500',
                    'url' => route('flujos'),
                ]);
            }

            if ($siguientePaso->revisor_id) {
                Notificacion::create([
                    'user_id' => $siguientePaso->revisor_id,
                    'tipo' => 'flujo_revision',
                    'titulo' => "Nuevo paso para revisar: {$siguientePaso->paso_nombre}",
                    'mensaje' => "El paso anterior fue aprobado. Estarás atento para revisar '{$siguientePaso->paso_nombre}' cuando esté listo.",
                    'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>',
                    'color' => 'text-amber-500',
                    'url' => route('flujos'),
                ]);
            }
        }
    }

    public function misPendientes()
    {
        $userId = Auth::id();

        // Steps where user is an ejecutor (assigned)
        $pasosPendientes = FlujoPasoAsignacion::whereHas('ejecutores', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('estado', 'pendiente');
        })
        ->where('estado', 'en_progreso')
        ->with(['ejecucion.flujoTrabajo', 'ejecutores'])
        ->get()
        ->map(function ($paso) use ($userId) {
            $ejecutor = $paso->ejecutores->firstWhere('user_id', $userId);
            $ejecutor->makeHidden(['ejecutores']);
            return $paso;
        });

        // Steps where user is reviewer and needs to review
        $pasosPendientesRevision = FlujoPasoAsignacion::where('revisor_id', $userId)
            ->where('revision_estado', 'en_revision')
            ->with(['ejecucion.flujoTrabajo'])
            ->get();

        return response()->json([
            'pendientes' => $pasosPendientes,
            'pendientes_revision' => $pasosPendientesRevision,
        ]);
    }
}
