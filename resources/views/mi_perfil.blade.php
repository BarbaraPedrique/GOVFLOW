<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen" x-data="{ equipoModal: null }">

    @include('partials.sidebar')

    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
            <div>
                <h2 class="text-slate-800 font-semibold text-lg">Mi Perfil</h2>
            </div>
            <div class="flex items-center gap-6">
                @include('partials.break-buttons')
                @include('partials.notification-bell')
                <div class="relative" x-data="{ open: false }">
                    <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                        <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-800 leading-none">{{ $user->apodo ?? $user->name }}</p><p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $user->role?->display_name ?? 'Sin rol' }}</p></div>
                        <img src="{{ $user->foto ? asset('storage/'.$user->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                        <svg class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                    </div>
                    @include('partials.user-dropdown')
                </div>
            </div>
        </header>

        <main class="flex-1 p-10 mt-16 max-w-[1200px] w-full mx-auto space-y-8">

            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center gap-6">
                        <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80' }}" alt="Avatar" class="h-20 w-20 rounded-full object-cover border-2 border-white shadow-md ring-4 ring-white">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">{{ $user->apodo ?? $user->name }}</h1>
                            <p class="text-slate-500 text-sm mt-1">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    {{ $user->email }}
                                </span>
                                <span class="mx-2 text-slate-300">|</span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    {{ $user->role?->display_name ?? 'Sin rol' }}
                                </span>
                            </p>
                            @if ($user->descripcion)
                                <p class="text-sm text-slate-600 mt-2">{{ $user->descripcion }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-2 mt-3">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                @forelse ($equipos as $eq)
                                    <button @click="equipoModal = {{ $eq->id }}"
                                        class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full cursor-pointer hover:ring-2 hover:ring-offset-1 transition-all
                                        @if($eq->gerente_id === $user->id) bg-purple-50 text-purple-700 hover:ring-purple-300
                                        @elseif($user->equipos->contains($eq) && $user->equipos->find($eq->id)?->pivot->rol === 'lider_equipo') bg-amber-50 text-amber-700 hover:ring-amber-300
                                        @else bg-blue-50 text-blue-700 hover:ring-blue-300
                                        @endif">
                                        {{ $eq->nombre }}
                                    </button>
                                @empty
                                    <span class="text-sm text-slate-400">Sin equipo</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl border border-slate-200 p-6 text-center">
                        <p class="text-3xl font-bold text-slate-800">{{ $completados->count() }}</p>
                        <p class="text-sm text-slate-500 mt-1">Proyectos Completados</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-6 text-center">
                        <p class="text-3xl font-bold {{ $eficienciaGlobal >= 80 ? 'text-emerald-600' : ($eficienciaGlobal >= 50 ? 'text-amber-600' : 'text-rose-600') }}">{{ $eficienciaGlobal }}%</p>
                        <p class="text-sm text-slate-500 mt-1">Eficiencia Global</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-6 text-center">
                        <p class="text-3xl font-bold text-slate-800">{{ $completados->where('completado_a_tiempo', true)->count() }}</p>
                        <p class="text-sm text-slate-500 mt-1">Completados a Tiempo</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-800">Eficiencia Mensual</h3>
                </div>
                <div class="p-8">
                    @if ($eficienciaMensual->count() > 0)
                        <div class="space-y-4">
                            @foreach ($eficienciaMensual as $item)
                                @php
                                    $fecha = \Carbon\Carbon::createFromFormat('Y-m', $item->mes);
                                    $color = $item->eficiencia >= 80 ? 'bg-emerald-500' : ($item->eficiencia >= 50 ? 'bg-amber-500' : 'bg-rose-500');
                                @endphp
                                <div class="flex items-center gap-4">
                                    <div class="w-32 text-sm font-medium text-slate-700">{{ $fecha->locale('es')->isoFormat('MMMM Y') }}</div>
                                    <div class="flex-1 bg-slate-100 rounded-full h-3 overflow-hidden">
                                        <div class="{{ $color }} h-3 rounded-full transition-all" style="width: {{ $item->eficiencia }}%"></div>
                                    </div>
                                    <div class="w-20 text-right text-sm font-semibold text-slate-700">{{ $item->eficiencia }}%</div>
                                    <div class="w-32 text-right text-xs text-slate-400">
                                        {{ $item->a_tiempo }}/{{ $item->total }} a tiempo
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-400 text-center py-4">No hay datos de eficiencia disponibles.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-800">Proyectos Completados</h3>
                    <span class="text-xs font-medium text-slate-400">{{ $completados->count() }} registros</span>
                </div>
                <div class="overflow-x-auto">
                    @if ($completados->count() > 0)
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="px-8 py-4">Código</th>
                                    <th class="px-8 py-4">Nombre</th>
                                    <th class="px-8 py-4">Departamento</th>
                                    <th class="px-8 py-4">Fecha Límite</th>
                                    <th class="px-8 py-4">Completado</th>
                                    <th class="px-8 py-4">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($completados as $flujo)
                                    @php $aTiempo = $flujo->completado_a_tiempo; @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-8 py-4 font-mono text-xs font-semibold text-slate-800">{{ $flujo->codigo }}</td>
                                        <td class="px-8 py-4 font-medium text-slate-800">{{ $flujo->nombre }}</td>
                                        <td class="px-8 py-4 text-slate-500">{{ $flujo->departamento }}</td>
                                        <td class="px-8 py-4 text-slate-500">{{ $flujo->fecha_limite?->isoFormat('DD/MM/YYYY') ?? '—' }}</td>
                                        <td class="px-8 py-4 text-slate-500">{{ $flujo->fecha_completado?->isoFormat('DD/MM/YYYY') ?? '—' }}</td>
                                        <td class="px-8 py-4">
                                            @if ($aTiempo === true)
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4" /></svg>
                                                    A tiempo
                                                </span>
                                            @elseif ($aTiempo === false)
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                                    Vencido
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-sm text-slate-400 text-center py-8">Aún no has completado ningún proyecto.</p>
                    @endif
                </div>
            </div>

        <!-- Team Members Modal -->
        <div x-show="equipoModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" @click.outside="equipoModal = null">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[80vh] overflow-y-auto">
                @foreach ($equipos as $eq)
                    <div x-show="equipoModal === {{ $eq->id }}" x-cloak>
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">{{ $eq->nombre }}</h3>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Gerente: <span class="font-medium text-slate-600">{{ $eq->gerente?->name ?? '—' }}</span>
                                </p>
                            </div>
                            <button @click="equipoModal = null" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="p-6 space-y-3">
                            @php $miembros = $eq->miembros()->withPivot('rol')->get(); @endphp
                            @forelse ($miembros as $miembro)
                                <div class="flex items-center gap-3 py-2">
                                    <img src="{{ $miembro->foto ? asset('storage/'.$miembro->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-800">{{ $miembro->apodo ?? $miembro->name }}</p>
                                        <p class="text-xs text-slate-400">
                                            @php
                                                $rolPivot = $miembro->pivot?->rol ?? '';
                                            @endphp
                                            @if($miembro->id === $eq->gerente_id)
                                                <span class="text-purple-600 font-medium">Gerente</span>
                                            @elseif($rolPivot === 'lider_equipo')
                                                <span class="text-amber-600 font-medium">Líder</span>
                                            @else
                                                <span class="text-blue-600 font-medium">Miembro</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400 text-center py-4">Sin miembros en este equipo.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @include('partials.solicitar-modal')
    </main>
</div>

</body>
</html>
