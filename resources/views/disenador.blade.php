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

@include('partials.sidebar')

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Diseñador de Flujos</h2></div>
        <div class="flex items-center gap-6">
            @include('partials.break-buttons')
            @include('partials.notification-bell')
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->apodo ?? Auth::user()->name }}</p><p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p></div>
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
                <div class="min-w-[220px] flex-1">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Flujo existente</label>
                    <select x-model="flujoId" @change="cargarDiseno" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Crear nuevo...</option>
                        @foreach ($flujos as $flujo)
                            <option value="{{ $flujo->id }}">{{ $flujo->codigo }} — {{ $flujo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[220px] flex-1">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">O nombre del nuevo flujo</label>
                    <input type="text" x-model="nuevoFlujoNombre" @focus="flujoId=''" placeholder="Ej: Solicitud de permisos" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div class="min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Equipo asignado</label>
                    <select x-model="equipoId" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Sin equipo</option>
                        @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}">{{ $equipo->nombre }} ({{ $equipo->miembros->count() }} miembros)</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Versión</label>
                    <input type="text" x-model="diseno.version" placeholder="v1.0" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div class="flex gap-2">
                    <button @click="guardarDiseno" class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        Guardar Diseño
                    </button>
                    <button @click="guardarConfiguracion" x-show="flujoId" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Guardar Configuración
                    </button>
                </div>
            </div>
        </div>

        <template x-if="nuevoFlujoNombre">
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
                <div class="xl:col-span-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 h-fit relative z-10 overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Pasos</h3>
                        <span class="text-xs text-slate-400" x-text="pasos.length + ' paso(s)'"></span>
                    </div>

                    <div class="flex gap-2 mb-4">
                        <input type="text" x-model="nuevoPasoNombre" @keydown.enter="agregarPaso" placeholder="Nuevo paso..." class="flex-1 min-w-0 px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-blue-500">
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
                                    <input type="text" x-model="nuevoFlujoNombre" placeholder="Nombre del flujo de trabajo..." class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Equipo</label>
                                    <select x-model="equipoId" @change="onEquipoChange" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                        <option value="">Seleccionar equipo...</option>
                                        @foreach($equipos as $equipo)
                                            <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Departamento</label>
                                    <input type="text" x-model="nuevoFlujoDepartamento" placeholder="Departamento..." class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
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
                                            <input type="text" x-model="pasos[selectedPaso].nombre" placeholder="Ej: Validación OCR" class="w-full min-w-0 rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Descripción / Instrucciones</label>
                                            <textarea x-model="pasos[selectedPaso].descripcion" rows="2" placeholder="Guía paso a paso para el empleado..." class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"></textarea>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                                Revisor (aprueba/rechaza el paso)
                                                <span class="text-[10px] text-rose-500 font-medium ml-1">* Requerido</span>
                                                <span x-show="pasos.length > 0 && selectedPaso === pasos.length - 1" class="text-[10px] text-amber-600 font-medium ml-1">(último paso: solo administrador del equipo)</span>
                                            </label>
                                            <select x-model="pasos[selectedPaso].revisor_id" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                                <option value="">Seleccionar revisor...</option>
                                                <template x-for="u in equipoMiembros" :key="u.id">
                                                    <option :value="u.id"
                                                        x-show="esRevisorValido(u)"
                                                        x-text="u.name + ' (' + u.role_display + ')'"></option>
                                                </template>
                                            </select>
                                            <p x-show="!pasos[selectedPaso].revisor_id" class="text-xs text-rose-400 mt-1">Debes seleccionar un revisor para este paso.</p>
                                        </div>
                                    </div>
                                    <div class="pb-2 border-b border-slate-100">
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Personas asignadas a este paso</label>
                                        <div x-show="!equipoId" class="text-xs text-amber-600 mb-2">Selecciona un equipo en la configuración del flujo para ver sus miembros.</div>
                                        <div class="space-y-1.5 max-h-40 overflow-y-auto">
                                            <template x-for="u in equipoMiembros" :key="u.id">
                                                <label class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-slate-50 cursor-pointer">
                                                    <input type="checkbox" :value="u.id" :checked="(pasos[selectedPaso].asignados_ids || []).includes(u.id)" @change="toggleAsignado(u.id)" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 shrink-0">
                                                    <span class="text-sm text-slate-700" x-text="u.name"></span>
                                                    <span class="text-[10px] text-slate-400 ml-auto" x-text="u.role_display"></span>
                                                </label>
                                            </template>
                                            <div x-show="equipoId && equipoMiembros.length === 0" class="text-xs text-slate-400 italic px-3 py-2">El equipo no tiene miembros</div>
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
    @include('partials.solicitar-modal')
    </main>

    <script>
    function disenadorApp() {
        return {
            flujoId: '',
            nuevoFlujoNombre: '',
            nuevoFlujoDepartamento: '',
            equipoId: '',
            pasos: [],
            diseno: { version: 'v1.0', trigger_evento: '', trigger_descripcion: '' },
            nuevoPasoNombre: '',
            selectedPaso: null,
            dragIndex: null,
            csrf: '{{ csrf_token() }}',
            flujoNombres: { @foreach($flujos as $f) '{{ $f->id }}': '{{ $f->nombre }}', @endforeach },
            flujoDepartamentos: { @foreach($flujos as $f) '{{ $f->id }}': '{{ addslashes($f->departamento ?? 'General') }}', @endforeach },
            flujoEquipos: { @foreach($flujos as $f) '{{ $f->id }}': '{{ $f->equipo_id ?? '' }}', @endforeach },
            equiposData: @json($equiposData),

            cargarDiseno() {
                if (!this.flujoId) { this.pasos = []; this.diseno = { version: 'v1.0', trigger_evento: '', trigger_descripcion: '' }; this.nuevoFlujoNombre = ''; this.nuevoFlujoDepartamento = ''; this.selectedPaso = null; return; }
                this.nuevoFlujoNombre = this.flujoNombres[this.flujoId] || '';
                this.nuevoFlujoDepartamento = this.flujoDepartamentos[this.flujoId] || '';
                fetch(`{{ url('/disenador') }}/${this.flujoId}/pasos`)
                    .then(r => r.json()).then(d => {
                        this.pasos = (d.pasos || []).map(p => ({ asignados_ids: [], revisor_id: '', ...p }));
                        this.diseno = d.diseno || { version: 'v1.0', trigger_evento: '', trigger_descripcion: '' };
                        if (!this.diseno.version) this.diseno.version = 'v1.0';
                        this.equipoId = this.flujoEquipos[this.flujoId] || '';
                        this.selectedPaso = this.pasos.length > 0 ? 0 : null;
                    });
            },

            onEquipoChange() {
                // Resetear asignaciones de todos los pasos al cambiar equipo
                this.pasos.forEach(p => { p.asignados_ids = []; p.revisor_id = ''; });
            },

            guardarConfiguracion() {
                if (!this.flujoId) { alert('Primero guarda el diseño para crear el flujo.'); return; }
                fetch(`{{ url('/flujos-trabajo') }}/${this.flujoId}`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        nombre: this.nuevoFlujoNombre,
                        departamento: this.nuevoFlujoDepartamento || 'General',
                        equipo_id: this.equipoId,
                        estado: 'Activo'
                    })
                }).then(r => r.json()).then(d => {
                    if (d.id || d.success) {
                        this.flujoNombres[this.flujoId] = this.nuevoFlujoNombre;
                        this.flujoDepartamentos[this.flujoId] = this.nuevoFlujoDepartamento;
                        this.flujoEquipos[this.flujoId] = this.equipoId;
                        alert('Configuración guardada correctamente.');
                    } else {
                        alert(d.message || 'Error al guardar la configuración.');
                    }
                }).catch(e => alert('Error de red: ' + e.message));
            },

            asegurarFlujo() {
                if (this.flujoId) return Promise.resolve(this.flujoId);
                if (!this.nuevoFlujoNombre.trim()) { alert('Escribe el nombre del nuevo flujo primero.'); return Promise.reject(); }
                return fetch('{{ url('/flujos-trabajo') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                    body: (() => { const f = new FormData(); f.append('nombre', this.nuevoFlujoNombre); f.append('departamento', this.nuevoFlujoDepartamento || 'General'); f.append('estado', 'Activo'); f.append('equipo_id', this.equipoId); return f; })()
                }).then(r => r.json()).then(d => {
                    if (!d.id) throw new Error(d.message || 'Error al crear el flujo.');
                    this.flujoId = d.id;
                    this.flujoNombres[this.flujoId] = this.nuevoFlujoNombre;
                    this.flujoDepartamentos[this.flujoId] = this.nuevoFlujoDepartamento;
                    this.flujoEquipos[this.flujoId] = this.equipoId;
                    return this.flujoId;
                });
            },

            get equipoMiembros() {
                return this.equiposData[this.equipoId] || [];
            },

            esRevisorValido(u) {
                const esUltimo = this.pasos.length > 0 && this.selectedPaso === this.pasos.length - 1;
                const esAdminGlobal = u.role_slug === 'super_admin' || u.role_slug === 'administrador';
                const esAdminEquipo = u.pivot_rol === 'administrador' || u.pivot_rol === 'gerente';
                const esLiderEquipo = u.role_slug === 'lider_equipo' || u.role_slug === 'gerente' || u.pivot_rol === 'lider_equipo';
                if (esUltimo) {
                    // Último paso: solo administradores/gerentes del equipo o globales
                    return esAdminGlobal || esAdminEquipo;
                }
                // Pasos normales: lider_equipo+ (global o del equipo)
                return esAdminGlobal || esAdminEquipo || esLiderEquipo;
            },

            toggleAsignado(userId) {
                if (!this.pasos[this.selectedPaso].asignados_ids) this.pasos[this.selectedPaso].asignados_ids = [];
                const idx = this.pasos[this.selectedPaso].asignados_ids.indexOf(userId);
                if (idx > -1) this.pasos[this.selectedPaso].asignados_ids.splice(idx, 1);
                else this.pasos[this.selectedPaso].asignados_ids.push(userId);
            },

            agregarPaso() {
                if (!this.nuevoPasoNombre.trim()) return;
                this.pasos.push({
                    nombre: this.nuevoPasoNombre,
                    descripcion: '',
                    asignacion_usuario_id: '',
                    asignados_ids: [],
                    revisor_id: '',
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

            pasosValidos() {
                if (this.pasos.length === 0) { alert('Agrega al menos un paso antes de guardar.'); return false; }
                for (let i = 0; i < this.pasos.length; i++) {
                    if (!this.pasos[i].revisor_id) {
                        alert('El paso ' + (i+1) + ' ("' + (this.pasos[i].nombre || 'Sin nombre') + '") no tiene revisor. Todos los pasos requieren un revisor.');
                        this.selectedPaso = i;
                        return false;
                    }
                    if (!this.pasos[i].asignados_ids || this.pasos[i].asignados_ids.length === 0) {
                        alert('El paso ' + (i+1) + ' ("' + (this.pasos[i].nombre || 'Sin nombre') + '") no tiene personas asignadas.');
                        this.selectedPaso = i;
                        return false;
                    }
                }
                return true;
            },

            guardarDiseno() {
                if (!this.pasosValidos()) return;
                this.asegurarFlujo().then(id => {
                    fetch(`{{ url('/disenador') }}/${id}/pasos`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': this.csrf,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ pasos: this.pasos, diseno: this.diseno })
                    }).then(r => r.json()).then(d => {
                        if (d.success) {
                            // If published, auto-start the flow
                            if (this.diseno.publicado) {
                                return fetch(`{{ url('/flujos') }}/${id}/iniciar`, {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' }
                                }).then(r => r.json()).then(res => {
                                    if (res.success) {
                                        alert('Flujo publicado e iniciado correctamente. Los usuarios asignados ya pueden ver sus pasos.');
                                    } else {
                                        alert(res.message || 'Error al iniciar el flujo.');
                                    }
                                });
                            }
                            alert('Diseño guardado correctamente.');
                        } else {
                            alert(d.message || 'Error al guardar el diseño.');
                        }
                    }).catch(e => alert('Error de red: ' + e.message));
                }).catch(e => { if (e) alert(e); });
            },

            publicarFlujo() {
                if (!this.flujoId && !this.nuevoFlujoNombre.trim()) { alert('Escribe el nombre del nuevo flujo primero.'); return; }
                if (!this.pasosValidos()) return;
                if (!confirm('¿Estás seguro de publicar este flujo en producción? Una vez publicado, estará disponible para todos los usuarios.')) return;
                this.diseno.publicado = true;
                this.guardarDiseno();
            }
        };
    }
</script>
</body>
</html>
