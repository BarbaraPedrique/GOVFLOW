<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diseñador de Flujos - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-20">
    <div class="h-16 border-b border-slate-100 flex items-center px-6">
        <img src="{{ asset('imagenes/logo2.png') }}" alt="Logo GOVFLOW" class="h-8 w-auto object-contain">
        <span class="ml-3 text-lg font-bold text-slate-800">GOVFLOW</span>
    </div>
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
        <a href="{{ route('inicio') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V16zM14 14a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg> Inicio</a>
        <a href="{{ route('tareas.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg> Tareas</a>
        <a href="{{ route('horarios.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> Horarios</a>
        <a href="{{ route('flujos') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg> Flujos de Trabajo</a>
        <a href="{{ route('equipos.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg> Equipos</a>
        <a href="{{ route('auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg> Auditoría</a>
        @if(in_array(Auth::user()->role?->slug, ['super_admin', 'administrador']))
            <a href="{{ route('logs.auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> Logs Auditoría</a>
        @endif
        <a href="{{ route('disenador') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#007BFF] bg-blue-50 rounded-xl transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" /></svg> Diseñador</a>
    </nav>
</aside>

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Diseñador de Flujos</h2></div>
        <div class="flex items-center gap-6">
            @include('partials.notification-bell')
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->name }}</p><p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p></div>
                    <img src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                    <svg class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                </div>
                @include('partials.user-dropdown')
            </div>
        </div>
    </header>

    <main class="flex-1 p-10 mt-16 max-w-[1600px] w-full mx-auto space-y-6" x-data="disenadorApp()">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="min-w-[260px] flex-1">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Flujo de Trabajo</label>
                    <select x-model="flujoId" @change="cargarDiseno" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Seleccionar flujo...</option>
                        @foreach ($flujos as $flujo)
                            <option value="{{ $flujo->id }}">{{ $flujo->codigo }} — {{ $flujo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Versión</label>
                    <input type="text" x-model="diseno.version" placeholder="v1.0" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <button @click="guardarDiseno" class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        Guardar Diseño
                    </button>
                </div>
            </div>
        </div>

        <template x-if="flujoId">
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
                <div class="xl:col-span-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 h-fit relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Pasos</h3>
                        <span class="text-xs text-slate-400" x-text="pasos.length + ' paso(s)'"></span>
                    </div>

                    <div class="flex gap-2 mb-4">
                        <input type="text" x-model="nuevoPasoNombre" @keydown.enter="agregarPaso" placeholder="Nuevo paso..." class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-blue-500">
                        <button @click="agregarPaso" class="px-3 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors shrink-0">+</button>
                    </div>

                    <div class="space-y-1 max-h-[500px] overflow-y-auto">
                        <template x-for="(paso, i) in pasos" :key="i">
                            <div>
                                <div @click="selectedPaso = i"
                                     :class="selectedPaso === i ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300'"
                                     class="flex items-center gap-3 px-3 py-2.5 rounded-xl border cursor-pointer transition-all draggable"
                                     draggable="true"
                                     @dragstart="dragIndex = i"
                                     @dragover.prevent
                                     @drop="moverPaso(i)">
                                    <span class="text-xs font-bold text-slate-400 w-5 shrink-0" x-text="i + 1"></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate" x-text="paso.nombre || 'Sin nombre'"></p>
                                        <p class="text-[10px] text-slate-400" x-show="paso.asignacion_rol" x-text="'Rol: ' + paso.asignacion_rol"></p>
                                    </div>
                                    <button @click.stop="eliminarPaso(i)" class="p-1 text-slate-300 hover:text-rose-500 transition-colors shrink-0">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                                <div x-show="i < pasos.length - 1" class="flex justify-center py-0.5">
                                    <svg class="h-3 w-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                </div>
                            </div>
                        </template>

                        <div x-show="pasos.length === 0" class="text-xs text-slate-400 text-center py-6">
                            Agrega pasos para comenzar
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-3 space-y-6">
                    <template x-if="selectedPaso === null">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                            <h3 class="text-base font-bold text-slate-700 mb-4">Configuración del Flujo</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Nombre del Flujo</label>
                                    <input type="text" x-model="flujoNombre" @change="actualizarNombreFlujo" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Disparador / Gatillo</label>
                                    <select x-model="diseno.trigger_evento" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                        <option value="">Sin disparador automático</option>
                                        <option value="usuario_registrado">Usuario nuevo registrado</option>
                                        <option value="documento_subido">Documento PDF cargado</option>
                                        <option value="tarea_completada">Tarea anterior completada</option>
                                        <option value="fecha_programada">Fecha programada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4" x-show="diseno.trigger_evento">
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Descripción del disparador</label>
                                <textarea x-model="diseno.trigger_descripcion" rows="2" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Ej: Se dispara cuando un usuario nuevo completa su registro..."></textarea>
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedPaso !== null">
                        <div>
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                    <h3 class="text-base font-bold text-slate-700">
                                        <span class="text-blue-600" x-text="'PASO ' + (selectedPaso + 1) + ':'"></span>
                                        <span x-text="' ' + (pasos[selectedPaso]?.nombre || 'Sin nombre')"></span>
                                    </h3>
                                    <span class="text-xs text-slate-400">Bloques 2–5</span>
                                </div>

                                <div class="p-6 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre de la Tarea</label>
                                            <input type="text" x-model="pasos[selectedPaso].nombre" placeholder="Ej: Validación OCR" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Descripción / Instrucciones</label>
                                            <textarea x-model="pasos[selectedPaso].descripcion" rows="2" placeholder="Guía paso a paso para el empleado..." class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"></textarea>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Asignación por Equipo / Rol</label>
                                            <select x-model="pasos[selectedPaso].asignacion_rol" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                                <option value="">Seleccionar rol...</option>
                                                <option value="super_admin">Super Admin</option>
                                                <option value="administrador">Administrador</option>
                                                <option value="gerente">Gerente</option>
                                                <option value="lider_equipo">Líder de Equipo</option>
                                                <option value="empleado">Empleado</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Personal Específico (opcional)</label>
                                            <select x-model="pasos[selectedPaso].asignacion_usuario_id" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                                <option value="">Cualquier usuario del rol</option>
                                                @foreach(\App\Models\User::all() as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Fecha Límite Dinámica</label>
                                            <div class="flex items-center gap-2">
                                                <input type="number" x-model="pasos[selectedPaso].fecha_limite_horas" placeholder="48" class="w-24 rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                                <span class="text-sm text-slate-500">horas después de activación</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Prioridad de la Tarea</label>
                                            <select x-model="pasos[selectedPaso].prioridad" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                                <option value="baja">Baja</option>
                                                <option value="media">Media</option>
                                                <option value="alta">Alta</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="pb-2">
                                        <div class="flex items-center justify-between mb-3">
                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Checklist de Pasos Internos</label>
                                            <button @click="agregarChecklistItem" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4" /></svg>
                                                Agregar item
                                            </button>
                                        </div>
                                        <div class="space-y-2 mb-4">
                                            <template x-for="(item, ci) in (pasos[selectedPaso].checklist || [])" :key="ci">
                                                <div class="flex items-center gap-3 bg-slate-50 rounded-lg px-4 py-2 border border-slate-100">
                                                    <input type="checkbox" disabled class="rounded border-slate-300 text-blue-600 shrink-0">
                                                    <input type="text" x-model="pasos[selectedPaso].checklist[ci].item" placeholder="Describa el paso de verificación..." class="flex-1 bg-transparent text-sm text-slate-700 focus:outline-none border-b border-transparent focus:border-blue-500">
                                                    <button @click="eliminarChecklistItem(ci)" class="p-1 text-slate-300 hover:text-rose-500 transition-colors shrink-0">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <div x-show="!pasos[selectedPaso].checklist || pasos[selectedPaso].checklist.length === 0" class="text-xs text-slate-400 italic">Sin items de checklist</div>
                                        </div>

                                        <div class="pt-4 border-t border-slate-100">
                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-3">Flujo de Estado del Borrador (Transiciones)</label>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="flex items-center gap-3 bg-emerald-50 rounded-lg px-4 py-3 border border-emerald-200">
                                                    <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7" /></svg>
                                                    <span class="text-xs font-semibold text-slate-600 w-20">Al aprobar →</span>
                                                    <select x-model="pasos[selectedPaso].transicion_aprobado" class="flex-1 rounded-lg border-emerald-200 bg-white text-sm text-slate-700 px-3 py-1.5 border focus:ring-2 focus:ring-emerald-500 outline-none">
                                                        <option value="">Sin transición</option>
                                                        <template x-for="(p, pi) in pasos" :key="pi">
                                                            <option :value="pi" x-show="pi !== selectedPaso" x-text="'Paso ' + (pi+1) + ': ' + (p.nombre || 'Sin nombre')"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                                <div class="flex items-center gap-3 bg-rose-50 rounded-lg px-4 py-3 border border-rose-200">
                                                    <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                                    <span class="text-xs font-semibold text-slate-600 w-20">Al rechazar →</span>
                                                    <select x-model="pasos[selectedPaso].transicion_rechazado" class="flex-1 rounded-lg border-rose-200 bg-white text-sm text-slate-700 px-3 py-1.5 border focus:ring-2 focus:ring-rose-500 outline-none">
                                                        <option value="">Sin transición</option>
                                                        <template x-for="(p, pi) in pasos" :key="pi">
                                                            <option :value="pi" x-show="pi !== selectedPaso" x-text="'Paso ' + (pi+1) + ': ' + (p.nombre || 'Sin nombre')"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-end gap-3">
                        <button @click="publicarFlujo" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7" /></svg>
                            Publicar en Producción
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </main>
</div>

<script>
    function disenadorApp() {
        return {
            flujoId: '',
            flujoNombre: '',
            pasos: [],
            diseno: { version: 'v1.0', trigger_evento: '', trigger_descripcion: '' },
            nuevoPasoNombre: '',
            selectedPaso: null,
            dragIndex: null,
            csrf: '{{ csrf_token() }}',
            flujoNombres: { @foreach($flujos as $f) '{{ $f->id }}': '{{ $f->nombre }}', @endforeach },

            cargarDiseno() {
                if (!this.flujoId) { this.pasos = []; this.diseno = { version: 'v1.0', trigger_evento: '', trigger_descripcion: '' }; this.selectedPaso = null; return; }
                this.flujoNombre = this.flujoNombres[this.flujoId] || '';
                fetch(`{{ url('/disenador') }}/${this.flujoId}/pasos`)
                    .then(r => r.json()).then(d => {
                        this.pasos = d.pasos || [];
                        this.diseno = d.diseno || { version: 'v1.0', trigger_evento: '', trigger_descripcion: '' };
                        if (!this.diseno.version) this.diseno.version = 'v1.0';
                        this.selectedPaso = this.pasos.length > 0 ? 0 : null;
                    });
            },

            actualizarNombreFlujo() {
                if (!this.flujoId) return;
                fetch(`{{ url('/flujos-trabajo') }}/${this.flujoId}`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ nombre: this.flujoNombre, departamento: '', estado: 'Activo' })
                }).then(r => r.json()).then(() => {
                    this.flujoNombres[this.flujoId] = this.flujoNombre;
                });
            },

            agregarPaso() {
                if (!this.nuevoPasoNombre.trim()) return;
                this.pasos.push({
                    nombre: this.nuevoPasoNombre,
                    descripcion: '',
                    asignacion_rol: '',
                    asignacion_usuario_id: '',
                    fecha_limite_horas: '',
                    prioridad: 'media',
                    checklist: [],
                    transicion_aprobado: '',
                    transicion_rechazado: ''
                });
                this.nuevoPasoNombre = '';
                this.selectedPaso = this.pasos.length - 1;
            },

            eliminarPaso(i) {
                this.pasos.splice(i, 1);
                if (this.selectedPaso === i) this.selectedPaso = null;
                else if (this.selectedPaso > i) this.selectedPaso--;
            },

            moverPaso(i) {
                if (this.dragIndex === null || this.dragIndex === i) return;
                const arr = this.pasos;
                const [el] = arr.splice(this.dragIndex, 1);
                arr.splice(i, 0, el);
                this.dragIndex = null;
            },

            agregarChecklistItem() {
                if (!this.pasos[this.selectedPaso].checklist) this.pasos[this.selectedPaso].checklist = [];
                this.pasos[this.selectedPaso].checklist.push({ item: '' });
            },

            eliminarChecklistItem(i) {
                this.pasos[this.selectedPaso].checklist.splice(i, 1);
            },

            guardarDiseno() {
                if (!this.flujoId) return;
                fetch(`{{ url('/disenador') }}/${this.flujoId}/pasos`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        pasos: JSON.stringify(this.pasos),
                        diseno: JSON.stringify(this.diseno)
                    })
                }).then(r => r.json()).then(d => {
                    if (d.success) alert('Diseño guardado correctamente.');
                });
            },

            publicarFlujo() {
                if (!this.flujoId) return;
                if (!confirm('¿Estás seguro de publicar este flujo en producción? Una vez publicado, estará disponible para todos los usuarios.')) return;
                this.diseno.publicado = true;
                this.guardarDiseno();
            }
        };
    }
</script>
</body>
</html>
