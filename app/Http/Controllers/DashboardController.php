<?php

namespace App\Http\Controllers;

use App\Models\FlujoPasoAsignacion;
use App\Models\FlujoPasoEjecutor;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function formatearSegundos(?int $segundos): string
    {
        if (!$segundos) return '—';
        $segundos = abs($segundos);
        $h = intdiv($segundos, 3600);
        $m = intdiv($segundos % 3600, 60);
        $s = $segundos % 60;
        return ($h ? "{$h}h " : '') . ($m ? "{$m}m " : '') . "{$s}s";
    }

    public function index()
    {
        $user = Auth::user();

        $pasoIds = FlujoPasoEjecutor::whereUserId($user->id)
            ->pluck('flujo_paso_asignacion_id');
        $flujoIdsComoRevisor = FlujoPasoAsignacion::whereRevisorId($user->id)
            ->pluck('flujo_ejecucion_id');

        $flujoIdsComoEjecutor = DB::table('flujo_paso_asignaciones')
            ->whereIn('id', $pasoIds->toArray())
            ->pluck('flujo_ejecucion_id');

        $allIds = $flujoIdsComoEjecutor->merge($flujoIdsComoRevisor);
        $flujoEjecucionIds = DB::table('flujo_ejecuciones')
            ->whereIn('id', $allIds->toArray())
            ->pluck('flujo_trabajo_id');

        $misFlujos = DB::table('flujos_trabajo')
            ->where('user_id', $user->id)
            ->orWhereIn('id', $flujoEjecucionIds->toArray())
            ->count();

        $usuariosActivos = DB::table('user_sessions')
            ->whereNull('logged_out_at')
            ->where('logged_in_at', '>=', now()->subHours(24))
            ->distinct()
            ->count('user_id');

        $tareasPersonales = DB::table('tareas')
            ->where('user_id', $user->id)
            ->where('completada', false)
            ->whereNull('completed_at')
            ->whereNotIn('categoria', ['Flujo', 'Solicitud'])
            ->count();

        $tareasFlujo = DB::table('tareas')
            ->where('user_id', $user->id)
            ->where('completada', false)
            ->whereNull('completed_at')
            ->where('categoria', 'Flujo')
            ->count();

        $sesionActual = UserSession::query()->where('user_id', $user->id)->whereNull('logged_out_at')->latest('id')->first();
        $sesionAnterior = UserSession::query()->where('user_id', $user->id)->whereNotNull('logged_out_at')->latest('id')->first();

        $tiempoActivo = $sesionActual
            ? $this->formatearSegundos($sesionActual->logged_in_at->diffInSeconds(now()) - $sesionActual->activeBreakSeconds)
            : '—';

        $ultimaDuracion = $sesionAnterior
            ? $this->formatearSegundos($sesionAnterior->duration)
            : '—';

        $ultimoDescanso = $sesionAnterior
            ? $this->formatearSegundos($sesionAnterior->totalBreakSeconds)
            : '—';

        $estrellas = $user->calcularEstrellasMes(now()->year, now()->month);

        return view('inicio', compact(
            'misFlujos',
            'usuariosActivos',
            'tareasPersonales',
            'tareasFlujo',
            'tiempoActivo',
            'ultimaDuracion',
            'ultimoDescanso',
            'estrellas',
        ));
    }
}
