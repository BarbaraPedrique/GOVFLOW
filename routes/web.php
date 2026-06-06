<?php

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisenadorController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\FlujoTrabajoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\LogAuditoriaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolicitudClienteController;
use App\Http\Controllers\TareaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register'])->name('register.store');

Route::middleware('auth')->group(function () {
    Route::get('/inicio', [DashboardController::class, 'index'])->name('inicio');

    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil');
    Route::get('/editar_perfil', [ProfileController::class, 'edit'])->name('perfil.edit');
    Route::post('/perfil/actualizar', [ProfileController::class, 'update'])->name('perfil.update');

    Route::get('/flujos', [FlujoTrabajoController::class, 'showTimeline'])->name('flujos');
    Route::resource('flujos-trabajo', FlujoTrabajoController::class);

    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria');

    Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
    Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
    Route::put('/tareas/{tarea}', [TareaController::class, 'update'])->name('tareas.update');
    Route::delete('/tareas/{tarea}', [TareaController::class, 'destroy'])->name('tareas.destroy');
    Route::post('/tareas/reordenar', [TareaController::class, 'reordenar'])->name('tareas.reordenar');

    Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
    Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
    Route::put('/horarios/{horario}', [HorarioController::class, 'update'])->name('horarios.update');
    Route::delete('/horarios/{horario}', [HorarioController::class, 'destroy'])->name('horarios.destroy');

    Route::post('/solicitar', [SolicitudClienteController::class, 'store'])->name('solicitar.store');
    Route::get('/solicitudes', [SolicitudClienteController::class, 'misSolicitudes'])->name('solicitudes.mis');
    Route::post('/solicitudes/{tarea}/aprobar', [SolicitudClienteController::class, 'aprobarSolicitud'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{tarea}/rechazar', [SolicitudClienteController::class, 'rechazarSolicitud'])->name('solicitudes.rechazar');

    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/{notificacion}/leido', [NotificacionController::class, 'marcarLeido'])->name('notificaciones.leido');
    Route::post('/notificaciones/marcar-todas', [NotificacionController::class, 'marcarTodasLeido'])->name('notificaciones.marcar-todas');

    Route::get('/logs-auditoria', [LogAuditoriaController::class, 'index'])->name('logs.auditoria');

    Route::get('/disenador', [DisenadorController::class, 'index'])->name('disenador');
    Route::put('/disenador/{flujo}/pasos', [DisenadorController::class, 'guardarPasos'])->name('disenador.guardar');
    Route::get('/disenador/{flujo}/pasos', [DisenadorController::class, 'obtenerPasos'])->name('disenador.pasos');

    Route::resource('equipos', EquipoController::class)->except(['show']);

    Route::prefix('admin')->middleware('auth')->group(function () {
        Route::get('/solicitudes', [\App\Http\Controllers\AdminSolicitudController::class, 'index'])->name('admin.solicitudes');
        Route::post('/solicitudes/{user}/aprobar', [\App\Http\Controllers\AdminSolicitudController::class, 'aprobar'])->name('admin.solicitudes.aprobar');
        Route::post('/solicitudes/{user}/rechazar', [\App\Http\Controllers\AdminSolicitudController::class, 'rechazar'])->name('admin.solicitudes.rechazar');
        Route::post('/solicitudes/tarea/{tarea}/aprobar', [\App\Http\Controllers\AdminSolicitudController::class, 'aprobarTarea'])->name('admin.solicitudes.aprobar-tarea');
        Route::post('/solicitudes/tarea/{tarea}/rechazar', [\App\Http\Controllers\AdminSolicitudController::class, 'rechazarTarea'])->name('admin.solicitudes.rechazar-tarea');
    });
});

Route::fallback(function () { return redirect('/'); });
