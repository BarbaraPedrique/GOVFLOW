<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

    @include('partials.sidebar')

    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
            <div>
                <h2 class="text-slate-800 font-semibold text-lg">Panel de Auditoría</h2>
            </div>
            <div class="flex items-center gap-6">
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

        <main class="flex-1 p-10 mt-16 max-w-[1400px] w-full mx-auto space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Eventos</p>
                            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $totalEventos }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 text-[#007BFF] rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Proyectos / Flujos</p>
                            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $proyectosActivos }}</p>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Usuarios Activos</p>
                            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $grupos->pluck('logs')->flatten()->pluck('user_id')->unique()->count() }}</p>
                        </div>
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" /></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Eventos / Proyecto</p>
                            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $proyectosActivos > 0 ? round($totalEventos / $proyectosActivos, 1) : 0 }}</p>
                        </div>
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <form method="GET" action="{{ route('auditoria') }}" class="flex flex-wrap items-end gap-4">
                    <div class="min-w-[200px] flex-1">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Proyecto / Flujo</label>
                        <select name="flujo_id" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Todos los proyectos</option>
                            @foreach($flujos as $flujo)
                                <option value="{{ $flujo->id }}" {{ request('flujo_id') == $flujo->id ? 'selected' : '' }}>{{ $flujo->codigo }} — {{ $flujo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[180px] flex-1">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Equipo / Departamento</label>
                        <select name="departamento" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Todos los equipos</option>
                            @foreach($departamentos as $depto)
                                <option value="{{ $depto }}" {{ request('departamento') == $depto ? 'selected' : '' }}>{{ $depto }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[160px]">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                    <div class="min-w-[160px]">
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-5 py-2.5 bg-[#007BFF] text-white text-sm font-semibold rounded-xl hover:bg-blue-600 transition-colors">Filtrar</button>
                        <a href="{{ route('equipos.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg> Equipos</a>
        <a href="{{ route('auditoria') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-colors">Limpiar</a>
                    </div>
                </form>
            </div>

            @if($grupos->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
                    <svg class="h-16 w-16 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    <h3 class="text-lg font-semibold text-slate-600 mb-1">Sin eventos de auditoría</h3>
                    <p class="text-slate-400 text-sm">No se encontraron registros con los filtros aplicados.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($grupos as $grupo)
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ open: true }">
                            <button @click="open = !open" class="w-full px-8 py-5 flex items-center justify-between hover:bg-slate-50 transition-colors text-left">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-slate-800">{{ $grupo->entity_name }}</h3>
                                        <div class="flex items-center gap-3 mt-0.5">
                                            <span class="text-xs text-slate-400">{{ $grupo->entity_type }}</span>
                                            <span class="text-xs text-slate-300">•</span>
                                            <span class="text-xs font-medium text-blue-600">{{ $grupo->cantidad }} evento(s)</span>
                                            <span class="text-xs text-slate-300">•</span>
                                            <span class="text-xs text-slate-400">Último: {{ $grupo->ultimo->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-slate-400 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                            </button>

                            <div x-show="open" x-collapse>
                                <div class="border-t border-slate-100">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                    <th class="px-8 py-4 w-1/6">Quién</th>
                                                    <th class="px-8 py-4 w-1/6">Cuándo</th>
                                                    <th class="px-8 py-4 w-1/6">Qué hizo</th>
                                                    <th class="px-8 py-4 w-1/6">Sobre qué</th>
                                                    <th class="px-8 py-4 w-1/3">Qué cambió</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach($grupo->logs as $log)
                                                    <tr class="hover:bg-slate-50 transition-colors">
                                                        <td class="px-8 py-4">
                                                            <div class="flex items-center gap-2">
                                                                <img src="{{ $log->user?->foto ? asset('storage/'.$log->user->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=60&q=80' }}" class="h-7 w-7 rounded-full object-cover shrink-0">
                                                                <span class="font-medium text-slate-700">{{ $log->user?->name ?? 'Sistema' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-8 py-4 text-slate-500 whitespace-nowrap">
                                                            <span class="text-xs">{{ $log->created_at->format('d/m/Y') }}</span>
                                                            <span class="text-xs text-slate-400 ml-1">{{ $log->created_at->format('H:i') }}</span>
                                                        </td>
                                                        <td class="px-8 py-4">
                                                            @php
                                                                $accionColor = match($log->accion) {
                                                                    'crear' => 'text-emerald-700 bg-emerald-50',
                                                                    'actualizar', 'update' => 'text-blue-700 bg-blue-50',
                                                                    'eliminar', 'delete' => 'text-rose-700 bg-rose-50',
                                                                    default => 'text-slate-700 bg-slate-100'
                                                                };
                                                            @endphp
                                                            <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $accionColor }}">{{ ucfirst($log->accion) }}</span>
                                                        </td>
                                                        <td class="px-8 py-4 text-slate-500 text-xs">{{ $log->descripcion }}</td>
                                                        <td class="px-8 py-4">
                                                            @if($log->metadata && is_array($log->metadata) && count($log->metadata) > 0)
                                                                <div class="space-y-1">
                                                                    @foreach($log->metadata as $campo => $valor)
                                                                        @if(is_array($valor) && isset($valor['old']) && isset($valor['new']))
                                                                            <div class="flex items-start gap-2 text-xs">
                                                                                <span class="font-medium text-slate-600 shrink-0">{{ $campo }}:</span>
                                                                                <div class="flex flex-wrap items-center gap-1">
                                                                                    <span class="line-through text-rose-500">{{ $valor['old'] ?? '' }}</span>
                                                                                    <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                                                                    <span class="text-emerald-600 font-medium">{{ $valor['new'] ?? '' }}</span>
                                                                                </div>
                                                                            </div>
                                                                        @elseif(is_string($campo))
                                                                            <div class="text-xs text-slate-500">
                                                                                <span class="font-medium text-slate-600">{{ $campo }}:</span>
                                                                                <span>{{ is_string($valor) ? $valor : json_encode($valor) }}</span>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @elseif($log->metadata)
                                                                <span class="text-xs text-slate-400 italic">{{ is_string($log->metadata) ? $log->metadata : 'Sin detalles' }}</span>
                                                            @else
                                                                <span class="text-xs text-slate-400 italic">Sin cambios registrados</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        @include('partials.solicitar-modal')
        </main>
    </div>

</body>
</html>