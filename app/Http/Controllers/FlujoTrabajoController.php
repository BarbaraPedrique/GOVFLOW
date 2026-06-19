<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\FlujoEjecucion;
use App\Models\FlujoPasoAsignacion;
use App\Models\FlujoTrabajo;
use App\Models\LogAuditoria;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FlujoTrabajoController extends Controller
{
    public function index(Request $request)
    {
        $query = FlujoTrabajo::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%")
                  ->orWhere('codigo', 'like', "%$buscar%")
                  ->orWhere('departamento', 'like', "%$buscar%")
                  ->orWhere('estado', 'like', "%$buscar%");
            });
        }

        $flujos = $query->orderByDesc('id')->get();

        return view('flujos.index', compact('flujos'));
    }

    public function create()
    {
        $equipos = Auth::user()->role?->slug === 'super_admin'
            ? Equipo::orderBy('nombre')->get()
            : Equipo::orderBy('nombre')->get();

        return view('flujos.create', compact('equipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255|unique:flujos_trabajo,nombre',
            'departamento' => 'required|string|max:255',
            'estado'       => 'required|in:Activo,Borrador,Completado,Pausado',
            'equipo_id'    => 'nullable|exists:equipos,id',
        ], [
            'nombre.required'       => 'El nombre del flujo es obligatorio.',
            'nombre.unique'         => 'Ya existe un flujo con ese nombre.',
            'departamento.required' => 'El departamento es obligatorio.',
            'estado.required'       => 'El estado es obligatorio.',
        ]);

        $flujo = FlujoTrabajo::create([
            'codigo'       => FlujoTrabajo::generarCodigo(),
            'nombre'       => $request->nombre,
            'departamento' => $request->departamento,
            'estado'       => $request->estado,
            'equipo_id'    => $request->equipo_id,
            'user_id'      => Auth::id(),
        ]);

        Notificacion::create([
            'user_id' => Auth::id(),
            'tipo' => 'flujo_creado',
            'titulo' => 'Nuevo flujo de trabajo',
            'mensaje' => "Flujo '{$flujo->nombre}' creado con código {$flujo->codigo}",
            'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>',
            'color' => 'text-amber-500',
            'url' => route('flujos-trabajo.index'),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $flujo->id, 'flujo' => $flujo]);
        }

        return redirect()->route('flujos-trabajo.index')
            ->with('success', 'Flujo de trabajo creado correctamente.');
    }

    public function show(FlujoTrabajo $flujos_trabajo)
    {
        return redirect()->route('flujos-trabajo.edit', $flujos_trabajo);
    }

    public function edit(FlujoTrabajo $flujos_trabajo)
    {
        $equipos = Auth::user()->role?->slug === 'super_admin'
            ? Equipo::orderBy('nombre')->get()
            : Equipo::orderBy('nombre')->get();

        return view('flujos.edit', compact('flujos_trabajo', 'equipos'));
    }

    public function update(Request $request, FlujoTrabajo $flujos_trabajo)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255|unique:flujos_trabajo,nombre,'.$flujos_trabajo->id,
            'departamento' => 'required|string|max:255',
            'estado'       => 'required|in:Activo,Borrador,Completado,Pausado',
            'equipo_id'    => 'nullable|exists:equipos,id',
        ]);

        $flujos_trabajo->update($request->only('nombre', 'departamento', 'estado', 'equipo_id'));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $flujos_trabajo->id]);
        }

        return redirect()->route('flujos-trabajo.index')
            ->with('success', 'Flujo actualizado correctamente.');
    }

    public function destroy(FlujoTrabajo $flujos_trabajo)
    {
        $user = Auth::user();
        if (!in_array($user->role?->slug, ['super_admin', 'administrador'])) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar flujos.'], 403);
            }
            return back()->with('error', 'No tienes permiso para eliminar flujos.');
        }

        try {
            $nombre = $flujos_trabajo->nombre;
            $codigo = $flujos_trabajo->codigo;

            DB::transaction(function () use ($flujos_trabajo) {
                $flujos_trabajo->estados()->delete();
                FlujoTrabajo::destroy($flujos_trabajo->id);
            });

            LogAuditoria::registrar(
                accion: 'eliminar_flujo',
                entidadType: 'FlujoTrabajo',
                entidadId: $flujos_trabajo->id,
                descripcion: "Flujo de trabajo {$codigo} — {$nombre} eliminado por " . Auth::user()->name,
            );

            if (request()->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('flujos-trabajo.index')
                ->with('success', 'Flujo eliminado correctamente.');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar el flujo: ' . $e->getMessage()], 500);
            }

            return redirect()->route('flujos-trabajo.index')
                ->with('error', 'Error al eliminar el flujo: ' . $e->getMessage());
        }
    }

    public function showTimeline(Request $request)
    {
        $user = Auth::user();
        $esSuperAdmin = $user->role?->slug === 'super_admin';
        $verMios = $request->filled('ver') && $request->ver === 'mios';

        $equipos = $esSuperAdmin
            ? Equipo::orderBy('nombre')->get()
            : $user->equipos()->orderBy('nombre')->get();

        $query = FlujoTrabajo::with('estados', 'user', 'equipo')
            ->with(['ejecuciones' => function ($q) {
                $q->withCount(['pasos as pasos_completados' => fn($qq) => $qq->where('estado', 'completado')]);
            }]);

        if ($request->filled('equipo_id')) {
            $query->where('equipo_id', $request->equipo_id);
        }

        if (!$esSuperAdmin) {
            $flujoRelIds = FlujoPasoAsignacion::where(function ($q) use ($user) {
                    $q->whereHas('ejecutores', fn($qq) => $qq->where('user_id', $user->id))
                      ->orWhere('revisor_id', $user->id);
                })
                ->pluck('flujo_ejecucion_id')
                ->pipe(fn($ids) => FlujoEjecucion::whereIn('id', $ids)->pluck('flujo_trabajo_id'))
                ->unique()
                ->toArray();

            $query->whereIn('id', $flujoRelIds);
        }

        $flujos = $query->orderByDesc('id')->get();

        $misPasosPendientes = FlujoPasoAsignacion::whereHas('ejecutores', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('estado', 'pendiente');
            })
            ->whereIn('estado', ['pendiente', 'en_progreso'])
            ->when($request->filled('equipo_id'), function ($q) use ($request) {
                $q->whereHas('ejecucion.flujoTrabajo', fn($qq) => $qq->where('equipo_id', $request->equipo_id));
            })
            ->with(['ejecucion.flujoTrabajo', 'ejecutores' => fn($q) => $q->where('user_id', $user->id)])
            ->get();

        $pasoCounts = null;
        if ($verMios) {
            $flujoIds = $flujos->pluck('id');
            $pasoCounts = FlujoPasoAsignacion::whereHas('ejecucion', fn($q) => $q->whereIn('flujo_trabajo_id', $flujoIds))
                ->whereHas('ejecutores', fn($q) => $q->where('user_id', $user->id))
                ->selectRaw('flujo_ejecucion_id, COUNT(*) as total, SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) as completados', ['completado'])
                ->groupBy('flujo_ejecucion_id')
                ->get()
                ->keyBy('flujo_ejecucion_id');
        }

        $pasoUsuarios = null;
        if (!$verMios && $flujos->isNotEmpty()) {
            $userIds = collect();
            foreach ($flujos as $flujo) {
                $pasos = $flujo->pasos ?? [];
                foreach ($pasos as $paso) {
                    if (!empty($paso['asignacion_usuario_id'])) {
                        $userIds->push((int) $paso['asignacion_usuario_id']);
                    }
                    if (!empty($paso['revisor_id'])) {
                        $userIds->push((int) $paso['revisor_id']);
                    }
                    if (!empty($paso['asignados_ids'])) {
                        foreach ($paso['asignados_ids'] as $aid) {
                            $userIds->push((int) $aid);
                        }
                    }
                }
            }
            $pasoUsuarios = $userIds->unique()->isNotEmpty()
                ? User::whereIn('id', $userIds->unique())->get()->keyBy('id')
                : collect();
        }

        $pendientesRevision = FlujoPasoAsignacion::where('revisor_id', $user->id)
            ->where('revision_estado', 'en_revision')
            ->when($request->filled('equipo_id'), function ($q) use ($request) {
                $q->whereHas('ejecucion.flujoTrabajo', fn($qq) => $qq->where('equipo_id', $request->equipo_id));
            })
            ->with(['ejecucion.flujoTrabajo'])
            ->get();

        return view('flujos', compact('flujos', 'equipos', 'esSuperAdmin', 'misPasosPendientes', 'verMios', 'pasoCounts', 'pasoUsuarios', 'pendientesRevision'));
    }
}
