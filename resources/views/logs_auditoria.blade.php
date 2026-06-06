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

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Fecha/Hora</th>
                            <th class="px-6 py-4">Usuario</th>
                            <th class="px-6 py-4">Acción</th>
                            <th class="px-6 py-4">Entidad</th>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Descripción</th>
                            <th class="px-6 py-4">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-xs">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3.5 text-slate-500 whitespace-nowrap">{{ $log->created_at->isoFormat('DD/MM/YY HH:mm') }}</td>
                                <td class="px-6 py-3.5 text-slate-800">{{ $log->user?->name ?? '—' }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                        @if(str_contains($log->accion, 'crear')) bg-blue-50 text-blue-700
                                        @elseif(str_contains($log->accion, 'completar')) bg-emerald-50 text-emerald-700
                                        @elseif(str_contains($log->accion, 'reabrir')) bg-amber-50 text-amber-700
                                        @elseif(str_contains($log->accion, 'eliminar')) bg-rose-50 text-rose-700
                                        @else bg-slate-50 text-slate-600
                                        @endif">
                                        {{ $log->accion }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-slate-500">{{ $log->entidad_type }}</td>
                                <td class="px-6 py-3.5 text-slate-500">{{ $log->entidad_id ?? '—' }}</td>
                                <td class="px-6 py-3.5 text-slate-600 max-w-xs truncate" title="{{ $log->descripcion }}">{{ $log->descripcion }}</td>
                                <td class="px-6 py-3.5 text-slate-400">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400">No hay registros de auditoría.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    @include('partials.solicitar-modal')
    </main>
</div>
</body>
</html>
