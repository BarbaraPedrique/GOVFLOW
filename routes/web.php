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
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SolicitudClienteController;
use App\Http\Controllers\TareaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register'])->name('register.store');

Route::middleware(['auth', 'suspended'])->group(function () {
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

    Route::get('/notas', [NoteController::class, 'index'])->name('notas.index');
    Route::post('/notas', [NoteController::class, 'store'])->name('notas.store');
    Route::put('/notas/{note}', [NoteController::class, 'update'])->name('notas.update');
    Route::delete('/notas/{note}', [NoteController::class, 'destroy'])->name('notas.destroy');

    Route::get('/personal', [PersonalController::class, 'index'])->name('personal.index');
    Route::get('/personal/{user}/detalle', [PersonalController::class, 'detalle'])->name('personal.detalle');
    Route::post('/personal/{user}/rol', [PersonalController::class, 'cambiarRol'])->name('personal.rol');
    Route::post('/personal/{user}/equipo', [PersonalController::class, 'cambiarEquipo'])->name('personal.equipo');
    Route::post('/personal/{user}/suspender', [PersonalController::class, 'toggleSuspender'])->name('personal.suspender');
    Route::delete('/personal/{user}', [PersonalController::class, 'eliminar'])->name('personal.eliminar');

    Route::get('/logs-auditoria', [LogAuditoriaController::class, 'index'])->name('logs.auditoria');

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
    Route::get('/reportes/pdf', [ReporteController::class, 'generarPdf'])->name('reportes.pdf');

    Route::get('/disenador', [DisenadorController::class, 'index'])->name('disenador');
    Route::put('/disenador/{flujo}/pasos', [DisenadorController::class, 'guardarPasos'])->name('disenador.guardar');
    Route::get('/disenador/{flujo}/pasos', [DisenadorController::class, 'obtenerPasos'])->name('disenador.pasos');

    Route::post('/flujos/paso/{pasoAsignacion}/completar', [\App\Http\Controllers\FlujoEjecucionController::class, 'completarPaso'])->name('flujos.paso.completar');
    Route::post('/flujos/paso/{pasoAsignacion}/revisar', [\App\Http\Controllers\FlujoEjecucionController::class, 'revisarPaso'])->name('flujos.paso.revisar');
    Route::get('/flujos/mis-pendientes', [\App\Http\Controllers\FlujoEjecucionController::class, 'misPendientes'])->name('flujos.mis-pendientes');
    Route::post('/flujos/{flujo}/iniciar', [\App\Http\Controllers\FlujoEjecucionController::class, 'iniciar'])->name('flujos.iniciar');
    Route::post('/break/start', [\App\Http\Controllers\BreakController::class, 'start'])->name('break.start');
    Route::post('/break/end', [\App\Http\Controllers\BreakController::class, 'end'])->name('break.end');
    Route::get('/break/status', [\App\Http\Controllers\BreakController::class, 'status'])->name('break.status');
    Route::resource('equipos', EquipoController::class)->except(['show']);

    Route::prefix('admin')->middleware('auth')->group(function () {
        Route::get('/solicitudes', [\App\Http\Controllers\SolicitudClienteController::class, 'misSolicitudes'])->name('admin.solicitudes');
        Route::post('/solicitudes/{user}/aprobar', [\App\Http\Controllers\AdminSolicitudController::class, 'aprobar'])->name('admin.solicitudes.aprobar');
        Route::post('/solicitudes/{user}/rechazar', [\App\Http\Controllers\AdminSolicitudController::class, 'rechazar'])->name('admin.solicitudes.rechazar');
        Route::post('/solicitudes/tarea/{tarea}/aprobar', [\App\Http\Controllers\AdminSolicitudController::class, 'aprobarTarea'])->name('admin.solicitudes.aprobar-tarea');
        Route::post('/solicitudes/tarea/{tarea}/rechazar', [\App\Http\Controllers\AdminSolicitudController::class, 'rechazarTarea'])->name('admin.solicitudes.rechazar-tarea');
    });
});

Route::fallback(function () { return redirect('/'); });
