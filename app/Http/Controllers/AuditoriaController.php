<?php

namespace App\Http\Controllers;

use App\Models\FlujoTrabajo;
use App\Models\LogAuditoria;
use App\Models\Role;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    private const HIERARCHY = [
        'super_admin'   => 1,
        'administrador' => 2,
        'gerente'       => 3,
        'lider_equipo'  => 4,
        'empleado'      => 5,
    ];

    public function index(Request $request)
    {
        $user = $request->user();
        $query = LogAuditoria::with('user');

        $userRank = $user ? (self::HIERARCHY[$user->role?->slug] ?? 99) : 99;

        if ($userRank >= 3) {
            $visibleSlugs = array_keys(array_filter(self::HIERARCHY, fn($r) => $r >= $userRank));
            $visibleUserIds = User::whereIn('role_id', Role::whereIn('slug', $visibleSlugs)->pluck('id'))->pluck('id');
            $query->whereIn('user_id', $visibleUserIds);
        }

        if ($request->filled('flujo_id')) {
            $query->where('entidad_type', 'App\Models\FlujoTrabajo')
                  ->where('entidad_id', $request->flujo_id);
        }

        if ($request->filled('departamento')) {
            $flujoIds = FlujoTrabajo::where('departamento', $request->departamento)->pluck('id');
            $query->where(function ($q) use ($flujoIds) {
                $q->whereIn('entidad_id', $flujoIds)
                  ->where('entidad_type', 'App\Models\FlujoTrabajo');
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $logs = $query->latest('created_at')->get();

        $grupos = $logs->groupBy(function ($log) {
            return $log->entidad_type . '::' . ($log->entidad_id ?? '0');
        })->map(function ($items, $key) {
            $parts = explode('::', $key);
            $type = $parts[0];
            $id = $parts[1] !== '0' ? $parts[1] : null;
            $entityName = $id ? $this->resolveEntityName($type, $id) : 'Sistema';
            $entityShort = class_basename($type);

            return (object) [
                'entity_name'  => $entityName,
                'entity_type'  => $entityShort,
                'entity_id'    => $id,
                'logs'         => $items,
                'cantidad'     => $items->count(),
                'ultimo'       => $items->first()->created_at,
            ];
        })->sortByDesc(fn ($g) => $g->ultimo);

        $flujos = FlujoTrabajo::orderBy('nombre')->get();
        $departamentos = FlujoTrabajo::distinct()->pluck('departamento')->sort();
        $totalEventos = $logs->count();
        $proyectosActivos = $grupos->count();
        $userRank = $userRank;

        return view('auditoria', compact(
            'grupos', 'flujos', 'departamentos',
            'totalEventos', 'proyectosActivos', 'userRank'
        ));
    }

    private function resolveEntityName(string $type, ?string $id): string
    {
        if (!$id) return 'Sistema';

        return match ($type) {
            'App\Models\FlujoTrabajo' => FlujoTrabajo::find($id)?->nombre ?? "Flujo #$id",
            'App\Models\Tarea'        => Tarea::find($id)?->titulo ?? "Tarea #$id",
            'App\Models\User'         => User::find($id)?->name ?? "Usuario #$id",
            default                   => class_basename($type) . " #$id",
        };
    }
}
