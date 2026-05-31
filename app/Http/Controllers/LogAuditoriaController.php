<?php

namespace App\Http\Controllers;

use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogAuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if (!$user || !in_array($user->role?->slug, ['super_admin', 'administrador'])) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $query = LogAuditoria::with('user')->orderByDesc('created_at');

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('entidad')) {
            $query->where('entidad_type', $request->entidad);
        }

        $logs = $query->paginate(25);
        $acciones = LogAuditoria::select('accion')->distinct()->pluck('accion');
        $entidades = LogAuditoria::select('entidad_type')->distinct()->pluck('entidad_type');

        return view('logs_auditoria', compact('logs', 'acciones', 'entidades'));
    }
}
