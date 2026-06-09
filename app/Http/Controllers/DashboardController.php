<?php

namespace App\Http\Controllers;

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
        $h = intdiv($segundos, 3600);
        $m = intdiv($segundos % 3600, 60);
        $s = $segundos % 60;
        return ($h ? "{$h}h " : '') . ($m ? "{$m}m " : '') . "{$s}s";
    }

    public function index()
    {
        $user = Auth::user();

        $misFlujos = FlujoTrabajo::where('user_id', $user->id)->count();

        $usuariosActivos = UserSession::whereNull('logged_out_at')
            ->where('logged_in_at', '>=', now()->subHours(24))
            ->distinct('user_id')
            ->count('user_id');

        $tareasPendientes = Tarea::where('user_id', $user->id)->where('completada', false)->count();

        $sesionActual = UserSession::where('user_id', $user->id)->whereNull('logged_out_at')->latest()->first();
        $sesionAnterior = UserSession::where('user_id', $user->id)->whereNotNull('logged_out_at')->latest()->first();

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
            'tareasPendientes',
            'tiempoActivo',
            'ultimaDuracion',
            'ultimoDescanso',
            'estrellas',
        ));
    }
}
