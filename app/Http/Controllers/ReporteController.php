<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\FlujoEjecucion;
use App\Models\FlujoPasoAsignacion;
use App\Models\LogAuditoria;
use App\Models\RoleHistorial;
use App\Models\Tarea;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(): View
    {
        return view('reportes');
    }

    public function generarPdf(Request $request)
    {
        $tipo = $request->input('tipo');
        $year = (int) $request->input('year', now()->year);

        $fechas = $this->calcularFechas($tipo, $request, $year);

        $data = $this->recopilarDatos($fechas['inicio'], $fechas['fin']);

        $titulo = $tipo === 'semanal'
            ? "Reporte Semanal - Semana {$request->input('semana')} del {$year}"
            : "Reporte Mensual - " . ucfirst(Carbon::createFromDate($year, (int)$request->input('mes'), 1)->locale('es')->monthName) . " {$year}";

        $pdf = Pdf::loadView('reporte-pdf', array_merge($data, [
            'titulo' => $titulo,
            'desde' => $fechas['inicio']->format('d/m/Y'),
            'hasta' => $fechas['fin']->format('d/m/Y'),
        ]));

        return $pdf->download("reporte-{$tipo}-{$year}.pdf");
    }

    private function calcularFechas(string $tipo, Request $request, int $year): array
    {
        if ($tipo === 'semanal') {
            $semana = (int) $request->input('semana', now()->isoWeek);
            $inicio = Carbon::now()->setISODate($year, $semana)->startOfWeek();
            $fin = Carbon::now()->setISODate($year, $semana)->endOfWeek();
        } else {
            $mes = (int) $request->input('mes', now()->month);
            $inicio = Carbon::createFromDate($year, $mes, 1)->startOfMonth();
            $fin = Carbon::createFromDate($year, $mes, 1)->endOfMonth();
        }

        return ['inicio' => $inicio, 'fin' => $fin];
    }

    private function recopilarDatos(Carbon $inicio, Carbon $fin): array
    {
        $flujosCreados = LogAuditoria::query()
            ->where('accion', 'crear')
            ->where(function ($q) {
                $q->where('entidad_type', 'App\Models\FlujoTrabajo')
                  ->orWhere('entidad_type', 'FlujoTrabajo');
            })
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();

        $flujosCompletados = FlujoEjecucion::query()
            ->where('estado', 'completada')
            ->whereBetween('updated_at', [$inicio, $fin])
            ->count();

        $flujosRealizados = $flujosCompletados;

        $flujosEnPeriodo = FlujoEjecucion::query()
            ->where(function ($q) use ($inicio, $fin) {
                $q->whereBetween('created_at', [$inicio, $fin])
                  ->orWhere(function ($q2) use ($inicio, $fin) {
                      $q2->where('estado', 'completada')
                         ->whereBetween('updated_at', [$inicio, $fin]);
                  });
            })
            ->with('flujoTrabajo')
            ->get();

        $detalleFlujos = $flujosEnPeriodo->map(function ($flujo) {
            $pasos = FlujoPasoAsignacion::query()
                ->where('flujo_ejecucion_id', $flujo->id)
                ->get();

            $totalPasos = $pasos->count();
            $pasosCompletados = $pasos->where('estado', 'completado')->count();
            $aTiempo = $pasos->filter(fn($p) => $p->fecha_limite && $p->fecha_completado && $p->fecha_completado <= $p->fecha_limite)->count();
            $fueraTiempo = $pasos->filter(fn($p) => $p->fecha_limite && $p->fecha_completado && $p->fecha_completado > $p->fecha_limite)->count();

            $inicioFlujo = $flujo->created_at;
            $finFlujo = $pasos->max('fecha_completado') ?? $flujo->updated_at;
            $duracionHoras = $inicioFlujo->diffInHours($finFlujo);

            $vigente = $flujo->flujoTrabajo !== null;

            return [
                'codigo' => $flujo->flujo_codigo ?? ($vigente ? $flujo->flujoTrabajo?->codigo : '—'),
                'nombre' => $flujo->flujo_nombre ?? ($vigente ? $flujo->flujoTrabajo?->nombre : '—'),
                'vigente' => $vigente,
                'estado' => $flujo->estado,
                'total_pasos' => $totalPasos,
                'pasos_completados' => $pasosCompletados,
                'a_tiempo' => $aTiempo,
                'fuera_tiempo' => $fueraTiempo,
                'duracion_horas' => $duracionHoras,
                'realizado_en' => $duracionHoras < 24
                    ? "{$duracionHoras}h"
                    : round($duracionHoras / 24, 1) . ' días',
            ];
        })->sortByDesc('duracion_horas')->values();

        $participantes = DB::table('flujo_paso_ejecutores')
            ->join('flujo_paso_asignaciones', 'flujo_paso_ejecutores.flujo_paso_asignacion_id', '=', 'flujo_paso_asignaciones.id')
            ->join('flujo_ejecuciones', 'flujo_paso_asignaciones.flujo_ejecucion_id', '=', 'flujo_ejecuciones.id')
            ->whereBetween('flujo_paso_asignaciones.created_at', [$inicio, $fin])
            ->distinct('flujo_paso_ejecutores.user_id')
            ->count('flujo_paso_ejecutores.user_id');

        $modificaciones = LogAuditoria::query()->whereBetween('created_at', [$inicio, $fin])->count();

        $nuevosIngresos = User::query()->whereBetween('created_at', [$inicio, $fin])->count();

        $empleadosEliminados = LogAuditoria::query()
            ->where('accion', 'eliminar_usuario')
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();

        $flujosEliminados = LogAuditoria::query()
            ->where('accion', 'eliminar_flujo')
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();

        $cambiosRoles = RoleHistorial::query()
            ->whereBetween('asignado_en', [$inicio, $fin])
            ->count();

        $solicitudes = Tarea::query()
            ->where('categoria', 'Solicitud')
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();

        $registrosPendientes = User::query()
            ->where('status', 'pendiente')
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();

        $equipos = Equipo::query()->withCount('miembros')->get()->map(function ($equipo) use ($inicio, $fin) {
            $tareasCompletadas = Tarea::query()
                ->where('equipo_id', $equipo->id)
                ->where('completada', true)
                ->whereBetween('completed_at', [$inicio, $fin])
                ->count();
            $tareasPendientes = Tarea::query()
                ->where('equipo_id', $equipo->id)
                ->where('completada', false)
                ->whereNull('completed_at')
                ->whereBetween('created_at', [$inicio, $fin])
                ->count();

            return [
                'nombre' => $equipo->nombre,
                'miembros' => $equipo->miembros_count,
                'tareas_completadas' => $tareasCompletadas,
                'tareas_pendientes' => $tareasPendientes,
            ];
        });

        $pieEquipos = $equipos
            ->filter(fn($e) => ($e['tareas_completadas'] + $e['tareas_pendientes']) > 0)
            ->sortByDesc(fn($e) => $e['tareas_completadas'])
            ->take(6)
            ->values();

        $pieChartBase64 = null;
        if ($pieEquipos->sum('tareas_completadas') > 0 && function_exists('imagecreatetruecolor')) {
            $pieEquiposArr = $pieEquipos->toArray();
            $total = array_sum(array_column($pieEquiposArr, 'tareas_completadas'));
            $colorMap = [
                [59, 130, 246], [34, 197, 94], [245, 158, 11],
                [239, 68, 68], [139, 92, 246], [236, 72, 153],
            ];

            $img = imagecreatetruecolor(300, 300);
            imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));

            $cx = 150; $cy = 150; $w = 260; $h = 260;
            $startAngle = 0;

            foreach ($pieEquiposArr as $i => $eq) {
                $val = (int) $eq['tareas_completadas'];
                if ($val <= 0) continue;
                $pct = $val / $total;
                $sliceAngle = $pct * 360;
                $endAngle = $startAngle + $sliceAngle;
                $color = imagecolorallocate($img, $colorMap[$i][0], $colorMap[$i][1], $colorMap[$i][2]);
                imagefilledarc($img, $cx, $cy, $w, $h, $startAngle, $endAngle, $color, IMG_ARC_PIE);
                if ($pct > 0.08) {
                    $midAngle = deg2rad($startAngle + $sliceAngle / 2);
                    $lx = $cx + 100 * cos($midAngle);
                    $ly = $cy + 100 * sin($midAngle);
                    $text = round($pct * 100) . '%';
                    $textColor = imagecolorallocate($img, 255, 255, 255);
                    $f = 3;
                    $tw = imagefontwidth($f) * strlen($text);
                    $th = imagefontheight($f);
                    imagestring($img, $f, (int)($lx - $tw / 2), (int)($ly - $th / 2), $text, $textColor);
                }
                $startAngle = $endAngle;
            }

            ob_start();
            imagepng($img);
            $png = ob_get_clean();
            imagedestroy($img);
            $pieChartBase64 = base64_encode($png);
        }

        $usuarios = User::query()->with('role')->get();

        $rendimientoUsuarios = $usuarios->map(function ($user) use ($inicio, $fin) {
            $tareasTotal = $user->tareas()
                ->whereBetween('created_at', [$inicio, $fin])
                ->count();
            $tareasCompletadas = $user->tareas()
                ->where('completada', true)
                ->whereBetween('completed_at', [$inicio, $fin])
                ->count();
            $tasa = $tareasTotal > 0 ? round(($tareasCompletadas / $tareasTotal) * 100, 1) : 0;

            return [
                'apodo' => $user->apodo ?? $user->name,
                'rol' => $user->role?->display_name ?? 'Sin rol',
                'tareas_total' => $tareasTotal,
                'tareas_completadas' => $tareasCompletadas,
                'tasa' => $tasa,
            ];
        });

        $conTareas = $rendimientoUsuarios->where('tareas_total', '>', 0);

        $mejorRendimiento = $conTareas->sortByDesc('tasa')->take(5)->values();
        $peorRendimiento = $conTareas->sortBy('tasa')
            ->filter(fn($u) => $u['tasa'] < 70)
            ->take(5)
            ->values();

        $semanas = [];
        $difDias = $inicio->diffInDays($fin);
        if ($difDias >= 28) {
            for ($i = 1; $i <= 4; $i++) {
                $semInicio = (clone $inicio)->addWeeks($i - 1);
                $semFin = (clone $inicio)->addWeeks($i)->subDay();
                if ($semFin->gt($fin)) $semFin = clone $fin;

                $flujosSem = FlujoEjecucion::query()
                    ->where('estado', 'completada')
                    ->whereBetween('updated_at', [$semInicio, $semFin])
                    ->count();

                $tareasSem = Tarea::query()
                    ->where('completada', true)
                    ->whereBetween('completed_at', [$semInicio, $semFin])
                    ->count();

                $totalTareasSem = Tarea::query()
                    ->whereBetween('created_at', [$semInicio, $semFin])
                    ->count();

                $efectividad = $totalTareasSem > 0 ? round(($tareasSem / $totalTareasSem) * 100, 1) : 0;

                $semanas[] = [
                    'semana' => $i,
                    'flujos' => $flujosSem,
                    'tareas_completadas' => $tareasSem,
                    'tareas_totales' => $totalTareasSem,
                    'efectividad' => $efectividad,
                ];
            }
        }

        $topDescansos = DB::table('session_breaks')
            ->join('user_sessions', 'session_breaks.user_session_id', '=', 'user_sessions.id')
            ->join('users', 'user_sessions.user_id', '=', 'users.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->whereBetween('session_breaks.break_start', [$inicio, $fin])
            ->whereNotNull('session_breaks.break_end')
            ->select(
                'users.id',
                'users.name',
                'users.apodo',
                'roles.display_name as rol',
                DB::raw('SUM(TIMESTAMPDIFF(SECOND, session_breaks.break_start, session_breaks.break_end)) as total_seconds')
            )
            ->groupBy('users.id', 'users.name', 'users.apodo', 'roles.display_name')
            ->orderBy('total_seconds', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($row) {
                $horas = floor($row->total_seconds / 3600);
                $minutos = floor(($row->total_seconds % 3600) / 60);
                $row->total_formatted = $horas > 0 ? "{$horas}h {$minutos}m" : "{$minutos}m";
                return $row;
            });

        return [
            'flujos_creados' => $flujosCreados,
            'flujos_realizados' => $flujosRealizados,
            'detalle_flujos' => $detalleFlujos,
            'participantes' => $participantes,
            'modificaciones' => $modificaciones,
            'nuevos_ingresos' => $nuevosIngresos,
            'empleados_eliminados' => $empleadosEliminados,
            'flujos_eliminados' => $flujosEliminados,
            'cambios_roles' => $cambiosRoles,
            'solicitudes' => $solicitudes,
            'registros_pendientes' => $registrosPendientes,
            'top_descansos' => $topDescansos,
            'pie_equipos' => $pieEquipos,
            'pie_chart_base64' => $pieChartBase64,
            'semanas' => $semanas,
            'equipos' => $equipos,
            'mejor_rendimiento' => $mejorRendimiento,
            'peor_rendimiento' => $peorRendimiento,
            'total_usuarios' => $usuarios->count(),
        ];
    }
}
