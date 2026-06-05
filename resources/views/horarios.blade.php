<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } .hora-columna { @apply text-xs text-slate-400 font-medium text-right pr-3 py-2 w-16 flex-shrink-0; } .bloque-horario { @apply rounded-lg px-2 py-1.5 text-xs font-medium cursor-grab active:cursor-grabbing hover:shadow-md transition-all; }</style>
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
        <a href="{{ route('horarios.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#007BFF] bg-blue-50 rounded-xl transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> Horarios</a>
        <a href="{{ route('flujos') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg> Flujos de Trabajo</a>
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
        <div><h2 class="text-slate-800 font-semibold text-lg">Horario Semanal</h2></div>
        <div class="flex items-center gap-6">
            @include('partials.notification-bell')
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->name }}</p><p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p></div>
                    <img src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                    <svg class="h-4 w-4 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                </div>
                @include('partials.user-dropdown')
            </div>
        </div>
    </header>

    <main class="flex-1 p-10 mt-16 max-w-[1400px] w-full mx-auto space-y-8">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg p-4">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse" x-data="horarioApp()">
                        <thead><tr class="bg-slate-50">
                            <th class="w-16 px-2 py-3 text-xs font-semibold text-slate-400 text-right"></th>
                            @foreach ($horarioPorDia as $dia)
                                <th class="px-2 py-3 text-xs font-semibold text-slate-600 text-center min-w-[120px]">{{ $dia['nombre'] }}</th>
                            @endforeach
                        </tr></thead>
                        <tbody>
                            @for ($h = 6; $h <= 22; $h++)
                                <tr class="border-t border-slate-100">
                                    <td class="hora-columna">{{ sprintf('%02d:00', $h) }}</td>
                                    @foreach ($horarioPorDia as $d => $dia)
                                        <td class="relative border-l border-slate-50 h-14 px-1 align-top"
                                            @drop.prevent="dropBlock($event, {{ $d }}, '{{ sprintf('%02d:00', $h) }}')"
                                            @dragover.prevent
                                            x-data="{ hora: '{{ sprintf('%02d:00', $h) }}', dia: {{ $d }} }">
                                            @php
                                                $bloques = $dia['bloques']->filter(function($b) use ($h) {
                                                    $inicio = (int) substr($b->hora_inicio, 0, 2);
                                                    return $inicio === $h;
                                                });
                                            @endphp
                                            @foreach ($bloques as $bloque)
                                                @if (!empty($bloque->es_tarea))
                                                    <div class="bloque-horario text-white mb-1 opacity-80 cursor-default"
                                                         style="background-color: {{ $bloque->color ?? '#007BFF' }}"
                                                         title="Tarea: {{ $bloque->titulo }}">
                                                        <div class="font-semibold truncate">{{ $bloque->titulo }}</div>
                                                        <div class="opacity-80">{{ substr($bloque->hora_inicio, 0, 5) }} - {{ substr($bloque->hora_fin, 0, 5) }}</div>
                                                    </div>
                                                @else
                                                    <div draggable="true"
                                                         @dragstart="dragId={{ $bloque->id }}"
                                                         class="bloque-horario text-white mb-1"
                                                         style="background-color: {{ $bloque->color ?? '#007BFF' }}"
                                                         @click="editBlock({{ $bloque->id }})">
                                                        <div class="font-semibold truncate">{{ $bloque->titulo }}</div>
                                                        <div class="opacity-80">{{ substr($bloque->hora_inicio, 0, 5) }} - {{ substr($bloque->hora_fin, 0, 5) }}</div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </td>
                                    @endforeach
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 h-fit">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Agregar Bloque</h3>
                <form method="POST" action="{{ route('horarios.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Día</label>
                        <select name="dia_semana" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] text-sm bg-white">
                            @foreach ($horarioPorDia as $i => $dia)
                                <option value="{{ $i }}">{{ $dia['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Inicio</label>
                            <input type="time" name="hora_inicio" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Fin</label>
                            <input type="time" name="hora_fin" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Título</label>
                        <input type="text" name="titulo" required placeholder="Ej: Reunión equipo" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Color</label>
                            <input type="color" name="color" value="#007BFF" class="w-full h-9 rounded-xl border border-slate-200 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Ubicación</label>
                            <input type="text" name="ubicacion" placeholder="Sala 3" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-[#007BFF] text-sm font-semibold text-white hover:bg-blue-600">Agregar</button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    function horarioApp() {
        return {
            dragId: null,
            dropBlock(ev, dia, hora) {
                if (!this.dragId) return;
                const form = new FormData();
                form.append('dia_semana', dia);
                form.append('hora_inicio', hora);
                form.append('_method', 'PUT');
                fetch(`{{ url('/horarios') }}/${this.dragId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: form
                }).then(r => r.json()).then(d => { if (d.success) window.location.reload(); });
                this.dragId = null;
            },
            editBlock(id) {
                if (confirm('¿Eliminar este bloque?')) {
                    fetch(`{{ url('/horarios') }}/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(r => r.json()).then(d => { if (d.success) window.location.reload(); });
                }
            }
        };
    }
</script>
</body>
</html>
