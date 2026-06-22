<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\LogAuditoria;
use App\Models\Notificacion;
use App\Models\Role;
use App\Models\RoleHistorial;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PersonalController extends Controller
{
    private function autorizar(): void
    {
        if (!in_array(Auth::user()->role?->slug, ['super_admin', 'administrador'])) {
            abort(403);
        }
    }

    public function index(Request $request): View
    {
        $this->autorizar();

        $base = User::query()->with(['role', 'equipos', 'roleHistorial.role', 'tareas', 'sessions.breaks'])
            ->where('status', '!=', 'pendiente');

        if ($request->filled('role_id')) {
            $base->where('role_id', $request->role_id);
        }
        if ($request->filled('equipo_id')) {
            $base->whereHas('equipos', fn($q) => $q->where('equipo_id', $request->equipo_id));
        }
        if ($request->filled('fecha_desde')) {
            $base->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $base->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $base->orderBy('name');

        $allUsers = (clone $base)->get();

        $personal = (clone $base)->paginate(8)->through(function ($user) {
            $totalTareas = $user->tareas->count();
            $completadas = $user->tareas->where('completada', true)->count();
            $rendimiento = $totalTareas > 0 ? round(($completadas / $totalTareas) * 100) : 0;

            $equipos = $user->equipos->map(function ($eq) {
                $rol = $eq->pivot->rol ?? 'miembro';
                $nombreRol = match ($rol) {
                    'lider_equipo' => 'Líder',
                    'empleado' => 'Empleado',
                    default => 'Miembro'
                };
                return (object) [
                    'id' => $eq->id,
                    'nombre' => $eq->nombre,
                    'rol' => $nombreRol,
                ];
            });

            $rolesAnteriores = $user->roleHistorial
                ->where('role_id', '!=', $user->role_id)
                ->pluck('role.name')
                ->toArray();

            $diasDesdeRegistro = now()->diffInDays($user->created_at);
            $tiempoRegistro = match (true) {
                $diasDesdeRegistro < 1 => 'Hoy',
                $diasDesdeRegistro < 30 => $diasDesdeRegistro . ' día(s)',
                $diasDesdeRegistro < 365 => floor($diasDesdeRegistro / 30) . ' mes(es)',
                default => floor($diasDesdeRegistro / 365) . ' año(s)',
            };

            return (object) [
                'id' => $user->id,
                'name' => $user->name,
                'apodo' => $user->apodo,
                'email' => $user->email,
                'foto' => $user->foto_url,
                'role' => $user->role,
                'status' => $user->status,
                'rendimiento' => $rendimiento,
                'totalTareas' => $totalTareas,
                'completadas' => $completadas,
                'equipos' => $equipos,
                'rolesAnteriores' => $rolesAnteriores,
                'tiempoRegistro' => $tiempoRegistro,
                'creado' => $user->created_at,
                'estrellas' => $user->calcularEstrellasMes(),
            ];
        });

        $personal->appends(request()->query());

        $stats = (object) [
            'total' => $allUsers->count(),
            'conEquipo' => $allUsers->filter(fn($p) => $p->equipos->isNotEmpty())->count(),
            'sinEquipo' => $allUsers->filter(fn($p) => $p->equipos->isEmpty())->count(),
            'rendimientoPromedio' => $allUsers->avg(fn($p) => $p->tareas->count() > 0 ? round(($p->tareas->where('completada', true)->count() / $p->tareas->count()) * 100) : 0),
        ];

        $roles = DB::table('roles')->whereNotIn('slug', ['super_admin'])->orderBy('name')->get();
        $equipos = DB::table('equipos')->orderBy('nombre')->get();

        return view('personal.index', compact('personal', 'stats', 'roles', 'equipos'));
    }

    public function detalle(User $user): JsonResponse
    {
        $this->autorizar();

        $user->load(['role', 'equipos', 'roleHistorial.role']);
        $totalTareas = $user->tareas()->count();
        $completadas = $user->tareas()->where('completada', true)->count();
        $rendimiento = $totalTareas > 0 ? round(($completadas / $totalTareas) * 100) : 0;

        $equipos = $user->equipos->map(function ($eq) {
            return [
                'id' => $eq->id,
                'nombre' => $eq->nombre,
                'rol' => $eq->pivot->rol ?? 'miembro',
            ];
        });

        $rolesAnteriores = $user->roleHistorial
            ->where('role_id', '!=', $user->role_id)
            ->map(fn($h) => ['role' => $h->role?->name ?? '?', 'desde' => $h->asignado_en?->format('d/m/Y') ?? '—']);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'apodo' => $user->apodo,
            'email' => $user->email,
            'foto' => $user->foto_url,
            'role' => ['id' => $user->role?->id, 'name' => $user->role?->display_name ?? $user->role?->name, 'slug' => $user->role?->slug],
            'status' => $user->status,
            'status_label' => $user->statusLabel(),
            'rendimiento' => $rendimiento,
            'totalTareas' => $totalTareas,
            'completadas' => $completadas,
            'equipos' => $equipos,
            'rolesAnteriores' => $rolesAnteriores,
            'creado' => $user->created_at?->format('d/m/Y'),
            'tiempoRegistro' => $this->tiempoRelativo($user->created_at),
            'estrellas' => $user->calcularEstrellasMes(),
        ]);
    }

    public function cambiarRol(Request $request, User $user): RedirectResponse
    {
        $this->autorizar();

        if ($user->role?->slug === 'super_admin') {
            abort(403, 'No puedes cambiar el rol del Super Admin.');
        }

        $request->validate(['role_id' => 'required|exists:roles,id']);
        $nuevoRol = Role::findOrFail($request->role_id);

        if ($user->role_id !== $nuevoRol->id) {
            if ($user->role_id) {
                RoleHistorial::create([
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                    'asignado_en' => now(),
                ]);
            }

            $user->update(['role_id' => $nuevoRol->id]);

            Notificacion::create([
                'user_id' => $user->id,
                'tipo' => 'rol_cambiado',
                'titulo' => 'Rol actualizado',
                'mensaje' => "Tu rol ha sido cambiado a {$nuevoRol->display_name} por " . Auth::user()->name . '.',
                'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>',
                'color' => 'text-blue-500',
                'url' => route('perfil'),
            ]);

            LogAuditoria::registrar(
                'cambio_rol',
                'User',
                $user->id,
                "Rol de {$user->name} cambiado a {$nuevoRol->name} por " . Auth::user()->name,
            );
        }

        return redirect()->route('personal.index')->with('success', "Rol de {$user->name} actualizado a {$nuevoRol->display_name}.");
    }

    public function cambiarEquipo(Request $request, User $user): RedirectResponse
    {
        $this->autorizar();

        $request->validate([
            'equipo_id' => 'nullable|exists:equipos,id',
            'rol_equipo' => 'required_with:equipo_id|in:lider_equipo,empleado',
        ]);

        $user->equipos()->detach();

        if ($request->filled('equipo_id')) {
            $user->equipos()->attach($request->equipo_id, ['rol' => $request->rol_equipo]);

            $equipo = Equipo::query()->find($request->equipo_id);
            Notificacion::create([
                'user_id' => $user->id,
                'tipo' => 'equipo_asignado',
                'titulo' => 'Asignado a equipo',
                'mensaje' => "Has sido asignado al equipo \"{$equipo->nombre}\" como " . ($request->rol_equipo === 'lider_equipo' ? 'Líder' : 'Empleado') . '.',
                'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
                'color' => 'text-emerald-500',
                'url' => route('equipos.index'),
            ]);

            LogAuditoria::registrar(
                'asignar_equipo',
                'User',
                $user->id,
                "{$user->name} asignado a equipo {$equipo->nombre} como {$request->rol_equipo} por " . Auth::user()->name,
            );
        } else {
            LogAuditoria::registrar(
                'remover_equipo',
                'User',
                $user->id,
                "{$user->name} removido de todos sus equipos por " . Auth::user()->name,
            );
        }

        return redirect()->route('personal.index')->with('success', 'Equipo actualizado correctamente.');
    }

    public function toggleSuspender(User $user): RedirectResponse
    {
        $this->autorizar();

        if ($user->role?->slug === 'super_admin') {
            abort(403, 'No puedes suspender al Super Admin.');
        }

        if ($user->status === User::STATUS_SUSPENDIDO) {
            $user->update(['status' => User::STATUS_ACTIVO]);
            Notificacion::create([
                'user_id' => $user->id,
                'tipo' => 'suspension_levantada',
                'titulo' => 'Suspensión levantada',
                'mensaje' => 'Tu suspensión ha sido levantada por ' . Auth::user()->name . '. Ya puedes acceder a todas las funciones del sistema.',
                'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'color' => 'text-emerald-500',
                'url' => route('inicio'),
            ]);
            LogAuditoria::registrar('levantar_suspension', 'User', $user->id, "Suspensión de {$user->name} levantada por " . Auth::user()->name);
            return redirect()->route('personal.index')->with('success', "Suspensión de {$user->name} levantada.");
        }

        $user->update(['status' => User::STATUS_SUSPENDIDO]);
        Notificacion::create([
            'user_id' => $user->id,
            'tipo' => 'cuenta_suspendida',
            'titulo' => 'Cuenta suspendida',
            'mensaje' => 'Tu cuenta ha sido suspendida temporalmente por ' . Auth::user()->name . '. Solo puedes ver el inicio, tu perfil y realizar solicitudes.',
            'icono' => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>',
            'color' => 'text-amber-500',
            'url' => route('inicio'),
        ]);
        LogAuditoria::registrar('suspender_cuenta', 'User', $user->id, "Cuenta de {$user->name} suspendida por " . Auth::user()->name);
        return redirect()->route('personal.index')->with('success', "Cuenta de {$user->name} suspendida.");
    }

    public function eliminar(User $user): RedirectResponse
    {
        if (Auth::user()->role?->slug !== 'super_admin') {
            abort(403, 'Solo el Super Admin puede eliminar perfiles.');
        }

        if ($user->role?->slug === 'super_admin') {
            abort(403, 'No puedes eliminar al Super Admin.');
        }

        $nombre = $user->name;
        LogAuditoria::registrar('eliminar_usuario', 'User', $user->id, "Usuario {$nombre} eliminado por " . Auth::user()->name);
        User::destroy($user->id);

        return redirect()->route('personal.index')->with('success', "Perfil de {$nombre} eliminado.");
    }

    private function tiempoRelativo($fecha): string
    {
        $dias = now()->diffInDays($fecha);
        return match (true) {
            $dias < 1 => 'Hoy',
            $dias < 30 => $dias . ' día(s)',
            $dias < 365 => floor($dias / 30) . ' mes(es)',
            default => floor($dias / 365) . ' año(s)',
        };
    }
}
