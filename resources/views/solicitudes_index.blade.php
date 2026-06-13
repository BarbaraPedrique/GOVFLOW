<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes y Registros - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

@include('partials.sidebar')

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Solicitudes y Registros</h2></div>
        <div class="flex items-center gap-6">
            @include('partials.break-buttons')
            @include('partials.notification-bell')
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->apodo ?? Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p>
                    </div>
                    <img src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                    <svg class="h-4 w-4 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                </div>
                @include('partials.user-dropdown')
            </div>
        </div>
    </header>

    <main class="flex-1 p-10 mt-16 max-w-[1000px] w-full mx-auto space-y-8" x-data="{ tab: '{{ $registrosPendientes->isNotEmpty() || $pendientes->isNotEmpty() ? ($registrosPendientes->isNotEmpty() ? 'registros' : 'solicitudes') : 'solicitudes' }}', rechazarConSugerencia: null }">

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('error') }}
            </div>
        @endif

        @if(isset($revisionesFlujo) && $revisionesFlujo->count() > 0)
            <div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-amber-100 bg-amber-50/30">
                    <h3 class="text-sm font-bold text-amber-700">Revisiones de flujo pendientes</h3>
                    <p class="text-xs text-amber-500 mt-0.5">Tienes {{ $revisionesFlujo->count() }} paso(s) pendiente(s) de revisión</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($revisionesFlujo as $rev)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700">{{ $rev->paso_nombre }}</p>
                                <p class="text-xs text-slate-500">Flujo: {{ $rev->ejecucion?->flujoTrabajo?->nombre ?? '—' }}</p>
                                @if ($rev->mensaje)
                                    <p class="text-xs text-slate-400 mt-0.5 italic">"{{ $rev->mensaje }}"</p>
                                @endif
                                @if ($rev->archivo)
                                    <a href="{{ asset('storage/' . $rev->archivo) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 underline mt-0.5">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        {{ basename($rev->archivo) }}
                                    </a>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-4">
                                <form action="{{ route('flujos.paso.revisar', $rev) }}" method="POST" class="inline" onsubmit="event.preventDefault(); fetch(this.action,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({accion:'aprobar',comentario:''})}).then(r=>r.json()).then(d=>{if(d.success)location.reload();else alert(d.message)});">
                                    @csrf
                                    <input type="hidden" name="accion" value="aprobar">
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">Aprobar</button>
                                </form>
                                <form action="{{ route('flujos.paso.revisar', $rev) }}" method="POST" class="inline" onsubmit="event.preventDefault(); const c=this.querySelector('[name=comentario]').value; fetch(this.action,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({accion:'rechazar',comentario:c})}).then(r=>r.json()).then(d=>{if(d.success)location.reload();else alert(d.message)});">
                                    @csrf
                                    <input type="hidden" name="accion" value="rechazar">
                                    <input type="text" name="comentario" placeholder="Motivo..." class="w-28 text-xs rounded-lg border border-slate-200 px-2 py-1">
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 rounded-lg hover:bg-rose-100 transition-colors">Rechazar</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tabs -->
        @if($registrosPendientes->isNotEmpty() || $registrosHistorial->isNotEmpty())
        <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
            <button @click="tab = 'registros'" :class="tab === 'registros' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2 text-sm font-semibold rounded-lg transition-all">
                Registros
                @if($registrosPendientes->count() > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-rose-500 rounded-full ml-1">{{ $registrosPendientes->count() }}</span>
                @endif
            </button>
            <button @click="tab = 'solicitudes'" :class="tab === 'solicitudes' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2 text-sm font-semibold rounded-lg transition-all">
                Solicitudes
                @if($pendientes->count() > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-rose-500 rounded-full ml-1">{{ $pendientes->count() }}</span>
                @endif
            </button>
        </div>
        @endif

        <!-- Tab: Registros -->
        <div x-show="tab === 'registros'" x-cloak class="space-y-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-700">Registros pendientes</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $registrosPendientes->count() }} solicitud(es) de registro pendiente(s)</p>
                </div>

                @if($registrosPendientes->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="h-16 w-16 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        <h3 class="text-lg font-semibold text-slate-600 mb-1">Sin registros pendientes</h3>
                        <p class="text-sm text-slate-400">Cuando alguien se registre, su solicitud aparecerá aquí.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($registrosPendientes as $solicitud)
                            <div class="px-6 py-5 flex items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-800">{{ $solicitud->name }}</h4>
                                        <p class="text-xs text-slate-400">{{ $solicitud->email }}</p>
                                        <p class="text-xs text-slate-500 mt-1">Rol solicitado: <span class="font-medium text-amber-600">{{ $solicitud->role?->display_name ?? 'Sin rol' }}</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <form action="{{ route('admin.solicitudes.aprobar', $solicitud) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-semibold hover:bg-emerald-100 transition-colors flex items-center gap-1.5">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Aprobar
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.solicitudes.rechazar', $solicitud) }}" method="POST" onsubmit="return confirm('¿Rechazar y eliminar esta solicitud?')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100 transition-colors flex items-center gap-1.5">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($registrosHistorial->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-700">Historial de registros</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $registrosHistorial->count() }} registro(s) procesado(s)</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($registrosHistorial as $t)
                        @php
                            preg_match('/user_id:(\d+)/', $t->descripcion, $uid);
                            $userId = $uid[1] ?? null;
                            $userName = '—';
                            $userEmail = '—';
                            $rolName = '—';
                            if ($userId) {
                                $userData = \App\Models\Tarea::where('descripcion', 'like', "user_id:{$userId}|%")->first();
                                if ($userData) {
                                    preg_match('/rol:(.+)/', $userData->descripcion, $rol);
                                    $rolName = $rol[1] ?? '—';
                                }
                                $userModel = \App\Models\User::find($userId);
                                if ($userModel) {
                                    $userName = $userModel->name;
                                    $userEmail = $userModel->email;
                                } else {
                                    $userName = 'Eliminado';
                                }
                            }
                            $badgeClass = $t->status === 'aprobado' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700';
                            $badgeIcon = $t->status === 'aprobado' ? '✓' : '✗';
                        @endphp
                        <div class="px-6 py-4 flex items-start justify-between gap-4 hover:bg-slate-50 transition-colors opacity-80">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeClass }}">
                                        {{ $badgeIcon }} {{ ucfirst($t->status) }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $t->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-800">{{ $userName }}</h4>
                                <p class="text-xs text-slate-500">{{ $userEmail }} — Rol: {{ $rolName }}</p>
                            </div>
                            <div class="text-xs text-slate-400 shrink-0">
                                {{ $t->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Tab: Solicitudes -->
        <div x-show="tab === 'solicitudes'" x-cloak class="space-y-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-700">Solicitudes pendientes</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tienes {{ $pendientes->count() }} solicitud(es) pendiente(s)</p>
                </div>

                @if($pendientes->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="h-16 w-16 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <h3 class="text-lg font-semibold text-slate-600 mb-1">Sin solicitudes pendientes</h3>
                        <p class="text-sm text-slate-400">No tienes solicitudes por revisar en este momento.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($pendientes as $s)
                            @php
                                $tipoEtiqueta = '';
                                $solicitante = '';
                                $equipoNombre = '';
                                $esRegistro = str_contains($s->descripcion ?? '', 'user_id:');
                                $partes = explode('---', $s->descripcion);
                                $metadata = end($partes);
                                if (preg_match('/Tipo: (.+)/', $metadata, $m)) {
                                    $tipoEtiqueta = trim($m[1]);
                                }
                                if (preg_match('/Solicitante: (.+?) \(/', $metadata, $m)) {
                                    $solicitante = trim($m[1]);
                                }
                                if (preg_match('/Equipo: (.+)/', $metadata, $m)) {
                                    $equipoNombre = trim($m[1]);
                                }
                                $esUnirseEquipo = str_contains($tipoEtiqueta, 'Unirse');
                                $tipoClass = match (true) {
                                    $esRegistro => 'bg-amber-50 text-amber-700',
                                    $esUnirseEquipo => 'bg-blue-50 text-blue-700',
                                    str_contains($tipoEtiqueta, 'Cambio') => 'bg-purple-50 text-purple-700',
                                    str_contains($tipoEtiqueta, 'Revisión') && str_contains($tipoEtiqueta, 'tareas') => 'bg-amber-50 text-amber-700',
                                    str_contains($tipoEtiqueta, 'Revisión') && str_contains($tipoEtiqueta, 'web') => 'bg-cyan-50 text-cyan-700',
                                    str_contains($tipoEtiqueta, 'Reportar') => 'bg-rose-50 text-rose-700',
                                    default => 'bg-slate-50 text-slate-600'
                                };
                            @endphp
                            @if(!$esRegistro)
                            <div class="px-6 py-5 flex items-start justify-between gap-4 hover:bg-slate-50 transition-colors">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full {{ $tipoClass }}">
                                            {{ $tipoEtiqueta ?: 'Solicitud' }}
                                        </span>
                                        <span class="text-xs text-slate-400">{{ $s->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <h4 class="text-sm font-semibold text-slate-800">{{ $s->titulo }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $s->descripcion }}</p>
                                    <div class="flex items-center gap-3 mt-2 text-xs text-slate-400">
                                        <span>Solicitante: <strong class="text-slate-600">{{ $solicitante ?: 'Desconocido' }}</strong></span>
                                        @if ($equipoNombre)
                                            <span>•</span>
                                            <span>Equipo: <strong class="text-slate-600">{{ $equipoNombre }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <form action="{{ route('solicitudes.aprobar', $s) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition-colors flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Aprobar
                                        </button>
                                    </form>
                                    @if ($esUnirseEquipo)
                                        <button @click="rechazarConSugerencia = {{ $s->id }}" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition-colors flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                            Rechazar
                                        </button>
                                    @else
                                        <form action="{{ route('solicitudes.rechazar', $s) }}" method="POST" onsubmit="return confirm('¿Rechazar esta solicitud?')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition-colors flex items-center gap-1">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                                Rechazar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            @if($historial->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-700">Historial de solicitudes</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $historial->count() }} solicitud(es) procesada(s)</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($historial as $s)
                        @php
                            $tipoEtiqueta = '';
                            $solicitante = '';
                            $equipoNombre = '';
                            $esRegistro = str_contains($s->descripcion ?? '', 'user_id:');
                            $partes = explode('---', $s->descripcion);
                            $metadata = end($partes);
                            if (preg_match('/Tipo: (.+)/', $metadata, $m)) {
                                $tipoEtiqueta = trim($m[1]);
                            }
                            if (preg_match('/Solicitante: (.+?) \(/', $metadata, $m)) {
                                $solicitante = trim($m[1]);
                            }
                            if (preg_match('/Equipo: (.+)/', $metadata, $m)) {
                                $equipoNombre = trim($m[1]);
                            }
                            $tipoClass = match (true) {
                                $esRegistro => 'bg-amber-50 text-amber-700',
                                str_contains($tipoEtiqueta, 'Unirse') => 'bg-blue-50 text-blue-700',
                                str_contains($tipoEtiqueta, 'Cambio') => 'bg-purple-50 text-purple-700',
                                str_contains($tipoEtiqueta, 'Revisión') && str_contains($tipoEtiqueta, 'tareas') => 'bg-amber-50 text-amber-700',
                                str_contains($tipoEtiqueta, 'Revisión') && str_contains($tipoEtiqueta, 'web') => 'bg-cyan-50 text-cyan-700',
                                str_contains($tipoEtiqueta, 'Reportar') => 'bg-rose-50 text-rose-700',
                                default => 'bg-slate-50 text-slate-600'
                            };
                            $badgeClass = $s->status === 'aprobado' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700';
                            $badgeIcon = $s->status === 'aprobado' ? '✓' : '✗';
                        @endphp
                        @if(!$esRegistro)
                        <div class="px-6 py-4 flex items-start justify-between gap-4 hover:bg-slate-50 transition-colors opacity-80">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full {{ $tipoClass }}">
                                        {{ $tipoEtiqueta ?: 'Solicitud' }}
                                    </span>
                                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeClass }}">
                                        {{ $badgeIcon }} {{ ucfirst($s->status) }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $s->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-800">{{ $s->titulo }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $s->descripcion }}</p>
                                <div class="flex items-center gap-3 mt-1 text-xs text-slate-400">
                                    <span>Solicitante: <strong class="text-slate-600">{{ $solicitante ?: 'Desconocido' }}</strong></span>
                                    @if ($equipoNombre)
                                        <span>•</span>
                                        <span>Equipo: <strong class="text-slate-600">{{ $equipoNombre }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-xs text-slate-400 shrink-0">
                                {{ $s->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Modal rechazar con sugerencia -->
        <div x-show="rechazarConSugerencia" x-cloak class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
             @click.self="rechazarConSugerencia = null">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-xl bg-red-50 flex items-center justify-center">
                            <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Rechazar solicitud de equipo</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Opcionalmente puedes sugerir un equipo alternativo</p>
                        </div>
                    </div>
                    <form method="POST" x-bind:action="'{{ url('/solicitudes') }}/' + rechazarConSugerencia + '/rechazar'">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Sugerir equipo alternativo (opcional)</label>
                            <select name="equipo_sugerido_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#007BFF]/20 focus:border-[#007BFF] outline-none">
                                <option value="">No sugerir equipo</option>
                                @foreach($equiposDisponibles as $eq)
                                    <option value="{{ $eq->id }}">{{ $eq->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="rechazarConSugerencia = null" class="px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-600 text-sm font-semibold text-white hover:bg-red-700 transition-colors flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                Rechazar solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @include('partials.solicitar-modal')
    </main>
</div>

</body>
</html>