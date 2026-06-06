@if(Auth::check() && Auth::user()->role?->slug !== 'super_admin')
    @php
        $equiposDisponibles = \App\Models\Equipo::whereDoesntHave('miembros', function ($q) {
            $q->where('user_id', Auth::id());
        })->where('gerente_id', '!=', Auth::id())->get();
        $misTareas = \App\Models\Tarea::where('user_id', Auth::id())
            ->where('completada', false)
            ->orderBy('created_at', 'desc')
            ->get();
        $solicitarErrores = $errors->hasAny(['tipo', 'equipo_id', 'tarea_id', 'descripcion']);
    @endphp

    <div x-data="{ solicitarModal: @json($solicitarErrores), tipoSolicitud: '{{ old('tipo', '') }}' }">
        <button @click="solicitarModal = true"
            class="fixed bottom-6 left-6 z-40 flex items-center gap-2 px-4 py-2.5 bg-[#007BFF] text-white text-sm font-semibold rounded-xl shadow-lg hover:bg-blue-600 transition-all">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4" /></svg>
            Solicitar
        </button>

        <div x-show="solicitarModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" @click.outside="solicitarModal = false">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800">Nueva solicitud</h3>
                    <button @click="solicitarModal = false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form action="{{ route('solicitar.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tipo de solicitud</label>
                        <select name="tipo" x-model="tipoSolicitud" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccionar...</option>
                            <option value="unirse_equipo">Unirse a un equipo</option>
                            <option value="cambio_rol">Cambio de rol</option>
                            <option value="revision_tareas">Revisión de tareas</option>
                            <option value="revision_web">Revisión de la web</option>
                            <option value="reportar_error">Reportar error</option>
                        </select>
                        @error('tipo')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="tipoSolicitud === 'unirse_equipo'" x-cloak>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Equipo</label>
                        <select name="equipo_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccionar equipo...</option>
                            @foreach ($equiposDisponibles as $eq)
                                <option value="{{ $eq->id }}" @selected(old('equipo_id') == $eq->id)>{{ $eq->nombre }}</option>
                            @endforeach
                        </select>
                        @error('equipo_id')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="tipoSolicitud === 'revision_tareas'" x-cloak>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tarea a revisar</label>
                        <select name="tarea_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccionar tarea...</option>
                            @foreach ($misTareas as $t)
                                <option value="{{ $t->id }}" @selected(old('tarea_id') == $t->id)>{{ $t->titulo }} ({{ ucfirst($t->prioridad) }})</option>
                            @endforeach
                        </select>
                        @error('tarea_id')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="4" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Describe el motivo de tu solicitud...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="solicitarModal = false"
                            class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-[#007BFF] text-white text-sm font-semibold rounded-xl hover:bg-blue-600 transition-all">
                            Enviar solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
