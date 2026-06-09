<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BreakController extends Controller
{
    private function obtenerSesion(): ?UserSession
    {
        $session = UserSession::where('user_id', Auth::id())->whereNull('logged_out_at')->latest()->first();
        if (!$session) {
            $session = UserSession::create(['user_id' => Auth::id(), 'logged_in_at' => now()]);
        }
        return $session;
    }

    public function start(): JsonResponse
    {
        $session = $this->obtenerSesion();
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No hay sesión activa.'], 400);
        }

        if ($session->activeBreaks()->exists()) {
            return response()->json(['success' => false, 'message' => 'Ya tienes un descanso activo.'], 400);
        }

        $session->breaks()->create(['break_start' => now()]);

        return response()->json(['success' => true, 'message' => 'Descanso iniciado.']);
    }

    public function end(): JsonResponse
    {
        $session = $this->obtenerSesion();
        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No hay sesión activa.'], 400);
        }

        $break = $session->activeBreaks()->first();
        if (!$break) {
            return response()->json(['success' => false, 'message' => 'No tienes un descanso activo.'], 400);
        }

        $break->update(['break_end' => now()]);

        return response()->json(['success' => true, 'message' => 'Descanso culminado.']);
    }

    public function status(): JsonResponse
    {
        $session = $this->obtenerSesion();
        if (!$session) {
            return response()->json(['success' => false, 'on_break' => false]);
        }

        return response()->json([
            'success'       => true,
            'on_break'      => $session->activeBreaks()->exists(),
            'break_seconds' => $session->activeBreakSeconds,
        ]);
    }
}
