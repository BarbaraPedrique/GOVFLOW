<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Flujos de Trabajo - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; }

        .badge-activo     { background:#DCFCE7; color:#16A34A; }
        .badge-borrador   { background:#F1F5F9; color:#64748B; }
        .badge-completado { background:#DBEAFE; color:#2563EB; }
        .badge-pausado    { background:#FEF9C3; color:#CA8A04; }

        .badge {
            display:inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: .75rem;
            font-weight: 600;
        }

        tr { transition: background .15s; }
        tr:hover td { background: #F8FAFC; }

        /* Modal */
        .modal-backdrop {
            position:fixed; inset:0;
            background:rgba(15,23,42,.35);
            backdrop-filter: blur(2px);
            z-index:100;
            display:flex; align-items:center; justify-content:center;
        }
        .modal-box {
            background:#fff;
            border-radius:16px;
            padding:2rem;
            width:100%; max-width:440px;
            box-shadow: 0 20px 60px rgba(0,0,0,.15);
            animation: popIn .2s ease;
        }
        @keyframes popIn {
            from { opacity:0; transform:scale(.95) translateY(8px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }

        .input-field {
            width:100%;
            border:1.5px solid #E2E8F0;
            border-radius:10px;
            padding:.6rem .9rem;
            font-size:.9rem;
            outline:none;
            transition:border-color .2s, box-shadow .2s;
        }
        .input-field:focus {
            border-color:#007BFF;
            box-shadow:0 0 0 3px rgba(0,123,255,.12);
        }
        .input-field.is-invalid { border-color:#EF4444; }
        .invalid-feedback { color:#EF4444; font-size:.78rem; margin-top:.25rem; display:block; }
    </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

{{-- ── Sidebar ── --}}
<aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-20">
    <div class="h-16 border-b border-slate-100 flex items-center px-6">
        <img src="{{ asset('imagenes/logo2.png') }}" alt="GOVFLOW" class="h-8 w-auto object-contain"/>
    </div>
    <nav class="p-4 space-y-1 flex-1">
        <a href="{{ url('/inicio') }}"
           class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V16zM14 14a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('flujos.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#007BFF] bg-blue-50 rounded-xl transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Flujos de Trabajo
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Auditoría
        </a>
    </nav>
</aside>

{{-- ── Contenido principal ── --}}
<div class="flex-1 ml-64 flex flex-col min-h-screen">

    {{-- Header --}}
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <h2 class="text-slate-800 font-semibold text-lg">Panel de Control</h2>
        <div class="flex items-center gap-6">
            <button class="relative text-slate-400 hover:text-slate-600 transition-colors">
                <span class="absolute top-0 right-0 h-2 w-2 bg-rose-500 rounded-full border-2 border-white"></span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.03 6.03 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </button>
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-800 leading-none">Juan Pérez</p>
                        <p class="text-[11px] text-slate-400 font-medium mt-0.5">Administrador</p>
                    </div>
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80"
                         alt="Avatar" class="h-9 w-9 rounded-full object-cover border border-slate-200"/>
                    <svg class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div x-show="open" @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50">
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-[#007BFF] transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Mi Perfil
                    </a>
                    <hr class="my-2 border-slate-100"/>
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Main --}}
    <main class="flex-1 p-10 mt-16">

        {{-- Título + botón --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Flujos de Trabajo</h1>
                <p class="text-slate-500 text-sm mt-1">Gestiona los procesos y aprobaciones de la organización</p>
            </div>
            <a href="{{ route('flujos.create') }}"
               class="flex items-center gap-2 bg-[#007BFF] hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Nuevo Flujo
            </a>
        </div>

        {{-- Alerta éxito --}}
        @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium px-4 py-3 rounded-xl">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Card tabla --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- Buscador --}}
            <div class="px-6 py-4 border-b border-slate-100">
                <form method="GET" action="{{ route('flujos.index') }}">
                    <div class="relative max-w-sm">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                               placeholder="Buscar flujos..."
                               class="pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl w-full outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-100 transition"/>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">ID</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nombre del Flujo</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Departamento</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Fecha Creación</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Estado</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($flujos as $flujo)
                        <tr>
                            <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $flujo->codigo }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $flujo->nombre }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $flujo->departamento }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $flujo->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4">
                                <span class="badge {{ $flujo->badgeClase() }}">{{ $flujo->estado }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="relative inline-block" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="absolute right-0 mt-1 w-40 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-50">
                                        <a href="{{ route('flujos.edit', $flujo) }}"
                                           class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-[#007BFF] transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.25 2.25 0 113.182 3.182L12 20.25l-4.5 1.5 1.5-4.5 9.914-9.914z"/>
                                            </svg>
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('flujos.destroy', $flujo) }}"
                                              onsubmit="return confirm('¿Eliminar este flujo?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <svg class="h-10 w-10 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                No hay flujos de trabajo registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>
