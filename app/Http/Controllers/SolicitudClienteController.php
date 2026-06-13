<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\FlujoPasoAsignacion;
use App\Models\LogAuditoria;
use App\Models\Notificacion;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudClienteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:unirse_equipo,cambio_rol,revision_tareas,revision_web,reportar_error',
            'equipo_id' => 'required_if:tipo,unirse_equipo|exists:equipos,id',
            'tarea_id' => 'required_if:tipo,revision_tareas|exists:tareas,id',
            'descripcion' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        $tipos = [
            'unirse_equipo' => 'Unirse a un equipo',
            'cambio_rol' => 'Cambio de rol',
            'revision_tareas' => 'Revisión de tareas',
            'revision_web' => 'Revisión de la web',
            'reportar_error' => 'Reportar error',
        ];

        $titulo = 'Nueva solicitud: ' . $tipos[$request->tipo];

        $descripcion = $request->descripcion;
        $descripcion .= "\n\n---\nTipo: " . $tipos[$request->tipo];
        $descripcion .= "\nSolicitante: " . $user->name . ' (' . $user->email . ')';

        if ($request->tipo === 'unirse_equipo' && $request->equipo_id) {
            $equipo = Equipo::find($request->equipo_id);
            $descripcion .= "\nEquipo: " . ($equipo?->nombre ?? 'N/A');
        }

        $recipients = collect();

        if ($request->tipo === 'unirse_equipo') {
            $recipients = User::where('role_id', '<=', 3)
                ->where('id', '!=', $user->id)
                ->get();
            if ($request->equipo_id) {
                $equipo = Equipo::find($request->equipo_id);
                if ($equipo && $equipo->gerente && !$recipients->contains('id', $equipo->gerente_id)) {
                    $recipients->push($equipo->gerente);
                }
            }
        } elseif ($request->tipo === 'revision_tareas') {
            $recipients = User::where('role_id', '<=', 4)
                ->where('id', '!=', $user->id)
                ->get();
        } else {
            $recipients = User::where('role_id', 1)->get();
        }

        $created = 0;
        foreach ($recipients as $recipient) {
            Tarea::create([
                'user_id' => $recipient->id,
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'prioridad' => 'alta',
                'categoria' => 'Solicitud',
                'status' => 'pendiente',
            ]);

            Notificacion::create([
                'user_id' => $recipient->id,
                'tipo' => 'solicitud',
                'titulo' => $titulo,
                'mensaje' => $request->descripcion,
                'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>',
                'color' => 'text-blue-500',
                'url' => route('solicitudes.mis'),
            ]);
            $created++;
        }

        LogAuditoria::registrar(
            accion: 'solicitud_creada',
            entidadType: get_class($user),
            entidadId: $user->id,
            descripcion: "Solicitud {$request->tipo} creada por {$user->name} (notificados: {$created})",
        );

        return back()->with('success', 'Solicitud enviada correctamente.');
    }

    public function misSolicitudes()
    {
        $user = Auth::user();

        $pendientes = Tarea::where('user_id', $user->id)
            ->where('categoria', 'Solicitud')
            ->where('status', 'pendiente')
            ->where('completada', false)
            ->orderByDesc('created_at')
            ->get();

        $historial = Tarea::where('user_id', $user->id)
            ->where('categoria', 'Solicitud')
            ->whereIn('status', ['aprobado', 'rechazado'])
            ->orderByDesc('created_at')
            ->get();

        $equiposDisponibles = Equipo::all();

        $revisionesFlujo = FlujoPasoAsignacion::where('revisor_id', $user->id)
            ->where('revision_estado', 'en_revision')
            ->with(['ejecucion.flujoTrabajo'])
            ->get();

        $registrosPendientes = collect();
        $registrosHistorial = collect();
        if ($user->role?->slug === 'super_admin') {
            $registrosPendientes = User::where('status', User::STATUS_PENDIENTE)
                ->orderByDesc('created_at')
                ->get();

            $tareasRegistro = Tarea::where('categoria', 'Solicitud')
                ->where('descripcion', 'like', 'user_id:%')
                ->whereIn('status', ['aprobado', 'rechazado'])
                ->orderByDesc('updated_at')
                ->get();

            $registrosHistorial = $tareasRegistro;
        }

        return view('solicitudes_index', compact(
            'pendientes', 'historial', 'equiposDisponibles', 'revisionesFlujo',
            'registrosPendientes', 'registrosHistorial'
        ));
    }

    public function aprobarSolicitud(Tarea $tarea)
    {
        $user = Auth::user();
        if ($tarea->user_id !== $user->id) abort(403);

        $tarea->update(['completada' => true, 'status' => 'aprobado']);

        $titulo = 'Solicitud aprobada: ' . $tarea->titulo;

        preg_match('/Solicitante: (.+?) \(/', $tarea->descripcion, $matches);
        if (!empty($matches[1])) {
            $solicitante = User::where('name', $matches[1])->first();
            if ($solicitante) {
                Notificacion::create([
                    'user_id' => $solicitante->id,
                    'tipo' => 'solicitud_aprobada',
                    'titulo' => $titulo,
                    'mensaje' => "Tu solicitud \"{$tarea->titulo}\" ha sido aprobada por {$user->name}.",
                    'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                    'color' => 'text-emerald-500',
                    'url' => route('solicitudes.mis'),
                ]);
            }
        }

        LogAuditoria::registrar(
            accion: 'solicitud_aprobada',
            entidadType: get_class($tarea),
            entidadId: $tarea->id,
            descripcion: "Solicitud \"{$tarea->titulo}\" aprobada por {$user->name}",
        );

        return back()->with('success', 'Solicitud aprobada correctamente.');
    }

    public function rechazarSolicitud(Request $request, Tarea $tarea)
    {
        $user = Auth::user();
        if ($tarea->user_id !== $user->id) abort(403);

        $equipoSugerido = null;
        if ($request->filled('equipo_sugerido_id')) {
            $equipoSugerido = Equipo::find($request->equipo_sugerido_id);
        }

        $tarea->update(['completada' => true, 'status' => 'rechazado']);

        $titulo = 'Solicitud rechazada: ' . $tarea->titulo;

        preg_match('/Solicitante: (.+?) \(/', $tarea->descripcion, $matches);
        preg_match('/Equipo: (.+)/', $tarea->descripcion, $eqMatches);
        $equipoOriginal = $eqMatches[1] ?? '';

        if (!empty($matches[1])) {
            $solicitante = User::where('name', $matches[1])->first();
            if ($solicitante) {
                $mensaje = "Tu solicitud \"{$tarea->titulo}\" ha sido rechazada.";
                if ($equipoSugerido) {
                    $mensaje .= " En su lugar, se te sugiere el equipo: {$equipoSugerido->nombre}.";
                }

                Notificacion::create([
                    'user_id' => $solicitante->id,
                    'tipo' => 'solicitud_rechazada',
                    'titulo' => $titulo,
                    'mensaje' => $mensaje,
                    'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                    'color' => 'text-rose-500',
                    'url' => route('solicitudes.mis'),
                ]);

                if ($equipoSugerido) {
                    Notificacion::create([
                        'user_id' => $solicitante->id,
                        'tipo' => 'equipo_sugerido',
                        'titulo' => "Equipo sugerido: {$equipoSugerido->nombre}",
                        'mensaje' => "Se te ha sugerido el equipo \"{$equipoSugerido->nombre}\" como alternativa a \"{$equipoOriginal}\". Haz clic para crear una nueva solicitud.",
                        'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>',
                        'color' => 'text-blue-500',
                        'url' => route('solicitudes.mis'),
                    ]);
                }
            }
        }

        LogAuditoria::registrar(
            accion: 'solicitud_rechazada',
            entidadType: get_class($tarea),
            entidadId: $tarea->id,
            descripcion: "Solicitud \"{$tarea->titulo}\" rechazada por {$user->name}" . ($equipoSugerido ? " (sugerido: {$equipoSugerido->nombre})" : ''),
        );

        $msg = 'Solicitud rechazada.';
        if ($equipoSugerido) $msg .= " Se sugirió el equipo: {$equipoSugerido->nombre}.";

        return back()->with('success', $msg);
    }
}
