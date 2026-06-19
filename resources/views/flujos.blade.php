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

    @include('partials.sidebar')

    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
            <div>
                <h2 class="text-slate-800 font-semibold text-lg">Flujos de Trabajo</h2>
            </div>

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

        <main class="flex-1 p-10 mt-16 max-w-[1600px] w-full mx-auto space-y-8" x-data="{
    selectedFlujo: {{ $flujos->first()?->id ?? 'null' }},
    accordion: {},
    pasoModal: false,
    pasoActual: null,
    mensajePaso: '',
    revisionModal: false,
    revisionActual: null,
    revisionAccion: '',
    revisionComentario: '',
    csrf: '{{ csrf_token() }}',
    flujoEstados: {{ json_encode($flujos->mapWithKeys(fn($f) => [$f->id => $f->estado])) }},
    flujoNombres: {{ json_encode($flujos->mapWithKeys(fn($f) => [$f->id => $f->codigo . ' — ' . $f->nombre])) }},
    misPasos: {{ json_encode($misPasosPendientes->map(fn($p) => [
        'id' => $p->id,
        'paso_nombre' => $p->paso_nombre,
        'paso_index' => $p->paso_index,
        'estado' => $p->estado,
        'fecha_limite' => $p->fecha_limite?->format('d/m/Y H:i'),
        'flujo_nombre' => $p->ejecucion?->flujoTrabajo?->nombre ?? '—',
        'checklist' => ($p->ejecucion->flujoTrabajo->pasos ?? [])[$p->paso_index]['checklist'] ?? [],
    ])->values()) }},
    misRevisiones: {{ json_encode($pendientesRevision->map(fn($p) => [
        'id' => $p->id,
        'paso_nombre' => $p->paso_nombre,
        'paso_index' => $p->paso_index,
        'flujo_nombre' => $p->ejecucion?->flujoTrabajo?->nombre ?? '—',
        'fecha_limite' => $p->fecha_limite?->format('d/m/Y H:i'),
        'mensaje' => $p->mensaje,
        'archivo_url' => $p->archivo ? asset('storage/' . $p->archivo) : null,
        'archivo_nombre' => $p->archivo ? basename($p->archivo) : null,
    ])->values()) }},
    abrirPaso(id) {
        this.pasoActual = this.misPasos.find(p => p.id === id);
        this.mensajePaso = '';
        this.pasoModal = true;
    },
    completarPaso() {
        if (!this.pasoActual) return;
        const form = new FormData();
        form.append('mensaje', this.mensajePaso);
        const fileInput = document.getElementById('archivo_paso');
        if (fileInput?.files[0]) form.append('archivo', fileInput.files[0]);
        fetch('{{ url('/flujos/paso') }}/' + this.pasoActual.id + '/completar', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' }, body: form })
            .then(r => r.json()).then(d => {
                if (d.success) { this.pasoModal = false; location.reload(); } else alert(d.message || 'Error al completar paso.');
            }).catch(e => alert('Error de red: ' + e.message));
    },
    abrirRevision(p) {
        this.revisionActual = p;
        this.revisionAccion = '';
        this.revisionComentario = '';
        this.revisionModal = true;
    },
    enviarRevision() {
        if (!this.revisionActual || !this.revisionAccion) return;
        fetch('{{ url('/flujos/paso') }}/' + this.revisionActual.id + '/revisar', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ accion: this.revisionAccion, comentario: this.revisionComentario })
        }).then(r => r.json()).then(d => {
            if (d.success) { this.revisionModal = false; location.reload(); } else alert(d.message || 'Error al revisar.');
        }).catch(e => alert('Error de red: ' + e.message));
    },
    eliminarFlujo(id) {
        if (!id) return;
        if (!confirm('¿Estás seguro de eliminar este flujo? Esta acción no se puede deshacer.')) return;
        fetch('{{ url('/flujos-trabajo') }}/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' }
        }).then(r => r.json()).then(d => {
            if (d.success || d.id) { location.reload(); } else alert(d.message || 'Error al eliminar el flujo.');
        }).catch(e => alert('Error de red: ' + e.message));
    }
}">

    <div class="flex items-center gap-3">
        @if(in_array(Auth::user()->role?->slug, ['empleado', 'lider_equipo']))
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                Mis Flujos
            </span>
        @else
            <a href="{{ route('flujos', ['ver' => 'mios']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('ver') === 'mios' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-50 text-slate-500 border border-slate-200 hover:bg-slate-100' }}">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                Mis Flujos
            </a>
            <a href="{{ route('flujos') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('ver') !== 'mios' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-50 text-slate-500 border border-slate-200 hover:bg-slate-100' }}">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                Todos los Flujos
            </a>
        @endif
    </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Línea de Tiempo del Flujo</h1>
                    <p class="text-slate-500 text-sm">Monitoreo de etapas, participantes, tareas, reglas y caminos de aprobación.</p>
                </div>
                <span x-show="selectedFlujo && flujoEstados[selectedFlujo]"
                      :class="{
                          'bg-emerald-50 text-emerald-700 border-emerald-200': flujoEstados[selectedFlujo] === 'Activo',
                          'bg-amber-50 text-amber-700 border-amber-200': flujoEstados[selectedFlujo] === 'Borrador' || flujoEstados[selectedFlujo] === 'Pausado',
                          'bg-blue-50 text-blue-700 border-blue-200': flujoEstados[selectedFlujo] === 'Completado'
                      }"
                      class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold border">
                    <span class="h-2 w-2 rounded-full"
                          :class="{
                              'bg-emerald-500 animate-pulse': flujoEstados[selectedFlujo] === 'Activo',
                              'bg-amber-500': flujoEstados[selectedFlujo] === 'Borrador' || flujoEstados[selectedFlujo] === 'Pausado',
                              'bg-blue-500': flujoEstados[selectedFlujo] === 'Completado'
                          }"></span>
                    <span x-text="'Estado: ' + flujoEstados[selectedFlujo]"></span>
                </span>
            </div>

            @if($esSuperAdmin || !$verMios)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <form method="GET" action="{{ route('flujos') }}">
                        @if(request('ver') === 'mios')
                            <input type="hidden" name="ver" value="mios">
                        @endif
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Filtrar por Equipo</label>
                        <select name="equipo_id" onchange="this.form.submit()" class="w-full sm:w-96 rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Todos los equipos</option>
                            @foreach($equipos as $equipo)
                                <option value="{{ $equipo->id }}" {{ request('equipo_id') == $equipo->id ? 'selected' : '' }}>{{ $equipo->nombre }}</option>
                            @endforeach
                        </select>
                        <noscript><button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-lg text-sm">Filtrar</button></noscript>
                    </form>
                </div>
            @endif

            @if($flujos->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
                    <svg class="h-16 w-16 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    <h3 class="text-lg font-semibold text-slate-600 mb-1">{{ $verMios ? 'No tienes flujos asignados' : 'No hay flujos de trabajo' }}</h3>
                    <p class="text-slate-400 text-sm">{{ $verMios ? 'Aún no te han asignado pasos en ningún flujo.' : 'Crea un flujo desde el panel de administración para visualizarlo aquí.' }}</p>
                </div>
            @elseif($verMios)
                <div class="space-y-4">
                    @foreach($flujos as $flujo)
                        @php
                            $misPasosFlujo = $misPasosPendientes->filter(fn($p) => $p->ejecucion?->flujo_trabajo_id === $flujo->id);
                            $ejecucionFlujo = $misPasosFlujo->first()?->ejecucion ?? $flujo->ejecuciones->first();
                            $counts = $ejecucionFlujo && isset($pasoCounts[$ejecucionFlujo->id])
                                ? $pasoCounts[$ejecucionFlujo->id]
                                : null;
                            $totalAsignados = $counts?->total ?? $misPasosFlujo->count();
                            $completadosCount = $counts?->completados ?? 0;
                        @endphp
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">{{ $flujo->codigo }}</span>
                                    <h3 class="text-xl font-bold text-slate-800 mt-1">{{ $flujo->nombre }}</h3>
                                    <p class="text-sm text-slate-500">{{ $flujo->departamento }}@if($flujo->equipo) · {{ $flujo->equipo->nombre }}@endif</p>
                                </div>
                                <div class="text-right text-sm">
                                    <span class="text-slate-400">Progreso:</span>
                                    <p class="font-semibold text-slate-700">{{ $completadosCount }}/{{ $totalAsignados }} pasos</p>
                                </div>
                            </div>
                            @if($misPasosFlujo->isNotEmpty())
                                <div class="mt-4 space-y-2">
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tus pasos pendientes</p>
                                    @foreach($misPasosFlujo as $paso)
                                        @php
                                            $pasoDef = ($flujo->pasos ?? [])[$paso->paso_index] ?? [];
                                            $checklist = $pasoDef['checklist'] ?? [];
                                            $revisorId = $pasoDef['revisor_id'] ?? null;
                                        @endphp
                                        <div class="flex items-start justify-between p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-slate-700">{{ $paso->paso_nombre }}</p>
                                                @if(!empty($checklist))
                                                    <div class="mt-1.5 space-y-0.5">
                                                        @foreach($checklist as $ci)
                                                            <div class="flex items-center gap-1.5">
                                                                <svg class="h-2.5 w-2.5 text-slate-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7" /></svg>
                                                                <span class="text-[11px] text-slate-500">{{ $ci['item'] ?? '' }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($paso->fecha_limite)
                                                    <p class="text-xs mt-0.5 {{ now()->greaterThan($paso->fecha_limite) ? 'text-rose-500 font-semibold' : 'text-amber-500' }}">
                                                        Límite: {{ $paso->fecha_limite->format('d/m/Y H:i') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="shrink-0 ml-3 flex flex-col items-end gap-1.5">
                                                <button @click="abrirPaso({{ $paso->id }})" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                                    Completar
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Seleccionar Flujo</label>
                    <select x-model="selectedFlujo" class="w-full sm:w-96 rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        @foreach($flujos as $flujo)
                            <option value="{{ $flujo->id }}">{{ $flujo->codigo }} — {{ $flujo->nombre }} ({{ $flujo->departamento }})@if($flujo->equipo) — {{ $flujo->equipo->nombre }}@endif</option>
                        @endforeach
                    </select>
                </div>

                @foreach($flujos as $flujo)
                    <div x-show="selectedFlujo == {{ $flujo->id }}" x-cloak>
                        <div class="flex items-center gap-3 mb-4 text-xs text-slate-400">
                            <span>Creado por <strong class="text-slate-600">{{ $flujo->user?->apodo ?? $flujo->user?->name ?? '—' }}</strong></span>
                            @if($flujo->equipo)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-md font-medium">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ $flujo->equipo->nombre }}
                                </span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                                <div class="space-y-1">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total de Etapas</span>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ $flujo->estados->count() }} Etapas</h3>
                                </div>
                                <div class="p-2.5 bg-blue-50 text-[#007BFF] rounded-xl">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V16zM14 14a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                                <div class="space-y-1">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Participantes</span>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ $flujo->estados->flatMap->actores->count() }} Participantes</h3>
                                </div>
                                <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                                <div class="space-y-1">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tareas Totales</span>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ $flujo->estados->flatMap->actividades->count() + $flujo->ejecuciones->sum('pasos_completados') }} Tareas</h3>
                                </div>
                                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                </div>
                            </div>
                        </div>

                        @php $pasosFlujo = $flujo->pasos ?? []; @endphp
                        @if(!empty($pasosFlujo))
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                                    <h3 class="text-base font-bold text-slate-700">Pasos del Flujo ({{ count($pasosFlujo) }})</h3>
                                </div>
                                <div class="p-6 space-y-4">
                                    @foreach($pasosFlujo as $i => $paso)
                                        @php
                                            $asignadosNombres = [];
                                            $asignadosIds = $paso['asignados_ids'] ?? [];
                                            if (!empty($asignadosIds)) {
                                                foreach ($asignadosIds as $aid) {
                                                    $uid = (int) $aid;
                                                    $asignadosNombres[] = isset($pasoUsuarios[$uid]) ? $pasoUsuarios[$uid]->name : "Usuario #{$uid}";
                                                }
                                            }
                                            if (empty($asignadosNombres) && !empty($paso['asignacion_usuario_id']) && isset($pasoUsuarios[(int) $paso['asignacion_usuario_id']])) {
                                                $asignadosNombres[] = $pasoUsuarios[(int) $paso['asignacion_usuario_id']]->name;
                                            }
                                            if (empty($asignadosNombres) && !empty($paso['asignacion_rol'])) {
                                                $asignadosNombres[] = ucfirst(str_replace('_', ' ', $paso['asignacion_rol']));
                                            }
                                            $revisorId = $paso['revisor_id'] ?? null;
                                            $revisorNombre = '';
                                            if ($revisorId && isset($pasoUsuarios[(int) $revisorId])) {
                                                $revisorNombre = $pasoUsuarios[(int) $revisorId]->name;
                                            }
                                        @endphp
                                        <div class="flex items-start gap-4 p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                                            <div class="h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center shrink-0 text-xs font-bold">{{ $i + 1 }}</div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-4">
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-700">{{ $paso['nombre'] ?? 'Paso ' . ($i + 1) }}</p>
                                                        @if(!empty($paso['descripcion']))
                                                            <p class="text-xs text-slate-500 mt-0.5">{{ $paso['descripcion'] }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="shrink-0 text-right text-xs space-y-1">
                                                        @if(!empty($asignadosNombres))
                                                            <div class="space-y-0.5">
                                                                @foreach($asignadosNombres as $an)
                                                                    <span class="inline-block px-2 py-0.5 rounded-md font-medium bg-blue-50 text-blue-600 mr-1">{{ $an }}</span>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="inline-block px-2 py-0.5 rounded-md font-medium bg-slate-100 text-slate-400">Sin asignar</span>
                                                        @endif
                                                        @if($revisorNombre)
                                                            <div class="mt-1">
                                                                <span class="text-[10px] text-amber-600 font-medium">Revisor: {{ $revisorNombre }}</span>
                                                            </div>
                                                        @endif
                                                        @if(!empty($paso['fecha_limite_horas']))
                                                            <p class="text-amber-600 font-medium">{{ $paso['fecha_limite_horas'] }}h límite</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(!empty($paso['prioridad']) && $paso['prioridad'] !== 'media')
                                                    <span class="inline-block mt-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $paso['prioridad'] === 'alta' ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-500' }}">
                                                        {{ ucfirst($paso['prioridad']) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

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
                                                            <span>Participantes ({{ count($estado->actores ?? []) }})</span>
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
                                                            <span>Tareas ({{ count($estado->actividades ?? []) }})</span>
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
                                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3">Caminos de aprobación</span>
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

            @if(!$verMios)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-base font-bold text-slate-700 mb-4">Mis Pasos Asignados</h3>
                    @if($misPasosPendientes->isEmpty())
                        <p class="text-sm text-slate-400">No tienes pasos pendientes.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($misPasosPendientes as $paso)
                                @php
                                    $pasoDef = ($paso->ejecucion->flujoTrabajo->pasos ?? [])[$paso->paso_index] ?? [];
                                    $checklist = $pasoDef['checklist'] ?? [];
                                @endphp
                                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-700">{{ $paso->paso_nombre }}</p>
                                        <p class="text-xs text-slate-400 truncate">Flujo: {{ $paso->ejecucion?->flujoTrabajo?->nombre ?? '—' }}</p>
                                        @if(!empty($checklist))
                                            <div class="mt-2 space-y-1">
                                                <p class="text-[10px] font-semibold text-slate-400 uppercase">Pasos internos:</p>
                                                @foreach($checklist as $ci)
                                                    <div class="flex items-center gap-1.5">
                                                        <svg class="h-3 w-3 text-slate-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7" /></svg>
                                                        <span class="text-xs text-slate-500">{{ $ci['item'] ?? '' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($paso->fecha_limite)
                                            <p class="text-xs mt-1 {{ now()->greaterThan($paso->fecha_limite) ? 'text-rose-500 font-semibold' : 'text-amber-500' }}">
                                                Límite: {{ $paso->fecha_limite->format('d/m/Y H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0 ml-4">
                                        <span class="text-xs font-medium px-2 py-1 rounded-md {{ $paso->estado === 'en_progreso' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }}">
                                            {{ $paso->estado === 'en_progreso' ? 'En progreso' : 'Pendiente' }}
                                        </span>
                                        <button @click="abrirPaso({{ $paso->id }})" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                            Completar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-base font-bold text-slate-700 mb-4">Revisiones Pendientes</h3>
                    @if($pendientesRevision->isEmpty())
                        <p class="text-sm text-slate-400">No tienes pasos pendientes de revisión.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($pendientesRevision as $paso)
                                <div class="flex items-center justify-between p-4 rounded-xl border border-amber-100 bg-amber-50/30">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-700">{{ $paso->paso_nombre }}</p>
                                        <p class="text-xs text-slate-500">Flujo: {{ $paso->ejecucion?->flujoTrabajo?->nombre ?? '—' }}</p>
                                        <p class="text-xs text-amber-600 font-medium mt-1">Esperando tu revisión</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0 ml-4">
                                        <button @click="abrirRevision(misRevisiones.find(r => r.id === {{ $paso->id }}))" class="px-3 py-1.5 bg-amber-600 text-white text-xs font-semibold rounded-lg hover:bg-amber-700 transition-colors">
                                            Revisar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if(($esSuperAdmin || in_array(Auth::user()->role?->slug, ['administrador', 'gerente'])) && !$verMios)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-base font-bold text-slate-700 mb-4">Iniciar Flujo</h3>
                    <div class="flex items-center gap-4">
                        <select x-model="selectedFlujo" class="w-full sm:w-96 rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Seleccionar flujo...</option>
                            @foreach($flujos as $flujo)
                                <option value="{{ $flujo->id }}">{{ $flujo->codigo }} — {{ $flujo->nombre }}</option>
                            @endforeach
                        </select>
                        <button @click="if(!selectedFlujo) { alert('Selecciona un flujo.'); return; } fetch('{{ url('/flujos') }}/'+selectedFlujo+'/iniciar',{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}}).then(r=>r.json()).then(d=>{if(d.success)location.reload();else alert(d.message||'Error al iniciar flujo.')}).catch(e=>alert('Error de red: '+e.message))" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
                            Iniciar ejecución
                        </button>
                        @if($esSuperAdmin || Auth::user()->role?->slug === 'administrador')
                        <button @click="eliminarFlujo(selectedFlujo)" x-show="selectedFlujo" class="px-4 py-2.5 bg-rose-600 text-white text-sm font-semibold rounded-xl hover:bg-rose-700 transition-colors">
                            Eliminar flujo
                        </button>
                        @endif
                    </div>
                    <div x-show="selectedFlujo" class="mt-2">
                        <span class="text-[11px] text-slate-400">Flujo seleccionado: <span x-text="selectedFlujo ? (flujoNombres[selectedFlujo] || 'ID: ' + selectedFlujo) : ''"></span></span>
                    </div>
                </div>
            @endif

            {{-- Modal completar paso --}}
            <div x-show="pasoModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/30" @click.outside="pasoModal = false">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-slate-800" x-text="'Completar: ' + (pasoActual?.paso_nombre || '')"></h3>
                    <p class="text-sm text-slate-500" x-text="'Flujo: ' + (pasoActual?.flujo_nombre || '')"></p>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Pasos internos a seguir</label>
                        <template x-if="pasoActual && pasoActual.checklist && pasoActual.checklist.length > 0">
                            <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 mb-3">
                                <template x-for="(item, ci) in pasoActual.checklist" :key="ci">
                                    <div class="flex items-center gap-2 py-0.5">
                                        <svg class="h-3 w-3 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7" /></svg>
                                        <span class="text-xs text-slate-500" x-text="item.item"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Mensaje (opcional)</label>
                        <textarea x-model="mensajePaso" rows="3" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Ej: Adjunto documento aprobado..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Archivo (opcional)</label>
                        <input type="file" id="archivo_paso" class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button @click="pasoModal = false" class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors">Cancelar</button>
                        <button @click="completarPaso" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors">Marcar como completado</button>
                    </div>
                </div>
            </div>

            {{-- Modal revisar paso --}}
            <div x-show="revisionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/30" @click.outside="revisionModal = false">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-slate-800" x-text="'Revisar: ' + (revisionActual?.paso_nombre || '')"></h3>
                    <p class="text-sm text-slate-500" x-text="'Flujo: ' + (revisionActual?.flujo_nombre || '')"></p>
                    <div x-show="revisionActual?.mensaje" class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Mensaje del ejecutor</label>
                        <p class="text-sm text-slate-700 italic" x-text="revisionActual?.mensaje"></p>
                    </div>
                    <div x-show="revisionActual?.archivo_url" class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                        <label class="block text-xs font-semibold text-blue-400 uppercase tracking-wider mb-1">Archivo adjunto</label>
                        <a :href="revisionActual?.archivo_url" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline font-medium flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span x-text="revisionActual?.archivo_nombre || 'Descargar archivo'"></span>
                        </a>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Decisión</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition-colors" :class="revisionAccion === 'aprobar' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 hover:border-slate-300'" @click="revisionAccion = 'aprobar'">
                                <input type="radio" name="revision_accion" value="aprobar" x-model="revisionAccion" class="sr-only">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7" /></svg>
                                <span class="text-sm font-semibold">Aprobar</span>
                            </label>
                            <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition-colors" :class="revisionAccion === 'rechazar' ? 'border-rose-500 bg-rose-50 text-rose-700' : 'border-slate-200 hover:border-slate-300'" @click="revisionAccion = 'rechazar'">
                                <input type="radio" name="revision_accion" value="rechazar" x-model="revisionAccion" class="sr-only">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                <span class="text-sm font-semibold">Rechazar</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Comentario</label>
                        <textarea x-model="revisionComentario" rows="3" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Explica el motivo de tu decisión..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button @click="revisionModal = false" class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors">Cancelar</button>
                        <button @click="enviarRevision" :disabled="!revisionAccion" :class="revisionAccion === 'aprobar' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'" class="px-4 py-2 text-white text-sm font-semibold rounded-xl transition-colors" x-text="revisionAccion === 'aprobar' ? 'Aprobar paso' : revisionAccion === 'rechazar' ? 'Rechazar paso' : 'Selecciona una acción'"></button>
                    </div>
                </div>
            </div>
        @include('partials.solicitar-modal')
        </main>
    </div>

</body>
</html>