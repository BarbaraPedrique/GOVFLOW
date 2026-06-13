<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - GOVFLOW</title>
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
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->apodo ?? Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p>
                            </div>
                            <img src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" alt="Avatar" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                            <svg class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                        </div>
                        @include('partials.user-dropdown')
                    </div>
            </div>
        </header>

        <main class="flex-1 p-10 mt-16 max-w-[1600px] w-full mx-auto space-y-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Resumen General</h1>
                <p class="text-slate-500 text-sm">Resumen del estado de la plataforma de gobernanza</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Mis Flujos</span>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $misFlujos }}</h3>
                        <span class="text-xs font-medium text-slate-400">donde participo</span>
                    </div>
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Usuarios Activos</span>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $usuariosActivos }}</h3>
                        <span class="text-xs font-medium text-slate-400">trabajando ahora</span>
                    </div>
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Tareas Personales</span>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $tareasPersonales }}</h3>
                        <span class="text-xs font-medium text-slate-400">por completar</span>
                    </div>
                    <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Tareas de Flujo</span>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $tareasFlujo }}</h3>
                        <span class="text-xs font-medium text-slate-400">pendientes de workflow</span>
                    </div>
                    <div class="p-2.5 bg-violet-50 text-violet-600 rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Tiempo Activo</span>
                        <h3 class="text-2xl font-bold text-slate-800">{{ $tiempoActivo }}</h3>
                        <span class="text-xs font-medium text-slate-400">desde inicio de sesión</span>
                    </div>
                    <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Última Sesión</span>
                        <h3 class="text-2xl font-bold text-slate-800">{{ $ultimaDuracion }}</h3>
                        <span class="text-xs font-medium text-slate-400">duración total</span>
                    </div>
                    <div class="p-2.5 bg-sky-50 text-sky-600 rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Rendimiento Mensual</span>
                        <div class="flex items-center gap-2">
                            <h3 class="text-2xl font-bold text-slate-800">{{ $estrellas }}</h3>
                            <span class="text-sm text-slate-400">/ 5</span>
                        </div>
                        @include('partials.estrellas', ['puntuacion' => $estrellas, 'tamano' => 'h-5 w-5'])
                    </div>
                    <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </div>
                </div>
            </div>
        @include('partials.solicitar-modal')
        </main>
    </div>
</body>
</html>
