<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

@include('partials.sidebar')

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Equipos</h2></div>
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
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg p-4">{{ session('success') }}</div>
        @endif

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestión de Equipos</h1>
                <p class="text-slate-500 text-sm mt-1">Crea y administra equipos de trabajo con roles internos.</p>
            </div>
            <a href="{{ route('equipos.create') }}" class="px-5 py-2.5 bg-[#007BFF] text-white text-sm font-semibold rounded-xl hover:bg-blue-600 transition-colors flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4" /></svg>
                Nuevo Equipo
            </a>
        </div>

        @if($equipos->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
                <svg class="h-16 w-16 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                <h3 class="text-lg font-semibold text-slate-600 mb-1">No hay equipos</h3>
                <p class="text-slate-400 text-sm">Crea tu primer equipo para comenzar a organizar tu personal.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($equipos as $equipo)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">{{ $equipo->nombre }}</h3>
                                    @if($equipo->descripcion)
                                        <p class="text-sm text-slate-400 mt-1">{{ $equipo->descripcion }}</p>
                                    @endif
                                </div>
                                <div class="flex gap-1">
                                    <a href="{{ route('equipos.edit', $equipo) }}" class="p-2 text-slate-400 hover:text-[#007BFF] rounded-lg hover:bg-slate-50 transition-colors" title="Editar">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.25 2.25 0 113.182 3.182L12 20.25l-4.5 1.5 1.5-4.5L18.586 3.586z" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('equipos.destroy', $equipo) }}" onsubmit="return confirm('¿Eliminar este equipo?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-slate-50 transition-colors" title="Eliminar">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center gap-3 bg-slate-50 rounded-lg px-4 py-2.5">
                                    <div class="h-8 w-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ substr($equipo->gerente?->name ?? '?', 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-slate-400 font-medium">Gerente</p>
                                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $equipo->gerente?->name ?? 'Sin asignar' }}</p>
                                    </div>
                                </div>

                                @php
                                    $lideres = $equipo->miembros->where('pivot.rol', 'lider_equipo');
                                    $empleados = $equipo->miembros->where('pivot.rol', 'empleado');
                                @endphp

                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Líderes ({{ $lideres->count() }})</p>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($lideres as $lider)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                {{ $lider->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">Sin líderes</span>
                                        @endforelse
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Empleados ({{ $empleados->count() }})</p>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($empleados as $emp)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 text-slate-600 rounded-lg text-xs font-medium">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                {{ $emp->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">Sin empleados</span>
                                        @endforelse
                                    </div>
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
