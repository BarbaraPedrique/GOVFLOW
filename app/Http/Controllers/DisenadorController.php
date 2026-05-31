<?php

namespace App\Http\Controllers;

use App\Models\FlujoTrabajo;
use App\Models\LogAuditoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        return view('disenador', compact('flujos'));
    }

    public function guardarPasos(Request $request, FlujoTrabajo $flujo): JsonResponse
    {
        $data = $request->validate([
            'pasos'  => 'sometimes|json',
            'diseno' => 'sometimes|json',
        ]);

        if (isset($data['pasos'])) {
            $flujo->update(['pasos' => $data['pasos']]);
        }

        if (isset($data['diseno'])) {
            $flujo->update(['diseno' => $data['diseno']]);
        }

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
            'pasos'  => json_decode($flujo->pasos ?? '[]'),
            'diseno' => json_decode($flujo->diseno ?? '{}'),
        ]);
    }
}
