<div class="flex items-center gap-2 border-r border-slate-200 pr-4" x-data="{
    onBreak: false,
    breakSeconds: 0,
    interval: null,
    csrf: '{{ csrf_token() }}',
    init() {
        fetch('{{ route('break.status') }}')
            .then(r => r.json()).then(d => {
                if (d.success) {
                    this.onBreak = d.on_break;
                    this.breakSeconds = d.break_seconds || 0;
                    if (this.onBreak) this.iniciarContador();
                }
            }).catch(e => console.error('break.status error:', e));
    },
    iniciarDescanso() {
        if (this.onBreak) return;
        fetch('{{ route('break.start') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' } })
            .then(r => r.json()).then(d => {
                if (d.success) { this.onBreak = true; this.breakSeconds = 0; this.iniciarContador(); }
                else { console.warn('break.start:', d); }
            }).catch(e => console.error('break.start error:', e));
    },
    culminarDescanso() {
        if (!this.onBreak) return;
        fetch('{{ route('break.end') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' } })
            .then(r => r.json()).then(d => {
                if (d.success) { this.onBreak = false; this.breakSeconds = 0; this.detenerContador(); }
                else { console.warn('break.end:', d); }
            }).catch(e => console.error('break.end error:', e));
    },
    iniciarContador() {
        this.detenerContador();
        this.interval = setInterval(() => { this.breakSeconds++; }, 1000);
    },
    detenerContador() {
        if (this.interval) { clearInterval(this.interval); this.interval = null; }
    },
    formatearTiempo(seg) {
        const h = Math.floor(seg / 3600);
        const m = Math.floor((seg % 3600) / 60);
        const s = seg % 60;
        return (h ? h + 'h ' : '') + (m ? m + 'm ' : '') + s + 's';
    }
}" x-init="init()">
    <button @click="iniciarDescanso" :class="onBreak ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100 cursor-pointer'" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        Inicio de descanso
    </button>
    <button @click="culminarDescanso" :class="!onBreak ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed' : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100 cursor-pointer'" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7" /></svg>
        Culminar tiempo de descanso
    </button>
    <span x-show="onBreak" class="text-xs text-slate-400" x-text="'(' + formatearTiempo(breakSeconds) + ')'"></span>
</div>
