@extends('layouts.app')

@section('content')
{{--
  CAMBIO 1: Inicialización de Alpine.
  Pasamos el JSON de suscripciones a la 3ra posición del arreglo de posSystem.
--}}
<div x-data='posSystem([], [], @json($subscriptions ?? []), [])' class="p-6 bg-[#F4F8FC] min-h-screen font-nunito">

    {{-- MODALES --}}
    @include('pos.modals')

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-8 max-w-[1600px] mx-auto">
        <h1 class="text-2xl font-black text-[#1E55AA]">Planes de Suscripción</h1>
        {{-- CAMBIO 2: Pasamos 'subscriptions' a la función del modal --}}
        <button @click="openAddModal('subscriptions')"
            class="bg-[#1E55AA] hover:bg-[#153e7d] text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-[#1E55AA]/20 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Registrar Suscripción
        </button>
    </div>

    <div class="max-w-[1600px] mx-auto space-y-6">
        {{-- Tarjetas de Métricas adaptadas a Suscripciones --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Tarjeta 1: Total de Planes --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="w-12 h-12 bg-blue-50 text-[#1E55AA] rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-3xl font-black text-slate-800" x-text="subscriptions.length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Total de Planes</p>
            </div>

            {{-- Tarjeta 2: Clientes Suscritos --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-emerald-100 relative overflow-hidden">
                <div class="absolute top-4 right-4 bg-emerald-100 text-emerald-700 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Activos
                </div>
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                    {{-- Nuevo ícono de Grupo de Usuarios --}}
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                {{-- Imprimimos el valor calculado desde Laravel --}}
                <h3 class="text-3xl font-black text-emerald-600">{{ $totalSubscribedClients ?? 0 }}</h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Clientes con Suscripción</p>
            </div>

            {{-- Tarjeta 3: Planes Inactivos --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-rose-100 relative overflow-hidden">
                <div class="w-12 h-12 bg-rose-100 text-rose-500 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <h3 class="text-3xl font-black text-rose-500" x-text="subscriptions.filter(s => !s.is_active).length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Planes Inactivos</p>
            </div>
        </div>

        {{-- Tabla de Suscripciones --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-6">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-sm font-black">
                            <th class="p-5 pl-6">Plan de Suscripción</th>
                            <th class="p-5">Duración</th>
                            <th class="p-5">Precio</th>
                            <th class="p-5 text-center">Estado</th>
                            <th class="p-5 pr-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {{-- CAMBIO 3: Iteramos sobre el arreglo subscriptions --}}
                        <template x-for="item in subscriptions" :key="item.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">

                                {{-- Nombre y Descripción --}}
                                <td class="p-5 pl-6">
                                    <div class="text-sm font-black text-[#1E55AA]" x-text="item.name"></div>
                                    <div class="text-[11px] font-bold text-slate-400 mt-0.5 max-w-[250px] truncate" x-show="item.description" x-text="item.description"></div>
                                </td>

                                {{-- CAMBIO 4: Mostramos los meses de duración --}}
                                <td class="p-5">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-black tracking-wide bg-indigo-50 text-indigo-600 border border-indigo-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span x-text="item.duration_months + (item.duration_months == 1 ? ' Mes' : ' Meses')"></span>
                                    </div>
                                </td>

                                {{-- Precio --}}
                                <td class="p-5 text-sm font-black text-[#1E55AA]" x-text="formatMoney(item.price)"></td>

                                {{-- Switch Interactivo de Estado --}}
                                <td class="p-5 text-center">
                                    <button @click="toggleServiceStatus(item)"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="item.is_active ? 'bg-emerald-500' : 'bg-slate-300'" role="switch">
                                        <span class="sr-only">Cambiar estado</span>
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200 ease-in-out"
                                            :class="item.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </button>
                                    <div class="text-[10px] font-black uppercase mt-1 tracking-wider"
                                         :class="item.is_active ? 'text-emerald-600' : 'text-slate-400'"
                                         x-text="item.is_active ? 'Activo' : 'Inactivo'">
                                    </div>
                                </td>

                                {{-- Acciones --}}
                                <td class="p-5 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- CAMBIO 5: Categoría 'subscriptions' para el modal --}}
                                        <button @click="openEditModal(item, 'subscriptions')" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-emerald-500 hover:bg-emerald-50 transition-colors" title="Editar Plan">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                        <button @click="openDeleteModal(item, 'subscriptions')" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-rose-500 hover:bg-rose-50 transition-colors" title="Eliminar Plan">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="subscriptions.length === 0" class="p-8 text-center text-slate-400 font-bold">
                    No hay planes de suscripción registrados en el catálogo.
                </div>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('js/pos.js') }}"></script>

@endsection

