@extends('layouts.app')

@section('content')
<div x-data='posSystem(@json($services ?? []), [], [], [])' class="p-6 bg-[#F4F8FC] min-h-screen font-nunito">

    {{-- MODALES --}}
    @include('pos.modals')

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-8 max-w-[1600px] mx-auto">
        <h1 class="text-2xl font-black text-[#1E55AA]">Servicios</h1>
        <button @click="openAddModal('services')"
            class="bg-[#1E55AA] hover:bg-[#153e7d] text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-[#1E55AA]/20 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Registrar Servicio
        </button>
    </div>

    <div class="max-w-[1600px] mx-auto space-y-6">
        {{-- Tarjetas de Métricas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Tarjeta 1: Total de Servicios --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="w-12 h-12 bg-blue-50 text-[#1E55AA] rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-3xl font-black text-slate-800" x-text="services.length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Total de Servicios</p>
            </div>

            {{-- Tarjeta 2: Servicios por Encargo --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-indigo-100 relative overflow-hidden">
                <div class="absolute top-4 right-4 bg-indigo-100 text-indigo-700 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Drop-off
                </div>
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                {{-- Contamos los que tienen la bandera is_for_orders en true --}}
                <h3 class="text-3xl font-black text-indigo-600" x-text="services.filter(s => s.is_for_orders).length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Servicios por Encargo</p>
            </div>

            {{-- Tarjeta 3: Servicios Inactivos --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-rose-100 relative overflow-hidden">
                <div class="absolute top-4 right-4 bg-rose-100 text-rose-700 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Pausados
                </div>
                <div class="w-12 h-12 bg-rose-100 text-rose-500 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                {{-- Contamos los que tienen is_active en false --}}
                <h3 class="text-3xl font-black text-rose-500" x-text="services.filter(s => !s.is_active).length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Servicios Inactivos</p>
            </div>
        </div>

        {{-- Tabla de Servicios --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-6">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-sm font-black">
                            <th class="p-5 pl-6">Servicio</th>
                            <th class="p-5">Descripción</th>
                            <th class="p-5">Modalidad</th>
                            <th class="p-5">Precio</th>
                            <th class="p-5 text-center">Estado</th>
                            <th class="p-5 pr-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="service in services" :key="service.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">

                                {{-- Nombre del Servicio --}}
                                <td class="p-5 pl-6">
                                    <div class="text-sm font-black text-[#1E55AA]" x-text="service.name"></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5" x-show="service.clave_prodserv" x-text="'SAT: ' + service.clave_prodserv"></div>
                                </td>

                                {{-- Descripción --}}
                                <td class="p-5 text-sm font-semibold text-slate-500 truncate max-w-[200px]" x-text="service.description || 'Sin descripción detallada'"></td>

                                {{-- Modalidad (Directo o Por Encargo) --}}
                                <td class="p-5">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-black tracking-wide"
                                          :class="service.is_for_orders ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-blue-50 text-blue-600 border border-blue-100'">
                                        <svg x-show="service.is_for_orders" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <svg x-show="!service.is_for_orders" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        <span x-text="service.is_for_orders ? 'Por Encargo' : 'Directo'"></span>
                                    </span>
                                </td>

                                {{-- Precio Unitario --}}
                                <td class="p-5 text-sm font-black text-slate-700" x-text="formatMoney(service.price)"></td>

                                {{-- Switch Interactivo de Estado --}}
                                <td class="p-5 text-center">
                                    <button @click="toggleServiceStatus(service)"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#1E55AA] focus:ring-offset-2"
                                        :class="service.is_active ? 'bg-emerald-500' : 'bg-slate-300'"
                                        role="switch" aria-checked="false">
                                        <span class="sr-only">Cambiar estado</span>
                                        {{-- El circulito que se mueve --}}
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="service.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </button>

                                    {{-- Texto de apoyo visual debajo del switch --}}
                                    <div class="text-[10px] font-black uppercase mt-1 tracking-wider"
                                         :class="service.is_active ? 'text-emerald-600' : 'text-slate-400'"
                                         x-text="service.is_active ? 'Activo' : 'Inactivo'">
                                    </div>
                                </td>

                                {{-- Acciones --}}
                                <td class="p-5 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-2">

                                        <button @click="openEditModal(service, 'services')" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-emerald-500 hover:bg-emerald-50 transition-colors" title="Editar Servicio">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                        <button @click="openDeleteModal(service, 'services')" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-rose-500 hover:bg-rose-50 transition-colors" title="Eliminar Servicio">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="services.length === 0" class="p-8 text-center text-slate-400 font-bold">
                    No hay servicios registrados en el catálogo.
                </div>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('js/pos.js') }}"></script>

@endsection
