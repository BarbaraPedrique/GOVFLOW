<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Crear Flujo - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .input-field {
            width:100%; border:1.5px solid #E2E8F0; border-radius:10px;
            padding:.65rem .9rem; font-size:.9rem; outline:none;
            transition:border-color .2s, box-shadow .2s;
        }
        .input-field:focus { border-color:#007BFF; box-shadow:0 0 0 3px rgba(0,123,255,.12); }
        .input-field.is-invalid { border-color:#EF4444; }
        .invalid-feedback { color:#EF4444; font-size:.78rem; margin-top:.25rem; display:block; }
    </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

{{-- Sidebar --}}
<aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-20">
    <div class="h-16 border-b border-slate-100 flex items-center px-6">
        <img src="{{ asset('imagenes/logo2.png') }}" alt="GOVFLOW" class="h-8 w-auto object-contain"/>
    </div>
    <nav class="p-4 space-y-1 flex-1">
        <a href="{{ url('/inicio') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V16zM14 14a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
            Dashboard
        </a>
        <a href="{{ route('flujos.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#007BFF] bg-blue-50 rounded-xl transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Flujos de Trabajo
        </a>
    </nav>
</aside>

<div class="flex-1 ml-64 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center px-10 fixed right-0 left-64 top-0 z-30">
        <h2 class="text-slate-800 font-semibold text-lg">Crear Nuevo Flujo</h2>
    </header>

    <main class="flex-1 p-10 mt-16 max-w-xl">
        <div class="mb-6">
            <a href="{{ route('flujos.index') }}" class="text-sm text-[#007BFF] hover:underline flex items-center gap-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
                Volver a Flujos
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
            <h1 class="text-xl font-bold text-slate-800 mb-1">Nuevo Flujo de Trabajo</h1>
            <p class="text-slate-500 text-sm mb-6">Completa los datos para registrar el flujo.</p>

            <form method="POST" action="{{ route('flujos.store') }}">
                @csrf

                <div class="space-y-5">
                    {{-- Nombre --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre del Flujo</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                               class="input-field {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                               placeholder="Ej: Aprobación de Presupuesto Q3"/>
                        @error('nombre') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- Departamento --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Departamento</label>
                        <input type="text" name="departamento" value="{{ old('departamento') }}"
                               class="input-field {{ $errors->has('departamento') ? 'is-invalid' : '' }}"
                               placeholder="Ej: Finanzas"/>
                        @error('departamento') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Estado</label>
                        <select name="estado" class="input-field {{ $errors->has('estado') ? 'is-invalid' : '' }}">
                            <option value="">Selecciona un estado</option>
                            @foreach(['Activo','Borrador','Completado','Pausado'] as $estado)
                                <option value="{{ $estado }}" {{ old('estado') == $estado ? 'selected' : '' }}>
                                    {{ $estado }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="submit"
                            class="flex-1 bg-[#007BFF] hover:bg-blue-700 text-white font-semibold text-sm py-2.5 rounded-xl transition-colors shadow-sm">
                        Crear Flujo
                    </button>
                    <a href="{{ route('flujos.index') }}"
                       class="flex-1 text-center border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm py-2.5 rounded-xl transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
