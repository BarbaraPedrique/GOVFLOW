<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::query()->where('user_id', Auth::id())
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
        if ($notificacion->user_id !== Auth::id()) {
            abort(403);
        }
        $notificacion->update(['leido' => true]);
        return response()->json(['success' => true]);
    }

    public function marcarTodasLeido(): JsonResponse
    {
        DB::table('notificaciones')->where('user_id', Auth::id())
            ->where('leido', false)
            ->update(['leido' => true]);
        return response()->json(['success' => true]);
    }
}
