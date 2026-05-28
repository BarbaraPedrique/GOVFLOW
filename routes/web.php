<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FlujoTrabajoController;

Route::get('/', function () {
    return view('login');
});

Route::get('/index', function () {
    return view('index');
});


Route::get('/registro', function () {
    return view('register');
});

Route::get('/inicio', function () {
    return view('inicio');
});

//EDITAR PERFIL

// Rutas para editar perfil
Route::get('/editar_perfil', function () {
    return view('editar_perfil'); // Esto carga tu archivo editar_perfil.blade.php
})->name('perfil.edit');

Route::put('/perfil/actualizar', function (\Illuminate\Http\Request $request) {
    // Aquí irá la lógica para guardar en la base de datos más adelante.
    // Por ahora, solo redireccionamos de vuelta a inicio.
    return redirect('/inicio')->with('status', 'Perfil actualizado (simulado)');
})->name('perfil.update');

//Flujos de Trabajo

Route::get('/flujos', function () {
    return view('flujos');
});

