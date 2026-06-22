<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->status === 'pendiente') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Tu cuenta está pendiente de aprobación. Espera a que un administrador la active.',
                ])->onlyInput('email');
            }

            if ($user->status === 'suspendido') {
                $request->session()->regenerate();
                UserSession::create(['user_id' => $user->id, 'logged_in_at' => now()]);
                return redirect()->intended('/inicio');
            }

            $request->session()->regenerate();
            UserSession::create(['user_id' => $user->id, 'logged_in_at' => now()]);
            return redirect()->intended('/inicio');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $session = UserSession::query()->where('user_id', $user->id)->whereNull('logged_out_at')->latest()->first();
            if ($session) {
                $session->breaks()->whereNull('break_end')->update(['break_end' => now()]);
                $session->update(['logged_out_at' => now()]);
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
