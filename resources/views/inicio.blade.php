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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-1">
                 <div class="bg-white p-6 rounded-2x1 border border-slate-200 shadow-sm flex justify-between items-start">
                    <div class="space-y-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Flujos de Trabajo</span>
                        <h3 class="text-3xl font-bold text-slate-800">{{ $totalFlujos }}</h3>
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md inline-block">{{ $flujosActivos }} activos</span>
                    </div>
                    <div class="p-2.5 bg-blue-50 text-[#007BFF] rounded-xl">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                </div>
                </div>
        @include('partials.solicitar-modal')
        </main>
    </div>
</body>
</html>
