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
        $revisionesPendientes = \App\Models\FlujoPasoAsignacion::where('revisor_id', Auth::id())
            ->where('revision_estado', 'en_revision')
            ->with(['ejecucion.flujoTrabajo', 'ejecutores.user'])
            ->get();
    @endphp

    <div x-data="{ solicitarModal: @json($solicitarErrores), tipoSolicitud: '{{ old('tipo', '') }}', tabSolicitud: 'nueva', rechazoComentario: '', revisarPaso(id, accion, comentario) { fetch('{{ url('/flujos/paso') }}/' + id + '/revisar', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ accion: accion, comentario: comentario }) }).then(r => r.json()).then(d => { if (d.success) { location.reload(); } else { alert(d.message || 'Error al revisar el paso.'); } }).catch(e => alert('Error de red: ' + e.message)); } }">
        <button @click="solicitarModal = true"
            class="fixed bottom-6 left-6 z-40 flex items-center gap-2 px-4 py-2.5 bg-[#007BFF] text-white text-sm font-semibold rounded-xl shadow-lg hover:bg-blue-600 transition-all">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4" /></svg>
            @if ($revisionesPendientes->count() > 0)
                <span class="flex items-center gap-1">
                    Solicitar
                    <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-rose-500 rounded-full">{{ $revisionesPendientes->count() }}</span>
                </span>
            @else
                Solicitar
            @endif
        </button>

        <div x-show="solicitarModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" @click.outside="solicitarModal = false">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800">Solicitudes</h3>
                    <button @click="solicitarModal = false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Tabs -->
                <div class="flex border-b border-slate-200 px-6">
                    <button @click="tabSolicitud = 'nueva'" :class="tabSolicitud === 'nueva' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors">Nueva solicitud</button>
                    <button @click="tabSolicitud = 'revisiones'" :class="tabSolicitud === 'revisiones' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors">
                        Revisiones pendientes
                        @if ($revisionesPendientes->count() > 0)
                            <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-rose-500 rounded-full ml-1">{{ $revisionesPendientes->count() }}</span>
                        @endif
                    </button>
                </div>

                <!-- Tab: Nueva solicitud -->
                <div x-show="tabSolicitud === 'nueva'">
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

                <!-- Tab: Revisiones pendientes -->
                <div x-show="tabSolicitud === 'revisiones'" class="p-6">
                    @if ($revisionesPendientes->count() > 0)
                        <div class="space-y-3">
                            @foreach ($revisionesPendientes as $rev)
                                <div class="border border-slate-200 rounded-xl p-4 hover:border-amber-300 transition-colors">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $rev->paso_nombre }}</p>
                                            <p class="text-xs text-slate-500">Flujo: {{ $rev->ejecucion->flujoTrabajo->nombre ?? '—' }}</p>
                                        </div>
                                        <span class="text-[10px] font-semibold uppercase text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Pendiente</span>
                                    </div>
                                    @if ($rev->mensaje)
                                        <p class="text-xs text-slate-600 mb-2 bg-slate-50 rounded-lg p-2">{{ $rev->mensaje }}</p>
                                    @endif
                                    @if ($rev->ejecutores->count() > 0)
                                        <div class="flex flex-wrap gap-1 mb-2">
                                            @foreach ($rev->ejecutores as $ejec)
                                                <span class="text-[10px] bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">{{ $ejec->user->name ?? '—' }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if ($rev->archivo)
                                        <a href="{{ asset('storage/' . $rev->archivo) }}" target="_blank" class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1 mb-2">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            Ver archivo
                                        </a>
                                    @endif
                                    <div class="flex gap-2 mt-2 pt-2 border-t border-slate-100">
                                        <button @click="revisarPaso({{ $rev->id }}, 'aprobar', '')" class="px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">Aprobar</button>
                                        <div class="flex gap-1">
                                            <input type="text" x-model="rechazoComentario" placeholder="Motivo del rechazo..." class="w-32 text-xs rounded-lg border border-slate-200 px-2 py-1">
                                            <button @click="revisarPaso({{ $rev->id }}, 'rechazar', rechazoComentario)" class="px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 rounded-lg hover:bg-rose-100 transition-colors">Rechazar</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="h-10 w-10 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-sm text-slate-500">No tienes revisiones pendientes.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
