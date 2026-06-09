<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\FlujoTrabajo;
use App\Models\LogAuditoria;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DisenadorController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if (!$user || !in_array($user->role?->slug, ['super_admin', 'administrador'])) {
            abort(403, 'No tienes permiso para acceder al Diseñador.');
        }

        $flujos = FlujoTrabajo::with('estados')->orderByDesc('id')->get();

        $equipos = Equipo::with('miembros.role')->orderBy('nombre')->get();
        $equiposData = $equipos->mapWithKeys(fn($e) => [$e->id => $e->miembros->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'role_slug' => $m->role?->slug ?? '',
            'role_display' => $m->role?->display_name ?? $m->role?->slug ?? '',
            'pivot_rol' => $m->pivot?->rol ?? '',
        ])]);

        return view('disenador', compact('flujos', 'equipos', 'equiposData'));
    }

    public function guardarPasos(Request $request, FlujoTrabajo $flujo): JsonResponse
    {
        $data = $request->validate([
            'pasos'  => 'sometimes|array',
            'diseno' => 'sometimes|array',
        ]);

        if (isset($data['pasos'])) {
            $flujo->pasos = $data['pasos'];
        }

        if (isset($data['diseno'])) {
            $flujo->diseno = $data['diseno'];
        }

        $flujo->save();

        LogAuditoria::registrar(
            'disenar_flujo',
            'FlujoTrabajo',
            $flujo->id,
            "Flujo '{$flujo->nombre}' actualizado con nuevo diseño",
            ['pasos' => $data['pasos'] ?? null, 'diseno' => $data['diseno'] ?? null],
        );

        return response()->json(['success' => true]);
    }

    public function obtenerPasos(FlujoTrabajo $flujo): JsonResponse
    {
        return response()->json([
            'pasos'  => $flujo->pasos ?? [],
            'diseno' => $flujo->diseno ?? (object)[],
        ]);
    }
}
