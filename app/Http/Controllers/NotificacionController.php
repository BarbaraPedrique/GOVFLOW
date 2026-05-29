<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::where('user_id', auth()->id())
            ->recientes()
            ->limit(20)
            ->get();

        $noLeidas = $notificaciones->where('leido', false)->count();

        if (request()->wantsJson() || request()->has('ajax')) {
            return response()->json([
                'notificaciones' => $notificaciones,
                'noLeidas' => $noLeidas,
            ]);
        }

        return view('notificaciones', compact('notificaciones', 'noLeidas'));
    }

    public function marcarLeido(Notificacion $notificacion): JsonResponse
    {
        if ($notificacion->user_id !== auth()->id()) {
            abort(403);
        }
        $notificacion->update(['leido' => true]);
        return response()->json(['success' => true]);
    }

    public function marcarTodasLeido(): JsonResponse
    {
        Notificacion::where('user_id', auth()->id())
            ->where('leido', false)
            ->update(['leido' => true]);
        return response()->json(['success' => true]);
    }
}
