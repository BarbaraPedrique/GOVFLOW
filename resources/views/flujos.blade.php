<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flujos de Trabajo - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
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
            <a href="{{ route('flujos') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#007BFF] bg-blue-50 rounded-xl transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg> Flujos de Trabajo</a>
            <a href="{{ route('equipos.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg> Equipos</a>
        <a href="{{ route('auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg> Auditoría</a>
            @if(in_array(Auth::user()->role?->slug, ['super_admin', 'administrador']))
            <a href="{{ route('logs.auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> Logs Auditoría</a>
            @endif
            @if(in_array(Auth::user()->role?->slug, ['super_admin', 'administrador']))
        <a href="{{ route('disenador') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" /></svg> Diseñador</a>
        @endif
        </nav>
    </aside>

    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
            <div>
                <h2 class="text-slate-800 font-semibold text-lg">Flujos de Trabajo</h2>
            </div>

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

        <main class="flex-1 p-10 mt-16 max-w-[1600px] w-full mx-auto space-y-8" x-data="{ selectedFlujo: {{ $flujos->first()?->id ?? 'null' }}, accordion: {} }">

            @if($flujos->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
                    <svg class="h-16 w-16 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    <h3 class="text-lg font-semibold text-slate-600 mb-1">No hay flujos de trabajo</h3>
                    <p class="text-slate-400 text-sm">Crea un flujo desde el panel de administración para visualizarlo aquí.</p>
                </div>
            @else
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Línea de Tiempo del Flujo</h1>
                        <p class="text-slate-500 text-sm">Monitoreo de estados, actores, actividades, reglas y rutas de transición.</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-semibold border border-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Estado: En Ejecución
                        </span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Seleccionar Flujo</label>
                    <select x-model="selectedFlujo" class="w-full sm:w-96 rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        @foreach($flujos as $flujo)
                            <option value="{{ $flujo->id }}">{{ $flujo->codigo }} — {{ $flujo->nombre }} ({{ $flujo->departamento }})</option>
                        @endforeach
                    </select>
                </div>

                @foreach($flujos as $flujo)
                    <div x-show="selectedFlujo == {{ $flujo->id }}" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                                <div class="space-y-1">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total de Estados</span>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ $flujo->estados->count() }} Estados</h3>
                                </div>
                                <div class="p-2.5 bg-blue-50 text-[#007BFF] rounded-xl">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V16zM14 14a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                                <div class="space-y-1">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Actores Involucrados</span>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ $flujo->estados->flatMap->actores->count() }} Actores</h3>
                                </div>
                                <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                                <div class="space-y-1">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Actividades Totales</span>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ $flujo->estados->flatMap->actividades->count() }} Actividades</h3>
                                </div>
                                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                </div>
                            </div>
                        </div>

                        <div class="relative" x-data="{ expanded: {} }">
                            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-slate-200 z-0"></div>

                            @foreach($flujo->estados as $estado)
                                <div class="relative z-10 pb-10 last:pb-0">
                                    <div class="flex items-start gap-8">
                                        <div class="flex flex-col items-center shrink-0 pt-1">
                                            <div class="h-8 w-8 rounded-full bg-blue-500 border-4 border-white shadow flex items-center justify-center text-white shrink-0">
                                                <span class="text-xs font-bold">{{ $estado->orden }}</span>
                                            </div>
                                            @if(!$loop->last)
                                                <div class="w-0.5 h-full min-h-[24px] bg-blue-200 mt-1"></div>
                                            @endif
                                        </div>

                                        <div class="flex-1 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                                            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-semibold text-slate-800">{{ $estado->nombre }}</h3>
                                                    <span class="text-xs text-slate-400 font-medium">{{ count($estado->rutas ?? []) }} ruta(s)</span>
                                                </div>
                                            </div>

                                            <div class="p-5">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div x-data="{ open: true }" class="border border-slate-100 rounded-xl overflow-hidden">
                                                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider hover:bg-slate-50 transition-colors">
                                                            <span>Actores ({{ count($estado->actores ?? []) }})</span>
                                                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                                                        </button>
                                                        <div x-show="open" x-collapse class="px-4 pb-3 space-y-2">
                                                            @forelse($estado->actores ?? [] as $actor)
                                                                <div class="flex items-center gap-3">
                                                                    @if(($actor['tipo'] ?? 'usuario') === 'sistema')
                                                                        <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                                                                        </div>
                                                                    @else
                                                                        <img src="{{ $actor['foto'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=60&q=80' }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover shrink-0">
                                                                    @endif
                                                                    <div class="min-w-0">
                                                                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $actor['nombre'] ?? 'Sin nombre' }}</p>
                                                                        <span class="text-xs text-slate-400">{{ $actor['rol'] ?? $actor['tipo'] ?? 'Usuario' }}</span>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <p class="text-xs text-slate-400 italic">Sin actores asignados</p>
                                                            @endforelse
                                                        </div>
                                                    </div>

                                                    <div x-data="{ open: false }" class="border border-slate-100 rounded-xl overflow-hidden">
                                                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider hover:bg-slate-50 transition-colors">
                                                            <span>Actividades ({{ count($estado->actividades ?? []) }})</span>
                                                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                                                        </button>
                                                        <div x-show="open" x-collapse class="px-4 pb-3 space-y-2">
                                                            @forelse($estado->actividades ?? [] as $actividad)
                                                                <div class="flex items-start gap-3">
                                                                    <div class="h-7 w-7 rounded-lg shrink-0 flex items-center justify-center
                                                                        @switch($actividad['tipo'] ?? '')
                                                                            @case('carga_datos') bg-amber-50 text-amber-600 @break
                                                                            @case('revision') bg-blue-50 text-blue-600 @break
                                                                            @case('checklist') bg-purple-50 text-purple-600 @break
                                                                            @case('subir_documento') bg-emerald-50 text-emerald-600 @break
                                                                            @default bg-slate-50 text-slate-500
                                                                        @endswitch
                                                                    ">
                                                                        @switch($actividad['tipo'] ?? '')
                                                                            @case('carga_datos')
                                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                                                                @break
                                                                            @case('revision')
                                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                                                @break
                                                                            @case('checklist')
                                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                                                                @break
                                                                            @case('subir_documento')
                                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                                                @break
                                                                            @default
                                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                                                        @endswitch
                                                                    </div>
                                                                    <div class="min-w-0">
                                                                        <p class="text-sm font-semibold text-slate-700">{{ $actividad['nombre'] ?? 'Actividad' }}</p>
                                                                        @if(!empty($actividad['descripcion']))
                                                                            <p class="text-xs text-slate-400">{{ $actividad['descripcion'] }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <p class="text-xs text-slate-400 italic">Sin actividades definidas</p>
                                                            @endforelse
                                                        </div>
                                                    </div>

                                                    <div x-data="{ open: false }" class="border border-slate-100 rounded-xl overflow-hidden">
                                                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider hover:bg-slate-50 transition-colors">
                                                            <span>Reglas ({{ count($estado->reglas ?? []) }})</span>
                                                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                                                        </button>
                                                        <div x-show="open" x-collapse class="px-4 pb-3 space-y-2">
                                                            @forelse($estado->reglas ?? [] as $regla)
                                                                <div class="flex items-start gap-3">
                                                                    <div class="h-7 w-7 rounded-lg shrink-0 flex items-center justify-center
                                                                        @switch($regla['tipo'] ?? '')
                                                                            @case('gobernanza') bg-rose-50 text-rose-600 @break
                                                                            @case('negocio') bg-amber-50 text-amber-600 @break
                                                                            @default bg-slate-50 text-slate-500
                                                                        @endswitch
                                                                    ">
                                                                        @switch($regla['tipo'] ?? '')
                                                                            @case('gobernanza')
                                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                                                                                @break
                                                                            @case('negocio')
                                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                                                                @break
                                                                            @default
                                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                                        @endswitch
                                                                    </div>
                                                                    <div class="min-w-0">
                                                                        <p class="text-sm font-semibold text-slate-700">{{ $regla['nombre'] ?? 'Regla' }}</p>
                                                                        @if(!empty($regla['descripcion']))
                                                                            <p class="text-xs text-slate-400">{{ $regla['descripcion'] }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <p class="text-xs text-slate-400 italic">Sin reglas definidas</p>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>

                                                @if(count($estado->rutas ?? []) > 0)
                                                    <div class="mt-4 pt-4 border-t border-slate-100">
                                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3">Rutas de Transición</span>
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach($estado->rutas as $ruta)
                                                                <div class="flex items-center gap-2 text-sm bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                                                                    <svg class="h-3.5 w-3.5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                                                    <span class="font-medium text-slate-700">{{ $ruta['destino'] ?? 'Siguiente' }}</span>
                                                                    @if(!empty($ruta['condicion']))
                                                                        <span class="text-xs text-slate-400">({{ $ruta['accion'] ?? '' }} {{ $ruta['condicion'] }})</span>
                                                                    @elseif(!empty($ruta['accion']))
                                                                        <span class="text-xs text-slate-400">({{ $ruta['accion'] }})</span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </main>
    </div>

</body>
</html>