<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'role'                  => 'required|in:editor,administrador,consultor',
            'password'              => 'required|min:8|confirmed',
        ], [
            'name.required'       => 'El nombre es obligatorio.',
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.unique'        => 'Este correo ya está registrado.',
            'role.required'       => 'Selecciona un rol.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'  => 'Las contraseñas no coinciden.',
        ]);

        $slugMap = [
            'editor' => 'gerente',
            'administrador' => 'administrador',
            'consultor' => 'empleado',
        ];

        $role = Role::where('slug', $slugMap[$request->role])->firstOrFail();

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role_id'  => $role->id,
            'status'   => User::STATUS_PENDIENTE,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/inicio');
    }
}
