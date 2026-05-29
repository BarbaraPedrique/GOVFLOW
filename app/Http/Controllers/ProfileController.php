<?php

namespace App\Http\Controllers;

use App\Models\FlujoTrabajo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('editar_perfil', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apodo' => ['nullable', 'string', 'max:255'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->name = $request->nombre;
        $user->apodo = $request->apodo;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        $user->descripcion = $request->descripcion;

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('avatars', 'public');
        }

        $user->save();

        return redirect()->route('perfil.edit')->with('status', 'Perfil actualizado correctamente.');
    }

    public function show(): View
    {
        $user = Auth::user()->load('role');

        $completados = FlujoTrabajo::where('user_id', $user->id)
            ->where('estado', 'Completado')
            ->whereNotNull('fecha_completado')
            ->orderByDesc('fecha_completado')
            ->get();

        $eficienciaMensual = FlujoTrabajo::where('user_id', $user->id)
            ->whereNotNull('fecha_completado')
            ->whereNotNull('fecha_limite')
            ->selectRaw("DATE_FORMAT(fecha_completado, '%Y-%m') as mes")
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN fecha_completado <= fecha_limite THEN 1 ELSE 0 END) as a_tiempo")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(function ($item) {
                $item->eficiencia = $item->total > 0
                    ? round(($item->a_tiempo / $item->total) * 100, 1)
                    : 0;
                return $item;
            });

        $eficienciaGlobal = $completados->count() > 0
            ? round(($completados->where('completado_a_tiempo', true)->count() / max($completados->count(), 1)) * 100, 1)
            : 0;

        return view('mi_perfil', compact(
            'user',
            'completados',
            'eficienciaMensual',
            'eficienciaGlobal'
        ));
    }
}
