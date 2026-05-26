<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FlujoTrabajoController;

Route::get('/', function () {
    return view('login');
});

Route::get('/', function () {
    return view('index');
});

Route::get('/', function () {
    return view('create');
});

Route::get('/registro', function () {
    return view('register');
});

Route::get('/inicio', function () {
    return view('inicio');
});

// Registro
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// Dashboard principal
Route::get('/inicio', function () {
    return view('dashboard');
})->name('inicio');

// Flujos de trabajo (CRUD completo)
Route::resource('flujos', FlujoTrabajoController::class);

