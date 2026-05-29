<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flujos de Trabajo - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght=400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-20">
        <div class="h-16 border-b border-slate-100 flex items-center px-6">
            <img src="{{ asset('imagenes/logo2.png') }}" alt="Logo GOVFLOW" class="h-8 w-auto object-contain">
            <span class="ml-3 text-lg font-bold text-slate-800">GOVFLOW</span>
        </div>
        <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
            <a href="{{ route('inicio') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V16zM14 14a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg> Inicio</a>
            <a href="{{ route('tareas.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg> Tareas</a>
            <a href="{{ route('horarios.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> Horarios</a>
            <a href="{{ route('flujos') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-[#007BFF] bg-blue-50 rounded-xl transition-colors"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg> Flujos de Trabajo</a>
            <a href="{{ route('auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg> Auditoría</a>
            <a href="{{ route('logs.auditoria') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> Logs Auditoría</a>
            <a href="{{ route('disenador') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors"><svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" /></svg> Diseñador</a>
        </nav>
    </aside>

    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-10 fixed right-0 left-64 top-0 z-30">
            <div>
                <h2 class="text-slate-800 font-semibold text-lg">Flujos de Trabajo</h2>
            </div>

            <div class="flex items-center gap-6">
                @include('partials.notification-bell')
                <div class="relative" x-data="{ open: false }">
                    <div @click="open = !open" class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                        <div class="text-right hidden sm:block"><p class="text-sm font-semibold text-slate-800 leading-none">{{ Auth::user()->name }}</p><p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ Auth::user()->role?->display_name ?? 'Sin rol' }}</p></div>
                        <img src="{{ Auth::user()->foto ? asset('storage/'.Auth::user()->foto) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80' }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                        <svg class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7" /></svg>
                    </div>
                    @include('partials.user-dropdown')
                </div>
            </div>
        </header>

        <main class="flex-1 p-10 mt-16 max-w-[1600px] w-full mx-auto space-y-8">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Línea de Tiempo del Flujo</h1>
                    <p class="text-slate-500 text-sm">Monitoreo de la secuencia lógica, asignación de tareas y actores responsables.</p>
                </div>
                <div>
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-semibold border border-emerald-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Estado: En Ejecución
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total de Tareas</span>
                        <h3 class="text-2xl font-bold text-slate-800">4 Pasos</h3>
                    </div>
                    <div class="p-2.5 bg-blue-50 text-[#007BFF] rounded-xl">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Actores Involucrados</span>
                        <h3 class="text-2xl font-bold text-slate-800">3 Sistemas / Equipos</h3>
                    </div>
                    <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Progreso Temporal</span>
                        <h3 class="text-2xl font-bold text-slate-800">75% Completado</h3>
                    </div>
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8 relative">

                <div class="absolute left-12 top-12 bottom-12 w-0.5 bg-slate-200 z-0"></div>

                <div class="space-y-12 relative z-10">

                    <div class="flex items-start gap-6">
                        <div class="h-8 w-8 rounded-full bg-emerald-500 border-4 border-white shadow flex items-center justify-center text-white shrink-0 mt-1">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <div class="flex-1 bg-slate-50 border border-slate-100 p-5 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <span class="text-[11px] font-bold tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">PASO 1 • COMPLETADO</span>
                                <h4 class="text-base font-semibold text-slate-800">Carga del Expediente Digital</h4>
                                <p class="text-slate-500 text-sm">Subida y ordenamiento de la documentación legal del solicitante.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl border border-slate-200 shrink-0 self-start md:self-auto shadow-sm">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=60&q=80" alt="Actor" class="h-7 w-7 rounded-full object-cover">
                                <div>
                                    <p class="text-xs font-semibold text-slate-700 leading-none">Juan Pérez</p>
                                    <span class="text-[10px] text-slate-400 font-medium">Actor: Administrador</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-6">
                        <div class="h-8 w-8 rounded-full bg-emerald-500 border-4 border-white shadow flex items-center justify-center text-white shrink-0 mt-1">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <div class="flex-1 bg-slate-50 border border-slate-100 p-5 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <span class="text-[11px] font-bold tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">PASO 2 • COMPLETADO</span>
                                <h4 class="text-base font-semibold text-slate-800">Validación OCR Automática</h4>
                                <p class="text-slate-500 text-sm">Verificación inteligente de identidad y firmas a través del software.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl border border-slate-200 shrink-0 self-start md:self-auto shadow-sm">
                                <div class="h-7 w-7 rounded-lg bg-blue-50 text-[#007BFF] flex items-center justify-center">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-700 leading-none">AI-Engine Core</p>
                                    <span class="text-[10px] text-blue-600 font-medium">Actor: Software Bot</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-6">
                        <div class="h-8 w-8 rounded-full bg-[#007BFF] border-4 border-blue-100 shadow flex items-center justify-center text-white shrink-0 mt-1 ring-4 ring-blue-50">
                            <span class="h-2 w-2 rounded-full bg-white animate-ping"></span>
                        </div>
                        <div class="flex-1 bg-white border-2 border-blue-500 p-5 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
                            <div class="space-y-1">
                                <span class="text-[11px] font-bold tracking-wider text-[#007BFF] bg-blue-50 px-2 py-0.5 rounded-md">PASO 3 • EN PROCESO ACTUAL</span>
                                <h4 class="text-base font-semibold text-slate-800">Auditoría y Firma del Gerente</h4>
                                <p class="text-slate-500 text-sm">Aprobación técnica definitiva y firma electrónica para la emisión.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-200 shrink-0 self-start md:self-auto">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=60&q=80" alt="Actor" class="h-7 w-7 rounded-full object-cover">
                                <div>
                                    <p class="text-xs font-semibold text-slate-700 leading-none">Carlos Mendoza</p>
                                    <span class="text-[10px] text-purple-600 font-medium">Actor: Equipo Gerencia</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-6">
                        <div class="h-8 w-8 rounded-full bg-slate-200 border-4 border-white shadow flex items-center justify-center text-slate-400 shrink-0 mt-1">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="flex-1 bg-white border border-slate-200 p-5 rounded-2xl opacity-60 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <span class="text-[11px] font-bold tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">PASO 4 • EN ESPERA</span>
                                <h4 class="text-base font-semibold text-slate-800">Notificación y Envío a Cliente</h4>
                                <p class="text-slate-500 text-sm">Distribución del producto finalizado mediante canales automatizados.</p>
                            </div>
                            <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 shrink-0 self-start md:self-auto">
                                <div class="h-7 w-7 rounded-lg bg-slate-200 text-slate-500 flex items-center justify-center">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-600 leading-none">Módulo de Correo</p>
                                    <span class="text-[10px] text-slate-400 font-medium">Actor: Sistema Mailer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

</body>
</html>
