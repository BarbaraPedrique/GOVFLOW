<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - GOVFLOW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md p-6">

        <div class="flex justify-center mb-8">
            <img src="{{ asset('imagenes/logo.png') }}" alt="Logo GOVFLOW" class="h-32 w-auto object-contain">
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
            <div class="text-center mb-8">
                <h2 class="text-[22px] font-bold text-slate-800 mb-1">Iniciar Sesión</h2>
                <p class="text-slate-500 text-sm">Ingresa tus credenciales para acceder a la plataforma</p>
            </div>

            <form action="#" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-800 mb-2">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#007BFF] focus:ring-1 focus:ring-[#007BFF] transition-colors" placeholder="nombre@ejemplo.com" required autofocus>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-sm font-semibold text-slate-800">Contraseña</label>
                        <a href="#" class="text-sm font-medium text-[#007BFF] hover:text-blue-700 transition-colors">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#007BFF] focus:ring-1 focus:ring-[#007BFF] transition-colors" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#007BFF] hover:bg-blue-600 text-white font-medium py-2.5 rounded-lg transition-colors flex items-center justify-center mt-2 shadow-sm">
                    Ingresar
                </button>
            </form>
            <div class="text-center mt-5">
            <p class="text-sm text-slate-500">
                ¿No tienes una cuenta? <a href="{{ url('/registro') }}" class="text-[#007BFF] font-medium hover:underline">Regístrate aquí</a>
            </p>
        </div>
        </div>
    </div>

</body>
</html>
