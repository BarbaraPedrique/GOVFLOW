<?php

namespace App\Http\Controllers;

use App\Models\FlujoTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->get('periodo', 'mensual');

        $eficienciaMensual = FlujoTrabajo::whereNotNull('fecha_completado')
            ->whereNotNull('fecha_limite')
            ->select(
                DB::raw("DATE_FORMAT(fecha_completado, '%Y-%m') as mes"),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN fecha_completado <= fecha_limite THEN 1 ELSE 0 END) as a_tiempo"),
                DB::raw("SUM(CASE WHEN fecha_completado > fecha_limite THEN 1 ELSE 0 END) as vencidas")
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(function ($item) {
                $item->eficiencia = $item->total > 0
                    ? round(($item->a_tiempo / $item->total) * 100, 1)
                    : 0;
                return $item;
            });

        $eficienciaPorDepartamento = FlujoTrabajo::whereNotNull('fecha_completado')
            ->whereNotNull('fecha_limite')
            ->select(
                'departamento',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN fecha_completado <= fecha_limite THEN 1 ELSE 0 END) as a_tiempo"),
                DB::raw("SUM(CASE WHEN fecha_completado > fecha_limite THEN 1 ELSE 0 END) as vencidas")
            )
            ->groupBy('departamento')
            ->get()
            ->map(function ($item) {
                $item->eficiencia = $item->total > 0
                    ? round(($item->a_tiempo / $item->total) * 100, 1)
                    : 0;
                return $item;
            });

        $totalFlujos = FlujoTrabajo::count();
        $completados = FlujoTrabajo::where('estado', 'Completado')->count();
        $tasaCompletitud = $totalFlujos > 0 ? round(($completados / $totalFlujos) * 100, 1) : 0;

        $tasaEficienciaGlobal = FlujoTrabajo::whereNotNull('fecha_completado')
            ->whereNotNull('fecha_limite')
            ->selectRaw("SUM(CASE WHEN fecha_completado <= fecha_limite THEN 1 ELSE 0 END) as a_tiempo")
            ->selectRaw("COUNT(*) as total")
            ->first();
        $eficienciaGlobal = $tasaEficienciaGlobal->total > 0
            ? round(($tasaEficienciaGlobal->a_tiempo / $tasaEficienciaGlobal->total) * 100, 1)
            : 0;

        $flujosRecientes = FlujoTrabajo::whereNotNull('fecha_completado')
            ->with('user')
            ->orderByDesc('fecha_completado')
            ->limit(10)
            ->get();

        return view('auditoria', compact(
            'eficienciaMensual',
            'eficienciaPorDepartamento',
            'totalFlujos',
            'completados',
            'tasaCompletitud',
            'eficienciaGlobal',
            'flujosRecientes',
            'periodo'
        ));
    }
}
