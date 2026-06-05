<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\LogAuditoria;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipoController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (!$user || !in_array($user->role?->slug, ['super_admin', 'administrador'])) {
                abort(403, 'No tienes permiso para gestionar equipos.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    public function index(): View
    {
        $equipos = Equipo::with(['gerente', 'miembros'])->orderByDesc('id')->get();
        return view('equipos.index', compact('equipos'));
    }

    public function create(): View
    {
        $gerentes = User::whereHas('role', fn($q) => $q->whereIn('slug', ['gerente']))->get();
        $lideres = User::whereHas('role', fn($q) => $q->whereIn('slug', ['lider_equipo', 'gerente', 'administrador']))->get();
        $empleados = User::whereHas('role', fn($q) => $q->whereIn('slug', ['empleado', 'lider_equipo', 'gerente', 'administrador']))->get();

        return view('equipos.form', compact('gerentes', 'lideres', 'empleados'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'gerente_id' => 'required|exists:users,id',
            'lideres' => 'nullable|array',
            'lideres.*' => 'exists:users,id',
            'empleados' => 'nullable|array',
            'empleados.*' => 'exists:users,id',
        ]);

        $equipo = Equipo::create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'gerente_id' => $data['gerente_id'],
            'created_by' => $request->user()->id,
        ]);

        $miembros = [];
        foreach ($data['lideres'] ?? [] as $uid) {
            $miembros[$uid] = ['rol' => 'lider_equipo'];
        }
        foreach ($data['empleados'] ?? [] as $uid) {
            if (!isset($miembros[$uid])) {
                $miembros[$uid] = ['rol' => 'empleado'];
            }
        }
        if (!empty($miembros)) {
            $equipo->miembros()->attach($miembros);
        }

        LogAuditoria::registrar(
            'crear_equipo',
            'Equipo',
            $equipo->id,
            "Equipo '{$equipo->nombre}' creado con gerente ID {$data['gerente_id']}",
        );

        return redirect()->route('equipos.index')->with('success', 'Equipo creado correctamente.');
    }

    public function edit(Equipo $equipo): View
    {
        $gerentes = User::whereHas('role', fn($q) => $q->whereIn('slug', ['gerente']))->get();
        $lideres = User::whereHas('role', fn($q) => $q->whereIn('slug', ['lider_equipo', 'gerente', 'administrador']))->get();
        $empleados = User::whereHas('role', fn($q) => $q->whereIn('slug', ['empleado', 'lider_equipo', 'gerente', 'administrador']))->get();

        $equipo->load('miembros');

        return view('equipos.form', compact('equipo', 'gerentes', 'lideres', 'empleados'));
    }

    public function update(Request $request, Equipo $equipo): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'gerente_id' => 'required|exists:users,id',
            'lideres' => 'nullable|array',
            'lideres.*' => 'exists:users,id',
            'empleados' => 'nullable|array',
            'empleados.*' => 'exists:users,id',
        ]);

        $equipo->update([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'gerente_id' => $data['gerente_id'],
        ]);

        $miembros = [];
        foreach ($data['lideres'] ?? [] as $uid) {
            $miembros[$uid] = ['rol' => 'lider_equipo'];
        }
        foreach ($data['empleados'] ?? [] as $uid) {
            if (!isset($miembros[$uid])) {
                $miembros[$uid] = ['rol' => 'empleado'];
            }
        }
        $equipo->miembros()->sync($miembros);

        LogAuditoria::registrar(
            'actualizar_equipo',
            'Equipo',
            $equipo->id,
            "Equipo '{$equipo->nombre}' actualizado",
        );

        return redirect()->route('equipos.index')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Equipo $equipo): RedirectResponse
    {
        LogAuditoria::registrar(
            'eliminar_equipo',
            'Equipo',
            $equipo->id,
            "Equipo '{$equipo->nombre}' eliminado",
        );

        $equipo->delete();

        return redirect()->route('equipos.index')->with('success', 'Equipo eliminado correctamente.');
    }
}
