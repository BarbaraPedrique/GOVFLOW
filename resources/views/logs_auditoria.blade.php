<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Auditoría - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

@include('partials.sidebar')

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Registro de Auditoría</h2></div>
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

    <main class="flex-1 p-10 mt-16 max-w-[1400px] w-full mx-auto space-y-6">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <form method="GET" action="{{ route('logs.auditoria') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Acción</label>
                    <select name="accion" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="">Todas</option>
                        @foreach ($acciones as $a)
                            <option value="{{ $a }}" @selected(request('accion') === $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Entidad</label>
                    <select name="entidad" class="px-4 py-2 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="">Todas</option>
                        @foreach ($entidades as $e)
                            <option value="{{ $e }}" @selected(request('entidad') === $e)>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#007BFF] text-sm font-semibold text-white hover:bg-blue-600">Filtrar</button>
                <a href="{{ route('logs.auditoria') }}" class="px-5 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
                <div class="text-xs text-slate-400 ml-auto">Total: {{ $logs->total() }} registros</div>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <table class="w-full text-[11px]">
                <thead>
                    <tr class="bg-slate-50 text-left text-[10px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-2 py-2.5 w-[90px]">Fecha</th>
                        <th class="px-2 py-2.5 w-[110px]">Usuario</th>
                        <th class="px-2 py-2.5 w-[65px]">Acción</th>
                        <th class="px-2 py-2.5 w-1/4">Entidad</th>
                        <th class="px-2 py-2.5 w-2/5">Descripción</th>
                        <th class="px-2 py-2.5 w-[40px]">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-2 py-2.5 text-slate-500 whitespace-nowrap">{{ $log->created_at->isoFormat('DD/MM/YY HH:mm') }}</td>
                            <td class="px-2 py-2.5 text-slate-800 truncate max-w-[110px]" title="{{ $log->user?->name ?? '—' }}">{{ $log->user?->name ?? '—' }}</td>
                            <td class="px-2 py-2.5">
                                <span class="inline-block px-1.5 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap
                                    @if(str_contains($log->accion, 'crear')) bg-blue-50 text-blue-700
                                    @elseif(str_contains($log->accion, 'completar')) bg-emerald-50 text-emerald-700
                                    @elseif(str_contains($log->accion, 'reabrir')) bg-amber-50 text-amber-700
                                    @elseif(str_contains($log->accion, 'eliminar')) bg-rose-50 text-rose-700
                                    @else bg-slate-50 text-slate-600
                                    @endif">
                                    {{ $log->accion }}
                                </span>
                            </td>
                            <td class="px-2 py-2.5 text-slate-500 truncate max-w-[200px]" title="{{ $log->entidad_type }} (ID: {{ $log->entidad_id ?? '—' }})">
                                {{ $log->entidad_type }} <span class="text-slate-400">#{{ $log->entidad_id ?? '—' }}</span>
                            </td>
                            <td class="px-2 py-2.5 text-slate-600 truncate max-w-[250px]" title="{{ $log->descripcion }}">{{ $log->descripcion }}</td>
                            @php
                                $entidadLegible = match(true) {
                                    str_contains($log->entidad_type, 'FlujoTrabajo') => 'Flujo de Trabajo',
                                    str_contains($log->entidad_type, 'Tarea') => 'Tarea',
                                    str_contains($log->entidad_type, 'User') => 'Usuario',
                                    str_contains($log->entidad_type, 'Equipo') => 'Equipo',
                                    str_contains($log->entidad_type, 'FlujoPaso') => 'Paso de Flujo',
                                    str_contains($log->entidad_type, 'FlujoEjecucion') => 'Ejecución de Flujo',
                                    str_contains($log->entidad_type, 'SolicitudCliente') => 'Solicitud',
                                    default => $log->entidad_type,
                                };
                                $accionLegible = match($log->accion) {
                                    'crear' => 'Creación',
                                    'actualizar', 'update' => 'Actualización',
                                    'eliminar', 'delete' => 'Eliminación',
                                    'completar' => 'Finalización',
                                    'reabrir' => 'Reapertura',
                                    'asignar' => 'Asignación',
                                    'rechazar' => 'Rechazo',
                                    'aprobar' => 'Aprobación',
                                    'iniciar' => 'Inicio',
                                    'publicar' => 'Publicación',
                                    'suspendido' => 'Suspensión',
                                    default => $log->accion,
                                };
                                $etiquetasCampos = [
                                    'nombre' => 'Nombre',
                                    'descripcion' => 'Descripción',
                                    'codigo' => 'Código',
                                    'estado' => 'Estado',
                                    'prioridad' => 'Prioridad',
                                    'rol' => 'Rol',
                                    'email' => 'Correo electrónico',
                                    'fecha_inicio' => 'Fecha de inicio',
                                    'fecha_fin' => 'Fecha de fin',
                                    'equipo_id' => 'Equipo',
                                    'checklist' => 'Lista de verificación',
                                    'titulo' => 'Título',
                                    'pasos' => 'Pasos del flujo',
                                    'diseno' => 'Diseño del flujo',
                                    'trigger_evento' => 'Evento disparador',
                                    'trigger_descripcion' => 'Descripción del disparador',
                                    'version' => 'Versión',
                                ];
                                $formatearValor = function($v) {
                                    if ($v === null || $v === '') return '—';
                                    if (in_array($v, ['activo','Activo'])) return 'Activo';
                                    if (in_array($v, ['inactivo','Inactivo'])) return 'Inactivo';
                                    if (in_array($v, ['pendiente','Pendiente'])) return 'Pendiente';
                                    if (in_array($v, ['completado','Completado'])) return 'Completado';
                                    if (in_array($v, ['en_progreso','en progreso','En progreso'])) return 'En progreso';
                                    if (in_array($v, ['rechazado','Rechazado'])) return 'Rechazado';
                                    if ($v === '1' || $v === 1) return 'Sí';
                                    if ($v === '0' || $v === 0) return 'No';
                                    return $v;
                                };
                            @endphp
                            <td class="px-2 py-2.5">
                                <button onclick="this.nextElementSibling.classList.remove('hidden')" class="text-[10px] font-semibold text-[#007BFF] hover:underline">Ver</button>
                                <div class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center p-4" onclick="event.target===this&&this.classList.add('hidden')">
                                    <div class="bg-white rounded-xl shadow-xl border border-slate-200 p-4 min-w-[260px] max-w-sm max-h-96 overflow-y-auto">
                                        <button onclick="this.closest('.fixed').classList.add('hidden')" class="float-right text-slate-400 hover:text-slate-600 text-sm leading-none">&times;</button>
                                        <div class="space-y-3 mt-1">
                                            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap
                                                    @if(str_contains($log->accion, 'crear')) bg-blue-50 text-blue-700
                                                    @elseif(str_contains($log->accion, 'completar')) bg-emerald-50 text-emerald-700
                                                    @elseif(str_contains($log->accion, 'reabrir')) bg-amber-50 text-amber-700
                                                    @elseif(str_contains($log->accion, 'eliminar')) bg-rose-50 text-rose-700
                                                    @else bg-slate-50 text-slate-600
                                                    @endif">
                                                    {{ $accionLegible }}
                                                </span>
                                                <span class="text-[11px] text-slate-400">{{ $log->created_at->isoFormat('DD/MM/YY HH:mm') }}</span>
                                            </div>
                                            <div class="text-xs space-y-2">
                                                <p><span class="font-semibold text-slate-600">Realizado por:</span> <span class="text-slate-700">{{ $log->user?->name ?? 'Sistema' }}</span></p>
                                                <p><span class="font-semibold text-slate-600">Tipo de registro:</span> <span class="text-slate-700">{{ $entidadLegible }}</span></p>
                                                <p><span class="font-semibold text-slate-600">Descripción:</span> <span class="text-slate-700">{{ $log->descripcion }}</span></p>
                                            </div>
                                            @if($log->metadata && is_array($log->metadata) && count($log->metadata) > 0)
                                                <div class="pt-2 border-t border-slate-100">
                                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Cambios realizados</p>
                                                    <div class="space-y-1.5">
                                                        @foreach($log->metadata as $campo => $valor)
                                                            @php $etiqueta = $etiquetasCampos[$campo] ?? ucfirst(str_replace('_', ' ', $campo)); @endphp
                                                            @if(is_array($valor) && isset($valor['old']) && isset($valor['new']))
                                                                <div class="text-[11px]">
                                                                    <span class="font-semibold text-slate-600">{{ $etiqueta }}</span>
                                                                    <div class="flex items-center gap-1 flex-wrap mt-0.5">
                                                                        <span class="line-through text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded">{{ $formatearValor($valor['old'] ?? '') }}</span>
                                                                        <svg class="h-2.5 w-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                                                        <span class="text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded">{{ $formatearValor($valor['new'] ?? '') }}</span>
                                                                    </div>
                                                                </div>
                                                            @elseif(is_string($campo) && is_string($valor))
                                                                <div class="text-[11px] text-slate-500">
                                                                    <span class="font-semibold text-slate-600">{{ $etiqueta }}:</span>
                                                                    <span class="ml-1">{{ $formatearValor($valor) }}</span>
                                                                </div>
                                                            @elseif(is_string($campo) && is_array($valor))
                                                                @php
                                                                    $totalItems = count($valor);
                                                                    $esNumerico = array_keys($valor) === range(0, $totalItems - 1);
                                                                @endphp
                                                                <div class="text-[11px]">
                                                                    <span class="font-semibold text-slate-600 block mb-0.5">{{ $etiqueta }}</span>
                                                                    @if($campo === 'pasos')
                                                                        <span class="text-slate-500">{{ $totalItems }} paso(s) definido(s)</span>
                                                                        @if($totalItems > 0)
                                                                            <ul class="mt-1 space-y-0.5">
                                                                                @foreach($valor as $p)
                                                                                    <li class="text-slate-500 flex items-center gap-1">
                                                                                        <svg class="h-2.5 w-2.5 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 5l7 7-7 7" /></svg>
                                                                                        {{ $p['nombre'] ?? 'Paso sin nombre' }}
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @endif
                                                                    @elseif($campo === 'diseno')
                                                                        <div class="space-y-0.5 mt-0.5">
                                                                            @php $disenoLabels = ['version' => 'Versión', 'trigger_evento' => 'Evento disparador', 'trigger_descripcion' => 'Descripción', 'publicado' => 'Publicado']; @endphp
                                                                            @foreach($disenoLabels as $dk => $dl)
                                                                                @if(isset($valor[$dk]) && $valor[$dk] !== '')
                                                                                    <div class="text-slate-500">
                                                                                        <span class="font-medium text-slate-600">{{ $dl }}:</span>
                                                                                        <span class="ml-0.5">{{ $formatearValor($valor[$dk]) }}</span>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    @elseif($esNumerico)
                                                                        <span class="text-slate-400">{{ $totalItems }} elemento(s)</span>
                                                                    @else
                                                                        <span class="text-slate-400">{{ $totalItems }} campo(s)</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            @if($log->ip_address || $log->user_agent)
                                                <div class="pt-2 border-t border-slate-100">
                                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Información técnica</p>
                                                    <div class="text-[10px] text-slate-400 space-y-0.5">
                                                        @if($log->ip_address)
                                                            <p><span class="font-medium">IP:</span> {{ $log->ip_address }}</p>
                                                        @endif
                                                        @if($log->user_agent)
                                                            <p><span class="font-medium">Navegador:</span> <span class="break-all">{{ $log->user_agent }}</span></p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-2 py-12 text-center text-sm text-slate-400">No hay registros de auditoría.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($logs->hasPages())
                <div class="px-3 py-3 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    @include('partials.solicitar-modal')
    </main>
</div>
</body>
</html>
