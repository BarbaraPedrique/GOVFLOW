<?php

namespace App\Http\Controllers;

use App\Models\FlujoTrabajo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
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
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ], [
            'foto.image' => 'El campo foto debe ser una imagen.',
            'foto.mimes' => 'El campo foto debe ser un archivo de tipo: jpeg, png, jpg, gif, webp.',
            'foto.max' => 'El campo foto no debe ser mayor a 5120 kilobytes.',
        ]);

        $user->name = $request->nombre;
        $user->apodo = $request->apodo;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        $user->descripcion = $request->descripcion;

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $avatarsDir = Storage::disk('public')->path('avatars');
            if (!File::exists($avatarsDir)) {
                File::makeDirectory($avatarsDir, 0755, true);
            }
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
        $user = Auth::user()->load(['role', 'equipos.gerente', 'equiposDirigidos']);

        $equipos = $user->equiposDirigidos->merge($user->equipos)->unique('id');
        $equipos->loadMissing(['miembros', 'gerente']);
        $completados = DB::table('flujos_trabajo')->where('user_id', $user->id)
            ->where('estado', 'Completado')
            ->whereNotNull('fecha_completado')
            ->orderByDesc('fecha_completado')
            ->get();

        $eficienciaMensual = DB::table('flujos_trabajo')->where('user_id', $user->id)
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
            'equipos',
            'completados',
            'eficienciaMensual',
            'eficienciaGlobal'
        ));
    }
}
