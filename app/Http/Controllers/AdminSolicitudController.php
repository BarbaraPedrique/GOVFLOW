<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminSolicitudController extends Controller
{
    private function autorizarSuperAdmin(): void
    {
        if (Auth::user()->role?->slug !== 'super_admin') {
            abort(403);
        }
    }

    public function index()
    {
        return redirect()->route('solicitudes.mis');
    }

    public function aprobar(User $user)
    {
        $this->autorizarSuperAdmin();

        if ($user->status !== User::STATUS_PENDIENTE) {
            return redirect()->route('admin.solicitudes')
                ->with('error', 'Esta solicitud ya fue procesada.');
        }

        DB::transaction(function () use ($user) {
            $user->update(['status' => User::STATUS_ACTIVO]);

            DB::table('tareas')->where('categoria', 'Solicitud')
                ->where('descripcion', 'like', "%user_id:{$user->id}%")
                ->update(['completada' => true, 'status' => 'aprobado']);

            Notificacion::create([
                'user_id' => $user->id,
                'tipo'    => 'cuenta_aprobada',
                'titulo'  => 'Cuenta aprobada',
                'mensaje' => 'Tu solicitud de registro fue aprobada. Ya puedes iniciar sesión.',
                'icono'   => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'color'   => 'text-emerald-500',
                'url'     => route('login'),
                'leido'   => false,
            ]);
        });

        return redirect()->route('admin.solicitudes')
            ->with('status', "Cuenta de {$user->name} aprobada correctamente.");
    }

    public function rechazar(User $user)
    {
        $this->autorizarSuperAdmin();

        if ($user->status !== User::STATUS_PENDIENTE) {
            return redirect()->route('admin.solicitudes')
                ->with('error', 'Esta solicitud ya fue procesada.');
        }

        $nombre = $user->name;

        DB::beginTransaction();
        try {
            DB::table('tareas')->where('categoria', 'Solicitud')
                ->where('descripcion', 'like', "%user_id:{$user->id}%")
                ->update(['completada' => true, 'status' => 'rechazado']);
            User::destroy($user->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('admin.solicitudes')
            ->with('status', "Solicitud de {$nombre} rechazada y eliminada.");
    }

    public function aprobarTarea(Tarea $tarea)
    {
        $this->autorizarSuperAdmin();

        $userId = $this->extractUserId($tarea);

        if (!$userId) {
            return response()->json(['success' => false, 'error' => 'No se pudo identificar el usuario.']);
        }

        $user = User::query()->find($userId);

        if (!$user || $user->status !== User::STATUS_PENDIENTE) {
            return response()->json(['success' => false, 'error' => 'Usuario no encontrado o ya procesado.']);
        }

        DB::transaction(function () use ($user, $tarea) {
            $user->update(['status' => User::STATUS_ACTIVO]);
            $tarea->update(['completada' => true, 'status' => 'aprobado']);

            Notificacion::create([
                'user_id' => $user->id,
                'tipo'    => 'cuenta_aprobada',
                'titulo'  => 'Cuenta aprobada',
                'mensaje' => 'Tu solicitud de registro fue aprobada. Ya puedes iniciar sesión.',
                'icono'   => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'color'   => 'text-emerald-500',
                'url'     => route('login'),
                'leido'   => false,
            ]);
        });

        return response()->json(['success' => true]);
    }

    public function rechazarTarea(Tarea $tarea)
    {
        $this->autorizarSuperAdmin();

        $userId = $this->extractUserId($tarea);

        if (!$userId) {
            return response()->json(['success' => false, 'error' => 'No se pudo identificar el usuario.']);
        }

        $user = DB::table('users')->where('id', $userId)->first();

        if (!$user || $user->status !== User::STATUS_PENDIENTE) {
            return response()->json(['success' => false, 'error' => 'Usuario no encontrado o ya procesado.']);
        }

        $nombre = $user->name;

        DB::beginTransaction();
        try {
            $tarea->update(['completada' => true, 'status' => 'rechazado']);
            User::destroy($user->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json(['success' => true, 'message' => "Solicitud de {$nombre} rechazada."]);
    }

    private function extractUserId(Tarea $tarea): ?int
    {
        if (preg_match('/user_id:(\d+)/', $tarea->descripcion ?? '', $m)) {
            return (int) $m[1];
        }
        return null;
    }
}
