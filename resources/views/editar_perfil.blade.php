<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-20">
        <div class="h-16 border-b border-slate-100 flex items-center px-6">
            <img src="{{ asset('imagenes/logo2.png') }}" alt="Logo GOVFLOW" class="h-8 w-auto object-contain">
        </div>

        <nav class="p-4 space-y-1 flex-1">
            <a href="{{ url('/inicio') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V16zM14 14a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                Inicio
            </a>
            <a href="{{ url('/flujos') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                Flujos de Trabajo
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                Auditoría
            </a>
        </nav>
    </aside>

    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
            <div>
                <h2 class="text-slate-800 font-semibold text-lg">Panel de Control</h2>
            </div>

            <div class="flex items-center gap-6">
                <button class="relative text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="absolute top-0 right-0 h-2 w-2 bg-rose-500 rounded-full border-2 border-white"></span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.03 6.03 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

                <div class="relative" x-data="{ open: false }">
                    <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-slate-800 leading-none">Juan Pérez</p>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Administrador</p>
                        </div>
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Avatar" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                        <svg class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50">

                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-[#007BFF] transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Mi Perfil
                        </a>
                        <a href="{{ url('/perfil/editar') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#007BFF] bg-blue-50 rounded-xl transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.25 2.25 0 113.182 3.182L12 20.25l-4.5 1.5 1.5-4.5L18.586 3.586z" /></svg>
                            Editar Perfil
                        </a>

                        <hr class="my-2 border-slate-100">

                        <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-10 mt-16 max-w-[1000px] w-full mx-auto space-y-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Editar Perfil</h1>
                <p class="text-slate-500 text-sm">Gestiona la información de tu cuenta e identidad en la plataforma.</p>
            </div>

            <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                @csrf
                @method('PUT')

                <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex flex-col sm:flex-row items-center gap-6" x-data="{ imgPreview: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }">
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
                            <input type="text" name="nombre" value="Juan Pérez" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Apodo / Nickname</label>
                            <input type="text" name="apodo" value="JuanP" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center justify-between text-sm font-semibold text-slate-700">
                                <span>Fecha de Nacimiento</span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">
                                    Requiere Autorización
                                </span>
                            </label>
                            <input type="date" name="fecha_nacimiento" value="1995-05-12" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Solicitar Cambio de Rol</label>
                            <div class="relative">
                                <select name="rol_solicitado" class="w-full px-4 py-2.5 appearance-none rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium bg-white transition-all">
                                    <option value="Administrador" selected>Administrador (Actual)</option>
                                    <option value="Gerente">Gerente</option>
                                    <option value="Empleado">Empleado</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">Descripción del Perfil</label>
                        <textarea name="descripcion" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-[#007BFF] focus:ring-2 focus:ring-blue-50 text-slate-800 text-sm font-medium transition-all placeholder-slate-400" placeholder="Escribe una breve descripción sobre ti y tus responsabilidades..."></textarea>
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
        </main>
    </div>

</body>
</html>
