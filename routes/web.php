<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FlujoTrabajoController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register'])->name('register.store');

Route::middleware('auth')->group(function () {
    Route::get('/inicio', [DashboardController::class, 'index'])->name('inicio');

    Route::get('/editar_perfil', [ProfileController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil/actualizar', [ProfileController::class, 'update'])->name('perfil.update');

    Route::get('/flujos', function () {
        return view('flujos');
    })->name('flujos');

    Route::resource('flujos-trabajo', FlujoTrabajoController::class);
});

// Fallback — redirigir a login si no hay ruta
Route::fallback(function () {
    return redirect('/');
});
