@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 p-6 font-sans">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <h1 class="text-2xl font-bold text-blue-900">Máquinas IoT</h1>
        
        <div class="flex flex-wrap gap-4 text-sm font-medium text-gray-700 bg-white p-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span>Disponible (5)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span>En Uso (4)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                <span>Fuera de Servicio (1)</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">
        
        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-blue-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-blue-50 text-blue-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M3 18h18M3 6h18"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-full">En Uso</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">L-01</h3>
                <p class="text-xs text-gray-500 font-medium">Lavadora</p>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-xs font-semibold text-gray-600">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Resta: 15 min
                    </span>
                    <span class="text-blue-600">75%</span>
                </div>
                <div class="w-full bg-blue-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full" style="width: 75%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-emerald-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-emerald-50 text-emerald-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M3 18h18M3 6h18"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-emerald-600 bg-emerald-50 rounded-full">Disponible</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">L-02</h3>
                <p class="text-xs text-gray-500 font-medium">Lavadora</p>
            </div>
            <button class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-medium text-sm py-2 px-4 rounded-xl transition-colors">
                Iniciar Ciclo
            </button>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-rose-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-rose-50 text-rose-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M3 18h18M3 6h18"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-rose-600 bg-rose-50 rounded-full">Fuera de Servicio</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">L-03</h3>
                <p class="text-xs text-gray-500 font-medium">Lavadora</p>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-rose-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Requiere mantenimiento</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-blue-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-blue-50 text-blue-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M3 18h18M3 6h18"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-full">En Uso</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">L-04</h3>
                <p class="text-xs text-gray-500 font-medium">Lavadora</p>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-xs font-semibold text-gray-600">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Resta: 5 min
                    </span>
                    <span class="text-blue-600">90%</span>
                </div>
                <div class="w-full bg-blue-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full" style="width: 90%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-emerald-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-emerald-50 text-emerald-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M3 18h18M3 6h18"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-emerald-600 bg-emerald-50 rounded-full">Disponible</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">L-05</h3>
                <p class="text-xs text-gray-500 font-medium">Lavadora</p>
            </div>
            <button class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-medium text-sm py-2 px-4 rounded-xl transition-colors">
                Iniciar Ciclo
            </button>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-emerald-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-emerald-50 text-emerald-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M3 18h18M3 6h18"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-emerald-600 bg-emerald-50 rounded-full">Disponible</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">L-06</h3>
                <p class="text-xs text-gray-500 font-medium">Lavadora</p>
            </div>
            <button class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-medium text-sm py-2 px-4 rounded-xl transition-colors">
                Iniciar Ciclo
            </button>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-emerald-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-emerald-50 text-emerald-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M8 5v14M16 5v14"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-emerald-600 bg-emerald-50 rounded-full">Disponible</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">S-01</h3>
                <p class="text-xs text-gray-500 font-medium">Secadora</p>
            </div>
            <button class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-medium text-sm py-2 px-4 rounded-xl transition-colors">
                Iniciar Ciclo
            </button>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-blue-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-blue-50 text-blue-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M8 5v14M16 5v14"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-full">En Uso</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">S-02</h3>
                <p class="text-xs text-gray-500 font-medium">Secadora</p>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-xs font-semibold text-gray-600">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Resta: 30 min
                    </span>
                    <span class="text-blue-600">45%</span>
                </div>
                <div class="w-full bg-blue-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full" style="width: 45%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-emerald-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-emerald-50 text-emerald-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M8 5v14M16 5v14"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-emerald-600 bg-emerald-50 rounded-full">Disponible</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">S-03</h3>
                <p class="text-xs text-gray-500 font-medium">Secadora</p>
            </div>
            <button class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-medium text-sm py-2 px-4 rounded-xl transition-colors">
                Iniciar Ciclo
            </button>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-blue-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-start">
                <div class="p-2.5 bg-blue-50 text-blue-500 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M8 5v14M16 5v14"/></svg>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-full">En Uso</span>
            </div>
            <div class="mt-4">
                <h3 class="text-xl font-bold text-gray-900">S-04</h3>
                <p class="text-xs text-gray-500 font-medium">Secadora</p>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-xs font-semibold text-gray-600">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Resta: 20 min
                    </span>
                    <span class="text-blue-600">60%</span>
                </div>
                <div class="w-full bg-blue-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full" style="width: 60%"></div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection