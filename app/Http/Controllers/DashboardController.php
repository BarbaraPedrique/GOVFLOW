<?php

namespace App\Http\Controllers;

use App\Models\FlujoEjecucion;
use App\Models\FlujoPasoAsignacion;
use App\Models\FlujoPasoEjecutor;
use App\Models\FlujoTrabajo;
use App\Models\Tarea;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $pasoIds = FlujoPasoEjecutor::where('user_id', $user->id)
            ->pluck('flujo_paso_asignacion_id');
        $flujoIdsComoRevisor = FlujoPasoAsignacion::where('revisor_id', $user->id)
            ->pluck('flujo_ejecucion_id');

        $flujoIdsComoEjecutor = FlujoPasoAsignacion::whereIn('id', $pasoIds->toArray())
            ->pluck('flujo_ejecucion_id');

        $allIds = $flujoIdsComoEjecutor->merge($flujoIdsComoRevisor);
        $flujoEjecucionIds = FlujoEjecucion::whereIn('id', $allIds->toArray())
            ->pluck('flujo_trabajo_id');

        $misFlujos = FlujoTrabajo::where('user_id', $user->id)
            ->orWhereIn('id', $flujoEjecucionIds->toArray())
            ->count();

        $usuariosActivos = UserSession::whereNull('logged_out_at')
            ->where('logged_in_at', '>=', now()->subHours(24))
            ->distinct()
            ->count('user_id');

        $tareasPersonales = Tarea::query()->where('user_id', $user->id)
            ->where('completada', false)
            ->whereNull('completed_at')
            ->whereNotIn('categoria', ['Flujo', 'Solicitud'])
            ->count();

        $tareasFlujo = Tarea::query()->where('user_id', $user->id)
            ->where('completada', false)
            ->whereNull('completed_at')
            ->where('categoria', 'Flujo')
            ->count();

        $sesionActual = UserSession::query()->where('user_id', $user->id)->whereNull('logged_out_at')->latest()->first();
        $sesionAnterior = UserSession::query()->where('user_id', $user->id)->whereNotNull('logged_out_at')->latest()->first();

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
