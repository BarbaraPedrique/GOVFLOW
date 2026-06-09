<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->status === User::STATUS_SUSPENDIDO) {
            $allowedRoutes = [
                'inicio',
                'perfil', 'perfil.edit', 'perfil.update',
                'solicitar.store', 'solicitudes.mis', 'solicitudes.aprobar', 'solicitudes.rechazar',
                'logout',
                'notificaciones.index', 'notificaciones.leido', 'notificaciones.marcar-todas',
            ];

            $routeName = $request->route()?->getName();

            if (!$routeName || !in_array($routeName, $allowedRoutes)) {
                return redirect()->route('inicio')->with('error', 'Tu cuenta está suspendida. Solo puedes acceder a funciones limitadas.');
            }
        }

        return $next($request);
    }
}
