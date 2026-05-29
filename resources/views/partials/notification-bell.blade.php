<div class="relative" x-data="{ openNotis: false, noLeidas: 0, notis: [] }"
     x-init="fetch('{{ route('notificaciones.index') }}?ajax=1').then(r=>r.json()).then(d=>{ noLeidas=d.noLeidas; notis=d.notificaciones })">
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
