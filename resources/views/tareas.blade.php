<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } .drag-over { background-color: #f0f9ff; border-color: #007BFF; }</style>
</head>
<body class="bg-slate-50 flex min-h-screen">

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-20">
    <div class="h-16 border-b border-slate-100 flex items-center px-6">
        <img src="{{ asset('imagenes/logo2.png') }}" alt="Logo GOVFLOW" class="h-8 w-auto object-contain">
        <span class="ml-3 text-lg font-bold text-slate-800">GOVFLOW</span>
    </div>
    <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
        <a href="{{ route('inicio') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V16zM14 14a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg> Inicio</a>
        <a href="{{ route('tareas.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#007BFF] bg-blue-50 rounded-xl transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg> Tareas</a>
        <a href="{{ route('horarios.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> Horarios</a>
        <a href="{{ route('flujos') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg> Flujos de Trabajo</a>
        <a href="{{ route('auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg> Auditoría</a>
        <a href="{{ route('logs.auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> Logs Auditoría</a>
        <a href="{{ route('disenador') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" /></svg> Diseñador</a>
    </nav>
</aside>

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
        <div><h2 class="text-slate-800 font-semibold text-lg">Tareas</h2></div>
        <div class="flex items-center gap-6">
            <div class="relative" x-data="{ openNotis: false, noLeidas: 0, notis: [] }"
                 x-init="fetch('{{ route('notificaciones.index') }}?ajax=1')
                    .then(r=>r.json()).then(d=>{ noLeidas=d.noLeidas; notis=d.notificaciones })">
                <button @click="openNotis = !openNotis; if(openNotis){fetch('{{ route('notificaciones.index') }}?ajax=1').then(r=>r.json()).then(d=>{noLeidas=d.noLeidas;notis=d.notificaciones})}" class="relative text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.03 6.03 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <span x-show="noLeidas > 0" x-text="noLeidas" class="absolute -top-1 -right-1 h-4 min-w-[16px] flex items-center justify-center bg-rose-500 text-white text-[10px] font-bold rounded-full px-1"></span>
                </button>
                <div x-show="openNotis" @click.outside="openNotis = false"
                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 max-h-96 overflow-y-auto">
                    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                        <span class="text-sm font-semibold text-slate-800">Notificaciones</span>
                        <button @click="fetch('{{ route('notificaciones.marcar-todas') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(r=>r.json()).then(()=>{noLeidas=0;notis.forEach(n=>n.leido=true)})" class="text-xs text-[#007BFF] hover:underline">Marcar todas leídas</button>
                    </div>
                    <template x-for="n in notis" :key="n.id">
                        <div class="px-4 py-3 hover:bg-slate-50 border-b border-slate-50 last:border-0 cursor-pointer"
                             @click="if(!n.leido){fetch('{{ url('/notificaciones') }}/'+n.id+'/leido',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>{n.leido=true;noLeidas=Math.max(0,noLeidas-1)})}; n.url && (window.location=n.url)">
                            <div class="flex gap-3">
                                <div class="mt-0.5" :class="n.color || 'text-[#007BFF]'">
                                    <template x-if="n.icono"><span x-html="n.icono"></span></template>
                                    <template x-if="!n.icono"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800" x-text="n.titulo" :class="!n.leido ? 'font-semibold' : ''"></p>
                                    <p class="text-xs text-slate-400 mt-0.5" x-text="n.mensaje"></p>
                                    <p class="text-[10px] text-slate-300 mt-1" x-text="new Date(n.created_at).toLocaleDateString('es-ES',{day:'numeric',month:'short',hour:'2-digit',minute:'2-digit'})"></p>
                                </div>
                                <div x-show="!n.leido" class="h-2 w-2 bg-[#007BFF] rounded-full mt-2 flex-shrink-0"></div>
                            </div>
                        </div>
                    </template>
                    <div x-show="notis.length === 0" class="p-6 text-center text-sm text-slate-400">Sin notificaciones</div>
                </div>
            </div>
            <div class="relative" x-data="{ open: false }">
                <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p>
                    </div>
                    <img src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                    <svg class="h-4 w-4 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                </div>
                <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50">
                    <a href="{{ route('perfil') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 transition-colors"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg> Mi Perfil</a>
                    <a href="{{ route('perfil.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 transition-colors"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.25 2.25 0 113.182 3.182L12 20.25l-4.5 1.5 1.5-4.5L18.586 3.586z" /></svg> Editar Perfil</a>
                    <hr class="my-2 border-slate-100">
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors w-full text-left"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg> Cerrar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 p-10 mt-16 max-w-[1200px] w-full mx-auto space-y-8">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg p-4">{{ session('success') }}</div>
        @endif

        <div x-data="tareasApp()" class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Nueva Tarea</h3>
                <form @submit.prevent="agregarTarea" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Título</label>
                        <input type="text" x-model="nuevaTarea.titulo" placeholder="¿Qué hay que hacer?" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Prioridad</label>
                        <select x-model="nuevaTarea.prioridad" class="px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] text-sm bg-white">
                            <option value="alta">Alta</option>
                            <option value="media" selected>Media</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Categoría</label>
                        <input type="text" x-model="nuevaTarea.categoria" placeholder="Ej: Desarrollo" class="px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] text-sm w-32">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Vence</label>
                        <input type="date" x-model="nuevaTarea.fecha_vencimiento" class="px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] text-sm">
                    </div>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#007BFF] text-sm font-semibold text-white hover:bg-blue-600">Agregar</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <template x-for="(grupo, p) in {alta:'Alta',media:'Media',baja:'Baja'}" :key="p">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full" :class="{'bg-rose-500':p==='alta','bg-amber-500':p==='media','bg-emerald-500':p==='baja'}"></span>
                            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider" x-text="grupo"></h3>
                            <span class="text-xs text-slate-400" x-text="'(' + $store.tareas.filter(t=>t.prioridad===p&&!t.completada).length + ')'"></span>
                        </div>
                        <div class="space-y-2" @drop="moverTarea($event, p)" @dragover.prevent>
                            <template x-for="tarea in $store.tareas.filter(t=>t.prioridad===p&&!t.completada).sort((a,b)=>a.orden-b.orden)" :key="tarea.id">
                                <div draggable="true" @dragstart="dragTarea=tarea.id"
                                     class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-all cursor-grab active:cursor-grabbing"
                                     :class="{'opacity-50':tarea.completada, 'border-l-4 border-l-rose-500':tarea.prioridad==='alta', 'border-l-4 border-l-amber-500':tarea.prioridad==='media', 'border-l-4 border-l-emerald-500':tarea.prioridad==='baja'}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-800" x-text="tarea.titulo"></p>
                                            <p class="text-xs text-slate-400 mt-1" x-show="tarea.categoria" x-text="tarea.categoria"></p>
                                            <p class="text-xs text-slate-400 mt-0.5" x-show="tarea.fecha_vencimiento" x-text="'Vence: '+new Date(tarea.fecha_vencimiento+'T00:00:00').toLocaleDateString('es-ES')"></p>
                                        </div>
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <button @click="toggleCompletada(tarea)" class="p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-emerald-600 transition-colors" title="Completar">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </button>
                                            <button @click="eliminarTarea(tarea)" class="p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-rose-600 transition-colors" title="Eliminar">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="$store.tareas.filter(t=>t.prioridad===p&&!t.completada).length===0" class="text-sm text-slate-400 text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                Sin tareas pendientes
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="$store.tareas.filter(t=>t.completada).length>0" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-700">Completadas (<span x-text="$store.tareas.filter(t=>t.completada).length"></span>)</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    <template x-for="tarea in $store.tareas.filter(t=>t.completada)" :key="tarea.id">
                        <div class="px-6 py-3 flex items-center gap-3 hover:bg-slate-50">
                            <button @click="toggleCompletada(tarea)" class="text-emerald-500"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button>
                            <span class="text-sm text-slate-500 line-through flex-1" x-text="tarea.titulo"></span>
                            <span class="text-xs text-slate-400 capitalize" x-text="tarea.prioridad"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('tareas', @json($tareas->flatten(1)->values() ?? []));

    Alpine.data('tareasApp', () => ({
        nuevaTarea: { titulo: '', prioridad: 'media', categoria: '', fecha_vencimiento: '' },
        dragTarea: null,
        csrf: '{{ csrf_token() }}',

        agregarTarea() {
            const form = new FormData();
            form.append('titulo', this.nuevaTarea.titulo);
            form.append('prioridad', this.nuevaTarea.prioridad);
            form.append('categoria', this.nuevaTarea.categoria);
            form.append('fecha_vencimiento', this.nuevaTarea.fecha_vencimiento);

            fetch('{{ route('tareas.store') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf }, body: form })
                .then(r => r.redirected ? window.location.href = r.url : r.json())
                .then(data => {
                    if (data?.success) {
                        const t = { id: data.id, titulo: this.nuevaTarea.titulo, prioridad: this.nuevaTarea.prioridad, categoria: this.nuevaTarea.categoria, fecha_vencimiento: this.nuevaTarea.fecha_vencimiento, completada: false, orden: 999 };
                        Alpine.store('tareas').push(t);
                        this.nuevaTarea = { titulo: '', prioridad: 'media', categoria: '', fecha_vencimiento: '' };
                    } else { window.location.reload(); }
                });
        },
        toggleCompletada(tarea) {
            fetch('{{ url('/tareas') }}/'+tarea.id, { method: 'PUT', headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json' }, body: JSON.stringify({ completada: !tarea.completada }) })
                .then(r => r.json()).then(d => { if(d.success) tarea.completada = !tarea.completada; });
        },
        eliminarTarea(tarea) {
            if(!confirm('¿Eliminar esta tarea?')) return;
            fetch('{{ url('/tareas') }}/'+tarea.id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrf } })
                .then(r => r.json()).then(d => { if(d.success) Alpine.store('tareas', Alpine.store('tareas').filter(t => t.id !== tarea.id)); });
        },
        moverTarea(ev, prioridad) {
            const id = this.dragTarea;
            if (!id) return;
            const tarea = Alpine.store('tareas').find(t => t.id === id);
            if (!tarea) return;
            const oldP = tarea.prioridad;
            tarea.prioridad = prioridad;
            fetch('{{ url('/tareas') }}/'+id, { method: 'PUT', headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json' }, body: JSON.stringify({ prioridad }) })
                .then(r => r.json()).then(d => { if(!d.success) tarea.prioridad = oldP; });
            this.dragTarea = null;
        }
    }));
});
</script>
</body>
</html>
