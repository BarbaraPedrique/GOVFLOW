<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($equipo) ? 'Editar' : 'Nuevo' }} Equipo - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

@include('partials.sidebar')

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">{{ isset($equipo) ? 'Editar' : 'Nuevo' }} Equipo</h2></div>
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

    <main class="flex-1 p-10 mt-16 max-w-[800px] w-full mx-auto">
        <form method="POST" action="{{ isset($equipo) ? route('equipos.update', $equipo) : route('equipos.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-8">
            @csrf
            @isset($equipo) @method('PUT') @endisset

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-lg p-4">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Nombre del Equipo</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $equipo->nombre ?? '') }}" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Descripción</label>
                    <textarea name="descripcion" rows="3" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">{{ old('descripcion', $equipo->descripcion ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Gerente Principal del Equipo</label>
                    <select name="gerente_id" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-700 px-4 py-2.5 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Seleccionar gerente...</option>
                        @foreach($gerentes as $gerente)
                            <option value="{{ $gerente->id }}" {{ old('gerente_id', $equipo->gerente_id ?? '') == $gerente->id ? 'selected' : '' }}>
                                {{ $gerente->name }} ({{ $gerente->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold text-slate-700 mb-4">Asignar Miembros</h3>

                <div class="space-y-6">
                    @php
                        $miembrosEquipo = $equipo ? $equipo->miembros : collect();
                        $adminEquipoIds = old('admin_equipo', $miembrosEquipo->where('pivot.rol', 'administrador')->pluck('id')->toArray());
                        $gerentesEquipoIds = old('gerentes_equipo', $miembrosEquipo->where('pivot.rol', 'gerente')->pluck('id')->toArray());
                        $lideresIds = old('lideres', $miembrosEquipo->where('pivot.rol', 'lider_equipo')->pluck('id')->toArray());
                        $empIds = old('empleados', $miembrosEquipo->where('pivot.rol', 'empleado')->pluck('id')->toArray());
                    @endphp

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Administradores del Equipo</label>
                        <p class="text-[11px] text-slate-400 mb-2">Pueden revisar y aprobar pasos de flujos del equipo.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto bg-slate-50 rounded-xl p-4 border border-slate-200">
                            @foreach($admins as $user)
                                <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white transition-colors cursor-pointer">
                                    <input type="checkbox" name="admin_equipo[]" value="{{ $user->id }}" {{ in_array($user->id, $adminEquipoIds) ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $user->email }} ({{ $user->role?->display_name ?? '' }})</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Gerentes del Equipo</label>
                        <p class="text-[11px] text-slate-400 mb-2">Pueden iniciar flujos y revisar pasos del equipo.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto bg-slate-50 rounded-xl p-4 border border-slate-200">
                            @foreach($gerentesEquipo as $user)
                                <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white transition-colors cursor-pointer">
                                    <input type="checkbox" name="gerentes_equipo[]" value="{{ $user->id }}" {{ in_array($user->id, $gerentesEquipoIds) ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $user->email }} ({{ $user->role?->display_name ?? '' }})</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Líderes de Equipo</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto bg-slate-50 rounded-xl p-4 border border-slate-200">
                            @foreach($lideres as $user)
                                <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white transition-colors cursor-pointer">
                                    <input type="checkbox" name="lideres[]" value="{{ $user->id }}" {{ in_array($user->id, $lideresIds) ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $user->email }} ({{ $user->role?->display_name ?? '' }})</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Empleados</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto bg-slate-50 rounded-xl p-4 border border-slate-200">
                            @foreach($empleados as $user)
                                <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white transition-colors cursor-pointer">
                                    <input type="checkbox" name="empleados[]" value="{{ $user->id }}" {{ in_array($user->id, $empIds) ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $user->email }} ({{ $user->role?->display_name ?? '' }})</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('equipos.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 bg-[#007BFF] text-white text-sm font-semibold rounded-xl hover:bg-blue-600 transition-colors">
                    {{ isset($equipo) ? 'Actualizar Equipo' : 'Crear Equipo' }}
                </button>
            </div>
        </form>
    @include('partials.solicitar-modal')
    </main>
</div>

</body>
</html>