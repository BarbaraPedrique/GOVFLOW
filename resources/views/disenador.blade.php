<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diseñador de Flujos - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } .paso { @apply bg-white border-2 border-slate-200 rounded-xl p-4 shadow-sm cursor-grab active:cursor-grabbing transition-all; } .paso:hover { @apply border-[#007BFF] shadow-md; } .paso-conector { @apply flex items-center justify-center; } .paso-conector::after { content: ''; @apply block w-0.5 h-8 bg-slate-300; }</style>
</head>
<body class="bg-slate-50 flex min-h-screen">

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-20">
    <div class="h-16 border-b border-slate-100 flex items-center px-6">
        <img src="{{ asset('imagenes/logo2.png') }}" alt="Logo GOVFLOW" class="h-8 w-auto object-contain">
        <span class="ml-3 text-lg font-bold text-slate-800">GOVFLOW</span>
    </div>
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
        <a href="{{ route('inicio') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V16zM14 14a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg> Inicio</a>
        <a href="{{ route('tareas.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg> Tareas</a>
        <a href="{{ route('horarios.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> Horarios</a>
        <a href="{{ route('flujos') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg> Flujos de Trabajo</a>
        <a href="{{ route('auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg> Auditoría</a>
        <a href="{{ route('logs.auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> Logs Auditoría</a>
        <a href="{{ route('disenador') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#007BFF] bg-blue-50 rounded-xl transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" /></svg> Diseñador</a>
    </nav>
</aside>

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Diseñador de Flujos</h2></div>
        <div class="flex items-center gap-6">
            @include('partials.notification-bell')
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->name }}</p><p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p></div>
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

        <div x-data="disenadorApp()" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 h-fit">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Flujo de Trabajo</h3>
                <select x-model="flujoId" @change="cargarPasos" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm mb-4 bg-white">
                    <option value="">Seleccionar flujo...</option>
                    @foreach ($flujos as $flujo)
                        <option value="{{ $flujo->id }}">{{ $flujo->codigo }} - {{ $flujo->nombre }}</option>
                    @endforeach
                </select>

                <template x-if="flujoId">
                    <div>
                        <div class="flex gap-2 mb-4">
                            <input type="text" x-model="nuevoPasoNombre" placeholder="Nuevo paso..." class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#007BFF]">
                            <button @click="agregarPaso" class="px-3 py-2 rounded-xl bg-[#007BFF] text-white text-sm font-semibold hover:bg-blue-600">+</button>
                        </div>

                        <div class="space-y-1">
                            <template x-for="(paso, i) in pasos" :key="i">
                                <div>
                                    <div draggable="true"
                                         @dragstart="dragIndex = i"
                                         @dragover.prevent
                                         @drop="moverPaso(i)"
                                         class="paso flex items-center gap-3">
                                        <div class="flex-1">
                                            <input type="text" x-model="paso.nombre" class="w-full bg-transparent text-sm font-medium text-slate-800 focus:outline-none border-b border-transparent focus:border-[#007BFF]">
                                            <select x-model="paso.rol" class="text-xs text-slate-400 bg-transparent mt-1 focus:outline-none">
                                                <option value="">Cualquier rol</option>
                                                <option value="administrador">Administrador</option>
                                                <option value="gerente">Gerente</option>
                                                <option value="empleado">Empleado</option>
                                            </select>
                                        </div>
                                        <button @click="eliminarPaso(i)" class="p-1 text-slate-300 hover:text-rose-500 transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                    <div x-show="i < pasos.length - 1" class="paso-conector"><svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg></div>
                                </div>
                            </template>
                        </div>

                        <button @click="guardarPasos" class="w-full mt-4 py-2.5 rounded-xl bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                            Guardar Diseño
                        </button>
                    </div>
                </template>
            </div>

            <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Vista Previa del Flujo</h3>
                <div class="flex flex-col items-center gap-3 py-8">
                    <template x-for="(paso, i) in pasos" :key="i">
                        <div class="w-full max-w-md">
                            <div class="bg-white border-2 border-[#007BFF] rounded-2xl p-5 text-center shadow-lg">
                                <div class="text-xs font-semibold text-[#007BFF] uppercase tracking-wider mb-1" x-text="'PASO ' + (i+1)"></div>
                                <div class="text-lg font-bold text-slate-800" x-text="paso.nombre"></div>
                                <div class="text-xs text-slate-400 mt-1" x-show="paso.rol" x-text="'Responsable: ' + paso.rol"></div>
                            </div>
                            <div x-show="i < pasos.length - 1" class="flex justify-center py-1">
                                <svg class="h-6 w-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                            </div>
                        </div>
                    </template>
                    <div x-show="pasos.length === 0" class="text-sm text-slate-400 text-center py-12">
                        <svg class="h-12 w-12 mx-auto text-slate-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" /></svg>
                        Selecciona un flujo y agrega pasos para comenzar
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function disenadorApp() {
        return {
            flujoId: '',
            pasos: [],
            nuevoPasoNombre: '',
            dragIndex: null,
            csrf: '{{ csrf_token() }}',

            cargarPasos() {
                if (!this.flujoId) { this.pasos = []; return; }
                fetch(`{{ url('/disenador') }}/${this.flujoId}/pasos`)
                    .then(r => r.json()).then(d => { this.pasos = d.pasos || []; });
            },
            agregarPaso() {
                if (!this.nuevoPasoNombre.trim()) return;
                this.pasos.push({ nombre: this.nuevoPasoNombre, rol: '' });
                this.nuevoPasoNombre = '';
            },
            eliminarPaso(i) { this.pasos.splice(i, 1); },
            moverPaso(i) {
                if (this.dragIndex === null || this.dragIndex === i) return;
                const arr = this.pasos;
                const [el] = arr.splice(this.dragIndex, 1);
                arr.splice(i, 0, el);
                this.dragIndex = null;
            },
            guardarPasos() {
                if (!this.flujoId) return;
                fetch(`{{ url('/disenador') }}/${this.flujoId}/pasos`, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ pasos: JSON.stringify(this.pasos) })
                }).then(r => r.json()).then(d => { if (d.success) alert('Diseño guardado correctamente.'); });
            }
        };
    }
</script>
</body>
</html>
