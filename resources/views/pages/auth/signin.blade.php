<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | BunnyKlin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="flex h-screen bg-white">

    <div class="relative hidden w-1/2 bg-[#1E55AA] lg:block overflow-hidden">
        <img class="absolute inset-0 h-full w-full object-cover" 
             src="https://images.unsplash.com/photo-1517677208171-0bc6725a3e60?q=80&w=2070&auto=format&fit=crop" 
             alt="BunnyKlin Background" />
        
        <div class="absolute inset-0 bg-[#1E55AA]/85 mix-blend-multiply"></div>
        
        <div class="absolute inset-0 flex flex-col items-start justify-end p-16">
            <h1 class="mb-2 text-5xl font-bold text-white tracking-tight">BunnyKlin</h1>
            <p class="text-lg font-light text-blue-100">Punto de venta.</p>
        </div>
    </div>

    <div class="flex w-full items-center justify-center bg-white lg:w-1/2 px-8 sm:px-16 xl:px-24">
        
        <div class="w-full max-w-sm">
            
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900">Iniciar sesión</h2>
                <p class="text-gray-500 mt-1">Acceda al panel administrativo.</p>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 text-red-700 px-4 py-3 rounded text-sm font-medium border border-red-100">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Usuario / Email</label>
                    <input type="email" name="email" required 
                        class="w-full px-0 py-2 border-b border-gray-300 focus:border-[#1E55AA] focus:ring-0 transition-colors outline-none bg-transparent">
                </div>

                <div class="mb-10">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Contraseña</label>
                    <input type="password" name="password" required 
                        class="w-full px-0 py-2 border-b border-gray-300 focus:border-[#1E55AA] focus:ring-0 transition-colors outline-none bg-transparent">
                </div>

                <button type="submit" 
                    class="w-full bg-[#1E55AA] text-white font-semibold py-3 rounded hover:bg-blue-800 transition-colors shadow-sm">
                    Acceder
                </button>
            </form>
            
            <div class="mt-12 text-center">
                <p class="text-[11px] text-gray-400 uppercase tracking-widest">&copy; {{ date('Y') }} BunnyKlin. Todos los derechos reservados.</p>
            </div>
            
        </div>
    </div>

</body>
</html>