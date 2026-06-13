<div x-data="notasApp()" x-init="cargar()" class="fixed bottom-6 right-6 z-40">
    <button @click="abierto = !abierto"
            class="h-12 w-12 rounded-full shadow-lg flex items-center justify-center transition-all duration-200 hover:scale-110"
            :class="abierto ? 'bg-slate-800 rotate-45' : 'bg-blue-600 hover:bg-blue-700'">
        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <template x-if="!abierto">
                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </template>
            <template x-if="abierto">
                <path d="M6 18L18 6M6 6l12 12" />
            </template>
        </svg>
    </button>

    <div x-show="abierto" x-cloak @click.outside="abierto = false"
         class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden max-h-[70vh] flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Notas
            </h3>
            <span class="text-xs text-slate-400" x-text="notas.length + ' nota(s)'"></span>
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            <template x-for="nota in notas" :key="nota.id">
                <div class="rounded-xl p-3 border transition-all group relative"
                     :style="{ backgroundColor: colorBg(nota.color), borderColor: colorBorder(nota.color) }">
                    <div class="flex items-start gap-2">
                        <div class="flex-1 min-w-0">
                            <div x-show="editId !== nota.id"
                                 class="text-sm text-slate-800 whitespace-pre-wrap break-words"
                                 x-text="nota.content"
                                 @dblclick="editar(nota)"></div>
                            <textarea x-show="editId === nota.id"
                                      x-model="editContent"
                                      class="w-full text-sm rounded-lg border border-slate-300 px-2 py-1 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      rows="3"
                                      x-ref="editArea"
                                      @keydown.escape="editId = null"
                                      @keydown.ctrl.enter="guardarEdit(nota)"></textarea>
                        </div>
                        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="editar(nota)" class="p-1 rounded-lg hover:bg-white/50 text-slate-400 hover:text-slate-600 transition-colors" x-show="editId !== nota.id">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button @click="eliminar(nota.id)" class="p-1 rounded-lg hover:bg-white/50 text-slate-400 hover:text-rose-600 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                    <div x-show="editId === nota.id" class="flex items-center gap-1.5 mt-2">
                        <template x-for="c in colores" :key="c">
                            <button @click="editColor = c"
                                    class="h-5 w-5 rounded-full border-2 transition-all"
                                    :class="editColor === c ? 'border-slate-600 scale-110' : 'border-transparent'"
                                    :style="{ backgroundColor: colorBorder(c) }"></button>
                        </template>
                        <button @click="guardarEdit(nota)" class="ml-auto px-2 py-0.5 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar</button>
                        <button @click="editId = null" class="px-2 py-0.5 text-xs font-semibold text-slate-500 hover:text-slate-700">Cancelar</button>
                    </div>
                </div>
            </template>

            <div x-show="notas.length === 0" class="text-center py-8 text-sm text-slate-400">
                <svg class="h-10 w-10 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Sin notas aún
            </div>
        </div>

        <div class="border-t border-slate-100 p-3 bg-slate-50 space-y-2">
            <textarea x-model="nuevaContent" rows="2"
                      class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Escribe una nota..."></textarea>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <template x-for="c in colores" :key="c">
                        <button @click="nuevoColor = c"
                                class="h-6 w-6 rounded-full border-2 transition-all"
                                :class="nuevoColor === c ? 'border-slate-600 scale-110' : 'border-transparent'"
                                :style="{ backgroundColor: colorBorder(c) }"></button>
                    </template>
                </div>
                <button @click="crear()" :disabled="!nuevaContent.trim()"
                        class="px-3 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notasApp', () => ({
        abierto: false,
        notas: [],
        nuevaContent: '',
        nuevoColor: 'yellow',
        editId: null,
        editContent: '',
        editColor: 'yellow',
        csrf: '{{ csrf_token() }}',
        colores: ['yellow', 'green', 'blue', 'pink', 'purple', 'orange', 'red', 'slate'],

        colorBg(c) {
            const map = {
                yellow: '#fef9c3',
                green: '#dcfce7',
                blue: '#dbeafe',
                pink: '#fce7f3',
                purple: '#f3e8ff',
                orange: '#ffedd5',
                red: '#ffe4e6',
                slate: '#f1f5f9',
            };
            return map[c] || '#fef9c3';
        },
        colorBorder(c) {
            const map = {
                yellow: '#eab308',
                green: '#22c55e',
                blue: '#3b82f6',
                pink: '#ec4899',
                purple: '#a855f7',
                orange: '#f97316',
                red: '#ef4444',
                slate: '#94a3b8',
            };
            return map[c] || '#eab308';
        },

        cargar() {
            fetch('{{ route("notas.index") }}')
                .then(r => r.json())
                .then(data => { this.notas = data; });
        },

        crear() {
            const content = this.nuevaContent.trim();
            if (!content) return;
            fetch('{{ route("notas.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ content, color: this.nuevoColor })
            }).then(r => r.json()).then(nota => {
                this.notas.unshift(nota);
                this.nuevaContent = '';
                this.nuevoColor = 'yellow';
            });
        },

        editar(nota) {
            this.editId = nota.id;
            this.editContent = nota.content;
            this.editColor = nota.color;
            this.$nextTick(() => {
                if (this.$refs.editArea) this.$refs.editArea.focus();
            });
        },

        guardarEdit(nota) {
            if (!this.editContent.trim()) return;
            fetch('{{ url("/notas") }}/' + nota.id, {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ content: this.editContent, color: this.editColor })
            }).then(r => r.json()).then(updated => {
                const idx = this.notas.findIndex(n => n.id === nota.id);
                if (idx !== -1) this.notas[idx] = updated;
                this.editId = null;
            });
        },

        eliminar(id) {
            if (!confirm('¿Eliminar esta nota?')) return;
            fetch('{{ url("/notas") }}/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' }
            }).then(r => r.json()).then(() => {
                this.notas = this.notas.filter(n => n.id !== id);
            });
        },
    }));
});
</script>
