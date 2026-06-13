<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use App\Models\Role;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:gerente,administrador,lider_equipo,empleado',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required'       => 'El nombre es obligatorio.',
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.unique'        => 'Este correo ya está registrado.',
            'role.required'       => 'Selecciona un rol.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'  => 'Las contraseñas no coinciden.',
        ]);

        $roleModel = Role::where('slug', $request->role)->firstOrFail();

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role_id'  => $roleModel->id,
            'status'   => User::STATUS_PENDIENTE,
            'password' => Hash::make($request->password),
        ]);

        $roleDisplay = $roleModel->display_name ?? ucfirst($request->role);
        $titulo = "{$request->name} solicitó ({$roleDisplay})";
        $descripcion = "user_id:{$user->id}|rol:{$roleDisplay}";

        $superAdmins = User::whereHas('role', fn($q) => $q->where('slug', 'super_admin'))->get();

        foreach ($superAdmins as $admin) {
            Notificacion::create([
                'user_id' => $admin->id,
                'tipo'    => 'solicitud_registro',
                'titulo'  => 'Nueva solicitud de cuenta',
                'mensaje' => "{$request->name} solicitó registrarse como {$roleDisplay}.",
                'icono'   => '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>',
                'color'   => 'text-amber-500',
                'url'     => route('admin.solicitudes'),
                'leido'   => false,
            ]);

            Tarea::create([
                'user_id'          => $admin->id,
                'titulo'           => $titulo,
                'descripcion'      => $descripcion,
                'prioridad'        => 'alta',
                'categoria'        => 'Solicitud',
                'completada'       => false,
                'orden'            => 0,
            ]);
        }

        return redirect()->route('login')
            ->with('status', 'Tu solicitud de registro ha sido enviada. Espera a que un administrador la apruebe.');
    }
}
