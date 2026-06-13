<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

@include('partials.sidebar')

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Horario Semanal</h2></div>
        <div class="flex items-center gap-6">
            @include('partials.break-buttons')
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

    <main class="flex-1 p-6 mt-16 max-w-[1400px] w-full mx-auto space-y-6" x-data="horarioApp()">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg p-4">{{ session('success') }}</div>
        @endif

        @php
            function horaAMPM($h): string {
                $ampm = $h >= 12 ? 'PM' : 'AM';
                $h12 = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                return $h12 . ':00 ' . $ampm;
            }
            function horaStrCorta($time): string {
                $parts = explode(':', $time);
                return $parts[0] . ':' . ($parts[1] ?? '00');
            }
            function textoColor($hex): string {
                $hex = ltrim($hex, '#');
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $luminancia = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
                return $luminancia > 0.55 ? 'text-slate-900' : 'text-white';
            }
        @endphp

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <div class="xl:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead><tr class="bg-slate-50">
                            <th class="w-16 px-1 py-2 text-[10px] font-semibold text-slate-400 text-right"></th>
                            @foreach ($horarioPorDia as $dia)
                                <th class="px-1 py-2 text-[11px] font-semibold text-slate-600 text-center min-w-[100px]">{{ $dia['nombre'] }}</th>
                            @endforeach
                        </tr></thead>
                        <tbody>
                            @for ($h = 6; $h <= 22; $h++)
                                <tr class="border-t border-slate-100">
                                    <td class="text-[10px] text-slate-400 font-medium text-right pr-2 py-1 w-16 align-top pt-2.5">{{ horaAMPM($h) }}</td>
                                    @foreach ($horarioPorDia as $d => $dia)
                                        @php
                                            $bloquesEnHora = $dia['bloques']->filter(function($b) use ($h) {
                                                $inicio = (int) substr($b->hora_inicio, 0, 2);
                                                $fin = $b->hora_fin ? (int) substr($b->hora_fin, 0, 2) : $inicio + 1;
                                                return $inicio <= $h && $fin >= $h;
                                            });
                                            $esInicio = fn($b) => (int) substr($b->hora_inicio, 0, 2) === $h;
                                        @endphp
                                        <td class="relative border-l border-slate-50 h-10 px-0 align-top overflow-hidden"
                                            @drop.prevent="dropBlock($event, {{ $d }}, '{{ sprintf('%02d:00', $h) }}')"
                                            @dragover.prevent
                                            x-data="{ hora: '{{ sprintf('%02d:00', $h) }}', dia: {{ $d }} }">
                                            @php
                                                $contadorBloques = $bloquesEnHora->count();
                                            @endphp
                                            @foreach ($bloquesEnHora as $i => $bloque)
                                                @php $esInicioBloque = $esInicio($bloque); @endphp
                                                @if ($esInicioBloque)
                                                    @if (!empty($bloque->es_tarea))
                                                        <div @click="editTarea({{ $bloque->id }})"
                                                             class="absolute inset-x-0 rounded-sm mx-px {{ textoColor($bloque->color ?? '#3B82F6') }} text-[9px] font-semibold cursor-pointer hover:brightness-110 transition-all flex flex-col justify-center px-1.5"
                                                             style="background-color: {{ $bloque->color ?? '#3B82F6' }}; top: 1px; bottom: 1px; z-index: {{ $contadorBloques - $i }};"
                                                             title="{{ $bloque->titulo }}">
                                                            <span class="truncate leading-tight">{{ $bloque->titulo }}</span>
                                                            <span class="opacity-80 leading-tight text-[8px]">{{ horaStrCorta($bloque->hora_inicio) }} - {{ horaStrCorta($bloque->hora_fin) }}</span>
                                                        </div>
                                                    @else
                                                        <div draggable="true"
                                                             @dragstart="dragId={{ $bloque->id }}"
                                                             @click="openEdit({{ $bloque->id }}, '{{ addslashes($bloque->titulo) }}', '{{ $bloque->color ?? '#3B82F6' }}', '{{ $bloque->hora_inicio }}', '{{ $bloque->hora_fin }}', '{{ $bloque->dia_semana }}', '{{ addslashes($bloque->ubicacion ?? '') }}')"
                                                             class="absolute inset-x-0 rounded-sm mx-px {{ textoColor($bloque->color ?? '#3B82F6') }} text-[9px] font-semibold cursor-grab active:cursor-grabbing hover:brightness-110 transition-all flex flex-col justify-center px-1.5"
                                                             style="background-color: {{ $bloque->color ?? '#3B82F6' }}; top: 1px; bottom: 1px; z-index: {{ $contadorBloques - $i }};"
                                                             title="{{ $bloque->titulo }}">
                                                            <span class="truncate leading-tight">{{ $bloque->titulo }}</span>
                                                            <span class="opacity-80 leading-tight text-[8px]">{{ horaStrCorta($bloque->hora_inicio) }} - {{ horaStrCorta($bloque->hora_fin) }}</span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="absolute inset-x-0 rounded-sm mx-px"
                                                         style="background-color: {{ $bloque->color ?? '#3B82F6' }}; top: 1px; bottom: 1px; opacity: 0.3; z-index: 0;"
                                                         title="{{ $bloque->titulo }} (continúa)"></div>
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
                <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                    <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4" /></svg>
                    Agregar Bloque
                </h3>
                <form method="POST" action="{{ route('horarios.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Día</label>
                        <select name="dia_semana" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                            @foreach ($horarioPorDia as $i => $dia)
                                <option value="{{ $i }}">{{ $dia['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Inicio</label>
                            <input type="time" name="hora_inicio" required value="08:00" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Fin</label>
                            <input type="time" name="hora_fin" required value="09:00" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Título</label>
                        <input type="text" name="titulo" required placeholder="Ej: Reunión de equipo" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Color</label>
                            <input type="color" name="color" value="#3B82F6" class="w-full h-10 rounded-xl border border-slate-200 cursor-pointer bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Ubicación</label>
                            <input type="text" name="ubicacion" placeholder="Oficina / Sala" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4" /></svg>
                        Agregar
                    </button>
                </form>
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-[10px] text-slate-400 leading-relaxed">
                        <span class="block font-medium text-slate-500 mb-1">¿Cómo usar?</span>
                        Arrastra un bloque azul a otra celda para cambiar su hora.<br>
                        Haz clic en un bloque para eliminarlo.
                    </p>
                </div>
            </div>
        </div>
    <!-- Edit Modal -->
    <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50" @click="editModal = false">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold text-slate-800 mb-4">Editar Bloque</h3>
            <form @submit.prevent="guardarEdicion" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Título</label>
                    <input type="text" x-model="editTitulo" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Inicio</label>
                        <div class="flex gap-1 items-center">
                            <select x-model="editHoraInicioH" class="px-2 py-2 rounded-lg border border-slate-200 text-sm bg-white w-14">
                                @for ($h = 1; $h <= 12; $h++)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endfor
                            </select>
                            <span class="text-slate-400">:</span>
                            <select x-model="editHoraInicioM" class="px-2 py-2 rounded-lg border border-slate-200 text-sm bg-white w-14">
                                <option value="00">00</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="45">45</option>
                            </select>
                            <select x-model="editHoraInicioP" class="px-2 py-2 rounded-lg border border-slate-200 text-sm bg-white w-16">
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Fin</label>
                        <div class="flex gap-1 items-center">
                            <select x-model="editHoraFinH" class="px-2 py-2 rounded-lg border border-slate-200 text-sm bg-white w-14">
                                @for ($h = 1; $h <= 12; $h++)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endfor
                            </select>
                            <span class="text-slate-400">:</span>
                            <select x-model="editHoraFinM" class="px-2 py-2 rounded-lg border border-slate-200 text-sm bg-white w-14">
                                <option value="00">00</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="45">45</option>
                            </select>
                            <select x-model="editHoraFinP" class="px-2 py-2 rounded-lg border border-slate-200 text-sm bg-white w-16">
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Color</label>
                        <input type="color" x-model="editColor" class="w-full h-10 rounded-xl border border-slate-200 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Ubicación</label>
                        <input type="text" x-model="editUbicacion" placeholder="Sala / Oficina" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Día</label>
                        <select x-model="editDia" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                            @foreach ($horarioPorDia as $i => $dia)
                                <option value="{{ $i }}">{{ $dia['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" @click="eliminarBloque()" class="w-full py-2.5 rounded-xl bg-rose-100 text-rose-700 text-sm font-semibold hover:bg-rose-200 transition-colors">Eliminar</button>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700">Guardar Cambios</button>
                </div>
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
            editModal: false,
            editId: null,
            editTitulo: '',
            editColor: '#3B82F6',
            editHoraInicioH: '8',
            editHoraInicioM: '00',
            editHoraInicioP: 'AM',
            editHoraFinH: '9',
            editHoraFinM: '00',
            editHoraFinP: 'AM',
            editDia: '0',
            editUbicacion: '',

            openEdit(id, titulo, color, horaInicio, horaFin, dia, ubicacion) {
                const hi = this.to12(horaInicio);
                const hf = this.to12(horaFin);
                this.editId = id;
                this.editTitulo = titulo;
                this.editColor = color;
                this.editHoraInicioH = hi.h;
                this.editHoraInicioM = hi.m;
                this.editHoraInicioP = hi.p;
                this.editHoraFinH = hf.h;
                this.editHoraFinM = hf.m;
                this.editHoraFinP = hf.p;
                this.editDia = dia;
                this.editUbicacion = ubicacion;
                this.editModal = true;
            },

            to24(h, m, p) {
                if (!h || !m) return null;
                let h24 = parseInt(h);
                if (p === 'PM' && h24 !== 12) h24 += 12;
                if (p === 'AM' && h24 === 12) h24 = 0;
                return String(h24).padStart(2, '0') + ':' + m;
            },
            to12(hora) {
                if (!hora) return { h: '', m: '00', p: 'AM' };
                const parts = hora.split(':');
                let hInt = parseInt(parts[0]);
                const p = hInt >= 12 ? 'PM' : 'AM';
                hInt = hInt % 12 || 12;
                return { h: String(hInt), m: parts[1] || '00', p };
            },

            guardarEdicion() {
                const hi = this.to24(this.editHoraInicioH, this.editHoraInicioM, this.editHoraInicioP);
                const hf = this.to24(this.editHoraFinH, this.editHoraFinM, this.editHoraFinP);
                const form = new FormData();
                form.append('titulo', this.editTitulo);
                form.append('color', this.editColor);
                form.append('hora_inicio', hi);
                form.append('hora_fin', hf);
                form.append('dia_semana', this.editDia);
                form.append('ubicacion', this.editUbicacion);
                form.append('_method', 'PUT');
                fetch(`{{ url('/horarios') }}/${this.editId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: form
                }).then(r => r.json()).then(d => {
                    if (d.success) { this.editModal = false; window.location.reload(); }
                });
            },

            eliminarBloque() {
                if (confirm('¿Eliminar este bloque del horario?')) {
                    fetch(`{{ url('/horarios') }}/${this.editId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(r => r.json()).then(d => { if (d.success) { this.editModal = false; window.location.reload(); } });
                }
            },

            editTarea(id) {
                alert('Edita esta tarea desde la página de Tareas.');
                window.location = '{{ route('tareas.index') }}';
            },

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
        };
    }
</script>
</body>
</html>