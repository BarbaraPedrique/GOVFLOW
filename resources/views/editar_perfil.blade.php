<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

    @include('partials.sidebar')

    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
            <div>
                <h2 class="text-slate-800 font-semibold text-lg">Panel de Control</h2>
            </div>

            <div class="flex items-center gap-6">
                @include('partials.break-buttons')
                @include('partials.notification-bell')
                <div class="relative" x-data="{ open: false }">
                    <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                        <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->apodo ?? Auth::user()->name }}</p><p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p></div>
                        <img src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                        <svg class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                    </div>
                    @include('partials.user-dropdown')
                </div>
            </div>
        </header>

        <main class="flex-1 p-10 mt-16 max-w-[1000px] w-full mx-auto space-y-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Editar Perfil</h1>
                <p class="text-slate-500 text-sm">Gestiona la información de tu cuenta e identidad en la plataforma.</p>
            </div>

            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl flex items-center gap-3 text-sm font-medium">
                    <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3 text-sm font-medium">
                    <svg class="h-5 w-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                @csrf

                <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex flex-col sm:flex-row items-center gap-6" x-data="{ imgPreview: '{{ $user->foto ? asset('storage/'.$user->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}' }">
                        <div class="relative group">
                            <img :src="imgPreview" alt="Vista previa avatar" class="h-24 w-24 rounded-full object-cover border-2 border-white shadow-md ring-4 ring-slate-100">
                        </div>
                        <div class="text-center sm:text-left space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Foto de Perfil</label>
                            <p class="text-xs text-slate-400">Soporta PNG, JPG o GIF. Máximo 2MB.</p>
                            <div class="mt-2">
                                <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 transition-all">
                                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                    Subir nueva foto
                                    <input type="file" name="foto" class="hidden" @change="let file = $event.target.files[0]; if(file) { imgPreview = URL.createObjectURL(file) }">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="space-y-2">
                            <label class="flex items-center justify-between text-sm font-semibold text-slate-700">
                                <span>Nombre Completo</span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 15v2m0 0v2m0-2h2m-2 0H10m3.432-9.352a4.796 4.796 0 011.086 1.086a4.796 4.796 0 01.086 4.22c-.172.417-.463.765-.828 1.011l-.039.026a4.833 4.833 0 01-5.467-.026l-.039-.026a2.417 2.417 0 01-.828-1.011a4.796 4.796 0 01.086-4.22a4.796 4.796 0 011.086-1.086M12 3v1m0 16v1m9-9h-1M4 12H3" /></svg>
                                    Requiere Autorización
                                </span>
                            </label>
                            <input type="text" name="nombre" value="{{ old('nombre', $user->name) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Apodo / Nickname</label>
                            <input type="text" name="apodo" value="{{ old('apodo', $user->apodo) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center justify-between text-sm font-semibold text-slate-700">
                                <span>Fecha de Nacimiento</span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">
                                    Requiere Autorización
                                </span>
                            </label>
                            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($user->fecha_nacimiento)->format('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Solicitar Cambio de Rol</label>
                            <div class="relative">
                                <select name="rol_solicitado" class="w-full px-4 py-2.5 appearance-none rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium bg-white transition-all">
                                    <option value="administrador" @selected($user->role?->slug === 'administrador')>{{ $user->role?->slug === 'administrador' ? 'Administrador (Actual)' : 'Administrador' }}</option>
                                    <option value="gerente" @selected($user->role?->slug === 'gerente')>{{ $user->role?->slug === 'gerente' ? 'Gerente (Actual)' : 'Gerente' }}</option>
                                    <option value="lider_equipo" @selected($user->role?->slug === 'lider_equipo')>{{ $user->role?->slug === 'lider_equipo' ? 'Líder de Equipo (Actual)' : 'Líder de Equipo' }}</option>
                                    <option value="empleado" @selected($user->role?->slug === 'empleado')>{{ $user->role?->slug === 'empleado' ? 'Empleado (Actual)' : 'Empleado' }}</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">Descripción del Perfil</label>
                        <textarea name="descripcion" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium transition-all placeholder-slate-400" placeholder="Escribe una breve descripción sobre ti y tus responsabilidades...">{{ old('descripcion', $user->descripcion) }}</textarea>
                    </div>
                </div>

                <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ url('/inicio') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#007BFF] text-sm font-semibold text-white hover:bg-blue-600 shadow-sm shadow-blue-100 transition-colors">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        @include('partials.solicitar-modal')
        </main>
    </div>

</body>
</html>
