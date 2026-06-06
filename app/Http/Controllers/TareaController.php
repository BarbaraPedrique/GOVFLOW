<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Horario;
use App\Models\Notificacion;
use App\Models\Tarea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TareaController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $equipos = collect();

        if (in_array($user->role?->slug, ['super_admin', 'administrador'])) {
            $tareas = Tarea::where('categoria', '!=', 'Solicitud')->with('equipo')->porPrioridad()->get()->groupBy('prioridad');
            $equipos = Equipo::orderBy('nombre')->get();
        } elseif ($user->role?->slug === 'gerente') {
            $equipoIds = $user->equiposDirigidos()->pluck('id');
            $tareas = Tarea::where(function($q) use ($equipoIds, $user) {
                    $q->whereIn('equipo_id', $equipoIds)->orWhere('user_id', $user->id);
                })->where('categoria', '!=', 'Solicitud')
                ->with('equipo')->porPrioridad()->get()->groupBy('prioridad');
            $equipos = $user->equiposDirigidos;
        } elseif ($user->role?->slug === 'lider_equipo') {
            $equipoIds = $user->equiposComoLider()->pluck('equipo_id');
            $tareas = Tarea::where(function($q) use ($equipoIds, $user) {
                    $q->whereIn('equipo_id', $equipoIds)->orWhere('user_id', $user->id);
                })->where('categoria', '!=', 'Solicitud')
                ->with('equipo')->porPrioridad()->get()->groupBy('prioridad');
            $equipos = $user->equiposComoLider;
        } else {
            $equipoIds = $user->equiposComoEmpleado()->pluck('equipo_id');
            $tareas = Tarea::where(function($q) use ($equipoIds, $user) {
                    $q->whereIn('equipo_id', $equipoIds)->orWhere('user_id', $user->id);
                })->where('categoria', '!=', 'Solicitud')
                ->with('equipo')->porPrioridad()->get()->groupBy('prioridad');
            $equipos = $user->equiposComoEmpleado;
        }

        return view('tareas', compact('tareas', 'equipos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $esAdmin = in_array($user->role?->slug, ['super_admin', 'administrador']);

        $rules = [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'prioridad' => 'required|in:alta,media,baja',
            'categoria' => 'nullable|string|max:100',
            'fecha_vencimiento' => 'nullable|date',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i',
            'receso' => 'nullable|integer|min:0|max:480',
            'equipo_id' => 'nullable|exists:equipos,id',
        ];

        $data = $request->validate($rules);

        if (!$esAdmin && $request->filled('equipo_id')) {
            $pertenece = $user->equipos()->where('equipo_id', $request->equipo_id)->exists();
            if (!$pertenece) {
                abort(403, 'No perteneces a ese equipo.');
            }
        }

        $maxOrden = Tarea::where('user_id', auth()->id())->max('orden') ?? 0;
        $data['user_id'] = auth()->id();
        $data['orden'] = $maxOrden + 1;

        $tarea = Tarea::create($data);

        Notificacion::create([
            'user_id' => auth()->id(),
            'tipo' => 'tarea_creada',
            'titulo' => 'Nueva tarea',
            'mensaje' => "Tarea '{$tarea->titulo}' creada con prioridad {$tarea->prioridad}",
            'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>',
            'color' => 'text-emerald-500',
            'url' => route('tareas.index'),
        ]);

        if ($tarea->fecha_vencimiento && $tarea->categoria !== 'Horario') {
            $diaSemana = (int) $tarea->fecha_vencimiento->format('N') - 1;
            $exists = Horario::where('user_id', auth()->id())
                ->where('dia_semana', $diaSemana)
                ->where('titulo', $tarea->titulo)
                ->exists();

            if (!$exists) {
                Horario::create([
                    'user_id' => auth()->id(),
                    'dia_semana' => $diaSemana,
                    'hora_inicio' => '08:00',
                    'hora_fin' => '09:00',
                    'titulo' => $tarea->titulo,
                ]);
            }
        }

        LogAuditoria::registrar(
            'crear_tarea',
            'Tarea',
            $tarea->id,
            "Tarea '{$tarea->titulo}' creada con prioridad {$tarea->prioridad}" . ($tarea->equipo_id ? " para equipo #{$tarea->equipo_id}" : ''),
        );

        return redirect()->route('tareas.index')->with('success', 'Tarea creada correctamente.');
    }

    public function update(Request $request, Tarea $tarea): JsonResponse
    {
        if ($tarea->user_id !== auth()->id()) abort(403);

        if ($request->has('completada')) {
            $tarea->update(['completada' => $request->boolean('completada')]);

            LogAuditoria::registrar(
                $request->boolean('completada') ? 'completar_tarea' : 'reabrir_tarea',
                'Tarea',
                $tarea->id,
                "Tarea '{$tarea->titulo}' " . ($request->boolean('completada') ? 'completada' : 'reabierta'),
            );

            return response()->json(['success' => true]);
        }

        if ($request->has('orden')) {
            $tarea->update(['orden' => $request->integer('orden')]);
            return response()->json(['success' => true]);
        }

        $data = $request->validate([
            'titulo' => 'string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'prioridad' => 'in:alta,media,baja',
            'categoria' => 'nullable|string|max:100',
            'fecha_vencimiento' => 'nullable|date',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i',
            'receso' => 'nullable|integer|min:0|max:480',
            'equipo_id' => 'nullable|exists:equipos,id',
        ]);

        $tarea->update($data);

        return response()->json(['success' => true]);
    }

    public function destroy(Tarea $tarea): JsonResponse
    {
        if ($tarea->user_id !== auth()->id()) abort(403);

        if ($tarea->fecha_vencimiento && $tarea->categoria !== 'Horario') {
            $diaSemana = (int) $tarea->fecha_vencimiento->format('N') - 1;
            Horario::where('user_id', auth()->id())
                ->where('dia_semana', $diaSemana)
                ->where('titulo', $tarea->titulo)
                ->delete();
        }

        $tarea->delete();
        return response()->json(['success' => true]);
    }

    public function reordenar(Request $request): JsonResponse
    {
        $ordenes = $request->validate([
            'ordenes' => 'required|array',
            'ordenes.*.id' => 'required|exists:tareas,id',
            'ordenes.*.orden' => 'required|integer|min:0',
        ]);

        foreach ($ordenes['ordenes'] as $item) {
            Tarea::where('id', $item['id'])->where('user_id', auth()->id())
                ->update(['orden' => $item['orden']]);
        }

        return response()->json(['success' => true]);
    }
}
