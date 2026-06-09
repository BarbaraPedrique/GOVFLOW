<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

@include('partials.sidebar')

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Personal</h2></div>
        <div class="flex items-center gap-6">
            @include('partials.break-buttons')
            @include('partials.notification-bell')
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->apodo ?? Auth::user()->name }}</p><p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p></div>
                    <img src="{{ Auth::user()->foto_url }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                    <svg class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                </div>
                @include('partials.user-dropdown')
            </div>
        </div>
    </header>

    <main class="flex-1 p-10 mt-16 max-w-[1400px] w-full mx-auto space-y-6" x-data="personalModal()">

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Personal</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats->total }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Con Equipo</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats->conEquipo }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Sin Equipo</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats->sinEquipo }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Rendimiento Promedio</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ round($stats->rendimientoPromedio ?? 0) }}%</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <table class="w-full text-[11px]">
                <thead>
                    <tr class="bg-slate-50 text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-2 py-2.5 w-[130px]">Nombre</th>
                        <th class="px-2 py-2.5 w-[70px]">Rol</th>
                        <th class="px-2 py-2.5 w-12">Estado</th>
                        <th class="px-2 py-2.5 w-1/5">Equipo(s)</th>
                        <th class="px-2 py-2.5 w-[70px]">Rendimiento</th>
                        <th class="px-2 py-2.5 w-[80px]">Registro</th>
                        <th class="px-2 py-2.5 w-[80px]">Roles Ant.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($personal as $p)
                        @php
                            $rolClass = match($p->role?->slug) {
                                'super_admin' => 'text-rose-700 bg-rose-50',
                                'administrador' => 'text-blue-700 bg-blue-50',
                                'gerente' => 'text-purple-700 bg-purple-50',
                                'lider_equipo' => 'text-amber-700 bg-amber-50',
                                'empleado' => 'text-slate-700 bg-slate-100',
                                default => 'text-slate-500 bg-slate-50'
                            };
                            $statusClass = match($p->status) {
                                'activo' => 'text-emerald-700 bg-emerald-50',
                                'suspendido' => 'text-amber-700 bg-amber-50',
                                'inactivo' => 'text-slate-500 bg-slate-100',
                                default => 'text-slate-500 bg-slate-50'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors cursor-pointer" @click="abrirDetalle({{ $p->id }})">
                            <td class="px-2 py-2">
                                <div class="flex items-center gap-1.5">
                                    <img src="{{ $p->foto }}" class="h-6 w-6 rounded-full object-cover shrink-0">
                                    <div class="truncate">
                                        <span class="font-medium text-slate-700 truncate max-w-[80px] block" title="{{ $p->name }}">{{ $p->name }}</span>
                                        <span class="text-[9px] text-slate-400 block truncate max-w-[80px]">{{ $p->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-2">
                                <span class="inline-block text-[10px] font-semibold px-1.5 py-0.5 rounded-full whitespace-nowrap {{ $rolClass }}">
                                    {{ $p->role?->display_name ?? $p->role?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-2 py-2">
                                <span class="inline-block text-[9px] font-semibold px-1 py-0.5 rounded-full whitespace-nowrap {{ $statusClass }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td class="px-2 py-2">
                                @if($p->equipos->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($p->equipos as $eq)
                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-medium text-slate-600 bg-slate-50 px-1.5 py-0.5 rounded-full border border-slate-200 whitespace-nowrap">
                                                {{ $eq->nombre }}
                                                <span class="text-[9px] text-slate-400">({{ $eq->rol }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Sin equipo</span>
                                @endif
                            </td>
                            <td class="px-2 py-2">
                                <div class="flex items-center gap-1.5">
                                    <div class="bg-slate-100 rounded-full h-1.5 w-12 overflow-hidden">
                                        <div class="h-full rounded-full {{ $p->rendimiento >= 80 ? 'bg-emerald-500' : ($p->rendimiento >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $p->rendimiento }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-semibold {{ $p->rendimiento >= 80 ? 'text-emerald-600' : ($p->rendimiento >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                        {{ $p->rendimiento }}%
                                    </span>
                                </div>
                                @include('partials.estrellas', ['puntuacion' => $p->estrellas, 'tamano' => 'h-3 w-3'])
                            </td>
                            <td class="px-2 py-2 text-slate-500 text-[10px] whitespace-nowrap">
                                <div>{{ $p->creado->format('d/m/Y') }}</div>
                                <div class="text-[9px] text-slate-400">{{ $p->tiempoRegistro }}</div>
                            </td>
                            <td class="px-2 py-2">
                                @if(!empty($p->rolesAnteriores))
                                    <div class="flex flex-col gap-0.5">
                                        @foreach($p->rolesAnteriores as $r)
                                            <span class="text-[10px] text-slate-500">{{ $r }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Ninguno</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($personal->isEmpty())
                <div class="px-2 py-12 text-center text-sm text-slate-400">No hay personal registrado.</div>
            @endif
        </div>

        <div>
            <div x-show="show" x-cloak class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" @click.self="cerrar">
                <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <img :src="user.foto" class="h-14 w-14 rounded-full object-cover border border-slate-200">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800" x-text="user.name"></h3>
                                    <p class="text-sm text-slate-500" x-text="user.email"></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full" :class="rolClass(user.role?.slug || '')" x-text="user.role?.name || '—'"></span>
                                        <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full" :class="statusClass(user.status)" x-text="user.status_label"></span>
                                    </div>
                                </div>
                            </div>
                            <button @click="cerrar" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mt-6">
                            <div class="bg-slate-50 rounded-xl p-3 text-center">
                                <p class="text-lg font-bold text-slate-800" x-text="user.rendimiento + '%'"></p>
                                <p class="text-[10px] text-slate-400">Rendimiento</p>
                                <div class="flex justify-center mt-1" x-html="renderStars(user.estrellas || 0)"></div>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 text-center">
                                <p class="text-lg font-bold text-slate-800" x-text="user.tiempoRegistro || '—'"></p>
                                <p class="text-[10px] text-slate-400">Tiempo en el sistema</p>
                                <p class="text-[9px] text-slate-400" x-text="'Desde ' + user.creado"></p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 text-center">
                                <p class="text-lg font-bold text-slate-800" x-text="(user.equipos?.length || 0) + ''"></p>
                                <p class="text-[10px] text-slate-400">Equipo(s)</p>
                                <p class="text-[9px] text-slate-400" x-text="user.equipos?.length ? user.equipos.map(e=>e.nombre).join(', ') : 'Sin equipo'"></p>
                            </div>
                        </div>

                        <div x-show="user.rolesAnteriores?.length" class="mt-4">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Roles anteriores</p>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="r in user.rolesAnteriores" :key="r.role">
                                    <span class="text-[11px] text-slate-600 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-200" x-text="r.role + (r.desde ? ' (' + r.desde + ')' : '')"></span>
                                </template>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 mt-6 pt-6">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Acciones</p>
                            <div class="space-y-3">

                                <form method="POST" x-bind:action="'{{ url('/personal') }}/' + user.id + '/rol'" class="flex items-end gap-3 flex-wrap" x-show="user.role?.slug !== 'super_admin'">
                                    @csrf
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Cambiar Rol</label>
                                        <select name="role_id" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                                            @foreach($roles as $r)
                                                <option value="{{ $r->id }}" x-bind:selected="user.role?.id === {{ $r->id }}">{{ $r->display_name ?? $r->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100 transition-colors">Guardar Rol</button>
                                </form>

                                <form method="POST" x-bind:action="'{{ url('/personal') }}/' + user.id + '/equipo'" class="flex items-end gap-3 flex-wrap">
                                    @csrf
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Asignar Equipo</label>
                                        <select name="equipo_id" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                                            <option value="">Sin equipo</option>
                                            @foreach($equipos as $eq)
                                                <option value="{{ $eq->id }}">{{ $eq->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 mb-1">Rol en equipo</label>
                                        <select name="rol_equipo" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white">
                                            <option value="empleado">Empleado</option>
                                            <option value="lider_equipo">Líder</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 text-xs font-semibold hover:bg-purple-100 transition-colors">Guardar Equipo</button>
                                </form>

                                <div class="flex items-center gap-3 flex-wrap">
                                    <form method="POST" x-bind:action="'{{ url('/personal') }}/' + user.id + '/suspender'" x-show="user.role?.slug !== 'super_admin'">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold hover:bg-amber-100 transition-colors flex items-center gap-1.5"
                                                x-text="user.status === 'suspendido' ? 'Reactivar cuenta' : 'Suspender temporalmente'">
                                        </button>
                                    </form>
                                    @if(Auth::user()->role?->slug === 'super_admin')
                                        <form method="POST" x-bind:action="'{{ url('/personal') }}/' + user.id" onsubmit="return confirm('¿Eliminar permanentemente este perfil? Esta acción no se puede deshacer.')" x-show="user.role?.slug !== 'super_admin'">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold hover:bg-rose-100 transition-colors flex items-center gap-1.5">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Eliminar perfil
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @include('partials.solicitar-modal')
    </main>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('personalModal', () => ({
        show: false,
        user: { role: {}, equipos: [], rolesAnteriores: [] },
        loading: false,

        abrirDetalle(userId) {
            this.loading = true;
            fetch('{{ url('/personal') }}/' + userId + '/detalle')
                .then(r => r.json())
                .then(data => {
                    this.user = data;
                    this.show = true;
                    this.loading = false;
                }).catch(() => { this.loading = false; });
        },
        cerrar() {
            this.show = false;
        },
        rolClass(slug) {
            const map = {
                super_admin: 'text-rose-700 bg-rose-50',
                administrador: 'text-blue-700 bg-blue-50',
                gerente: 'text-purple-700 bg-purple-50',
                lider_equipo: 'text-amber-700 bg-amber-50',
                empleado: 'text-slate-700 bg-slate-100',
            };
            return map[slug] || 'text-slate-500 bg-slate-50';
        },
        statusClass(status) {
            const map = {
                activo: 'text-emerald-700 bg-emerald-50',
                suspendido: 'text-amber-700 bg-amber-50',
                inactivo: 'text-slate-500 bg-slate-100',
            };
            return map[status] || 'text-slate-500 bg-slate-50';
        },
        renderStars(p) {
            p = Math.max(0, Math.min(5, +p || 0));
            const full = Math.floor(p);
            const half = (p - full) >= 0.25;
            const empty = 5 - full - (half ? 1 : 0);
            const path = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z';
            let stars = '<div class="flex items-center gap-0.5">';
            for (let i = 0; i < full; i++) stars += '<svg class="h-4 w-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="' + path + '" /></svg>';
            if (half) stars += '<svg class="h-4 w-4" viewBox="0 0 20 20"><defs><linearGradient id="hs' + p + '"><stop offset="50%" stop-color="#fbbf24" /><stop offset="50%" stop-color="#cbd5e1" /></linearGradient></defs><path d="' + path + '" fill="url(#hs' + p + ')" /></svg>';
            for (let i = 0; i < empty; i++) stars += '<svg class="h-4 w-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="' + path + '" /></svg>';
            stars += '</div>';
            return stars;
        }
    }));
});
</script>
</body>
</html>
