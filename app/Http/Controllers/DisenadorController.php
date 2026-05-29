<?php

namespace App\Http\Controllers;

use App\Models\FlujoTrabajo;
use App\Models\LogAuditoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisenadorController extends Controller
{
    public function index(): View
    {
        $flujos = FlujoTrabajo::where('user_id', auth()->id())
            ->orWhereIn('user_id', function ($q) {
                $q->select('id')->from('users')->whereIn('role_id', function ($q) {
                    $q->select('id')->from('roles')->whereIn('slug', ['administrador', 'gerente']);
                });
            })
            ->orderByDesc('id')
            ->get();

        return view('disenador', compact('flujos'));
    }

    public function guardarPasos(Request $request, FlujoTrabajo $flujo): JsonResponse
    {
        $data = $request->validate([
            'pasos' => 'required|json',
        ]);

        $flujo->update(['pasos' => $data['pasos']]);

        LogAuditoria::registrar(
            'disenar_flujo',
            'FlujoTrabajo',
            $flujo->id,
            "Flujo '{$flujo->nombre}' actualizado con nuevo diseño de pasos",
            ['pasos' => $data['pasos']],
        );

        return response()->json(['success' => true]);
    }

    public function obtenerPasos(FlujoTrabajo $flujo): JsonResponse
    {
        return response()->json([
            'pasos' => json_decode($flujo->pasos ?? '[]'),
        ]);
    }
}
