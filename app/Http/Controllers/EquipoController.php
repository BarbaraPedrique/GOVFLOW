<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\LogAuditoria;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipoController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        $user = $request->user();
        if (!$user || !in_array($user->role?->slug, ['super_admin', 'administrador'])) {
            abort(403, 'No tienes permiso para gestionar equipos.');
        }
    }

    public function index(): View
    {
        $equipos = Equipo::with(['gerente', 'miembros'])->orderByDesc('id')->get();
        return view('equipos.index', compact('equipos'));
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        $equipo = null;
        $gerentes = User::whereHas('role', fn($q) => $q->whereIn('slug', ['gerente']))->orderBy('name')->get();
        $lideres = User::whereHas('role', fn($q) => $q->whereIn('slug', ['lider_equipo', 'gerente', 'administrador']))->orderBy('name')->get();
        $empleados = User::whereHas('role', fn($q) => $q->whereIn('slug', ['empleado', 'lider_equipo', 'gerente', 'administrador']))->orderBy('name')->get();

        return view('equipos.form', compact('equipo', 'gerentes', 'lideres', 'empleados'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

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

        $icono = '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>';
        foreach (array_keys($miembros) as $uid) {
            Notificacion::create([
                'user_id' => $uid,
                'tipo' => 'equipo_asignado',
                'titulo' => 'Nuevo equipo',
                'mensaje' => "Has sido agregado al equipo '{$equipo->nombre}'",
                'icono' => $icono,
                'color' => 'text-blue-500',
                'url' => route('equipos.index'),
            ]);
        }

        LogAuditoria::registrar(
            'crear_equipo',
            'Equipo',
            $equipo->id,
            "Equipo '{$equipo->nombre}' creado con gerente ID {$data['gerente_id']}",
        );

        return redirect()->route('equipos.index')->with('success', 'Equipo creado correctamente.');
    }

    public function edit(Request $request, Equipo $equipo): View
    {
        $this->authorizeAdmin($request);

        $gerentes = User::whereHas('role', fn($q) => $q->whereIn('slug', ['gerente']))->orderBy('name')->get();
        $lideres = User::whereHas('role', fn($q) => $q->whereIn('slug', ['lider_equipo', 'gerente', 'administrador']))->orderBy('name')->get();
        $empleados = User::whereHas('role', fn($q) => $q->whereIn('slug', ['empleado', 'lider_equipo', 'gerente', 'administrador']))->orderBy('name')->get();

        $equipo->load('miembros');

        return view('equipos.form', compact('equipo', 'gerentes', 'lideres', 'empleados'));
    }

    public function update(Request $request, Equipo $equipo): RedirectResponse
    {
        $this->authorizeAdmin($request);

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

        $miembrosActuales = $equipo->miembros()->pluck('equipo_user.user_id')->toArray();
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

        $nuevosIds = array_diff(array_keys($miembros), $miembrosActuales);
        if (!empty($nuevosIds)) {
            $icono = '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>';
            foreach ($nuevosIds as $uid) {
                Notificacion::create([
                    'user_id' => $uid,
                    'tipo' => 'equipo_asignado',
                    'titulo' => 'Nuevo equipo',
                    'mensaje' => "Has sido agregado al equipo '{$equipo->nombre}'",
                    'icono' => $icono,
                    'color' => 'text-blue-500',
                    'url' => route('equipos.index'),
                ]);
            }
        }

        LogAuditoria::registrar(
            'actualizar_equipo',
            'Equipo',
            $equipo->id,
            "Equipo '{$equipo->nombre}' actualizado",
        );

        return redirect()->route('equipos.index')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Request $request, Equipo $equipo): RedirectResponse
    {
        $this->authorizeAdmin($request);

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
