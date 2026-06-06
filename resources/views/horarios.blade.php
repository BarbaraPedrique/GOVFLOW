<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } .hora-columna { @apply text-[10px] text-slate-400 font-medium text-right pr-2 py-1 w-12 flex-shrink-0; } .bloque-horario { @apply rounded-md px-1.5 py-1 text-[10px] font-medium cursor-grab active:cursor-grabbing hover:shadow-sm transition-all; }</style>
</head>
<body class="bg-slate-50 flex min-h-screen">

@include('partials.sidebar')

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Horario Semanal</h2></div>
        <div class="flex items-center gap-6">
            @include('partials.notification-bell')
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->apodo ?? Auth::user()->name }}</p><p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p></div>
                    <img src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                    <svg class="h-4 w-4 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                </div>
                @include('partials.user-dropdown')
            </div>
        </div>
    </header>

    <main class="flex-1 p-6 mt-16 max-w-[1200px] w-full mx-auto space-y-6">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg p-4">{{ session('success') }}</div>
        @endif

        @php
            function horaAMPM($h): string {
                $ampm = $h >= 12 ? 'PM' : 'AM';
                $h12 = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                return $h12 . ':00 ' . $ampm;
            }
            function horaStrAMPM($time): string {
                $parts = explode(':', $time);
                $h = (int) $parts[0];
                $m = $parts[1] ?? '00';
                $ampm = $h >= 12 ? 'PM' : 'AM';
                $h12 = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                return $h12 . ':' . $m . ' ' . $ampm;
            }
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse" x-data="horarioApp()">
                        <thead><tr class="bg-slate-50">
                            <th class="w-12 px-1 py-2 text-[10px] font-semibold text-slate-400 text-right"></th>
                            @foreach ($horarioPorDia as $dia)
                                <th class="px-1 py-2 text-[11px] font-semibold text-slate-600 text-center min-w-[90px]">{{ $dia['nombre'] }}</th>
                            @endforeach
                        </tr></thead>
                        <tbody>
                            @for ($h = 6; $h <= 22; $h++)
                                <tr class="border-t border-slate-100">
                                    <td class="hora-columna">{{ horaAMPM($h) }}</td>
                                    @foreach ($horarioPorDia as $d => $dia)
                                        <td class="relative border-l border-slate-50 h-9 px-1 align-top"
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
                                                    <div class="bloque-horario text-white mb-0.5 opacity-80 cursor-default"
                                                         style="background-color: {{ $bloque->color ?? '#007BFF' }}"
                                                         title="Tarea: {{ $bloque->titulo }}">
                                                        <div class="font-semibold truncate leading-tight">{{ $bloque->titulo }}</div>
                                                        <div class="opacity-80 leading-tight">{{ horaStrAMPM($bloque->hora_inicio) }} - {{ horaStrAMPM($bloque->hora_fin) }}</div>
                                                    </div>
                                                @else
                                                    <div draggable="true"
                                                         @dragstart="dragId={{ $bloque->id }}"
                                                         class="bloque-horario text-white mb-0.5"
                                                         style="background-color: {{ $bloque->color ?? '#007BFF' }}"
                                                         @click="editBlock({{ $bloque->id }})">
                                                        <div class="font-semibold truncate leading-tight">{{ $bloque->titulo }}</div>
                                                        <div class="opacity-80 leading-tight">{{ horaStrAMPM($bloque->hora_inicio) }} - {{ horaStrAMPM($bloque->hora_fin) }}</div>
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
    @include('partials.solicitar-modal')
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
