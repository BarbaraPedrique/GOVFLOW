<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('editar_perfil', ['user' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apodo' => ['nullable', 'string', 'max:255'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->name = $data['nombre'];
        $user->apodo = $data['apodo'] ?? null;
        $user->fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
        $user->descripcion = $data['descripcion'] ?? null;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('avatars', 'public');
            $user->foto = $path;
        }

        $user->save();

        return redirect('/inicio')->with('status', 'Perfil actualizado correctamente.');
    }
}
