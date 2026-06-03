@extends('layouts.app')

@section('content')
{{--
  CAMBIO 1: Inicialización de Alpine.
  Dejamos el array de servicios vacío [] y pasamos los datos a la segunda posición (suppliesDb).
--}}
<div x-data='posSystem([], @json($supplies ?? []), [], [])' class="p-6 bg-[#F4F8FC] min-h-screen font-nunito">

    {{-- MODALES --}}
    @include('pos.modals')

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-8 max-w-[1600px] mx-auto">
        <h1 class="text-2xl font-black text-[#1E55AA]">Inventario de Productos</h1>
        {{-- CAMBIO 2: Pasamos 'supplies' al modal --}}
        <button @click="openAddModal('supplies')"
            class="bg-[#1E55AA] hover:bg-[#153e7d] text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-[#1E55AA]/20 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Registrar Producto
        </button>
    </div>

    <div class="max-w-[1600px] mx-auto space-y-6">
        {{-- Tarjetas de Métricas adaptadas a Productos --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Tarjeta 1: Total de Productos --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="w-12 h-12 bg-blue-50 text-[#1E55AA] rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                {{-- Iteramos sobre 'supplies' en vez de 'services' --}}
                <h3 class="text-3xl font-black text-slate-800" x-text="supplies.length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Total de Productos</p>
            </div>

            {{-- Tarjeta 2: Productos con Bajo Stock (Ejemplo de nueva métrica) --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-amber-100 relative overflow-hidden">
                <div class="absolute top-4 right-4 bg-amber-100 text-amber-700 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Alerta
                </div>
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                {{-- Contamos productos con stock menor o igual a 5 --}}
                <h3 class="text-3xl font-black text-amber-600" x-text="supplies.filter(s => s.stock <= 5).length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Bajo Stock</p>
            </div>

            {{-- Tarjeta 3: Productos Inactivos --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-rose-100 relative overflow-hidden">
                <div class="w-12 h-12 bg-rose-100 text-rose-500 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <h3 class="text-3xl font-black text-rose-500" x-text="supplies.filter(s => !s.is_active).length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Productos Inactivos</p>
            </div>
        </div>

        {{-- Tabla de Productos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-6">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-sm font-black">
                            <th class="p-5 pl-6">Producto</th>
                            <th class="p-5">Stock</th>
                            <th class="p-5">Precio</th>
                            <th class="p-5 text-center">Estado</th>
                            <th class="p-5 pr-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {{-- CAMBIO 3: Iteramos sobre supplies --}}
                        <template x-for="item in supplies" :key="item.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">

                                {{-- Nombre --}}
                                <td class="p-5 pl-6">
                                    <div class="text-sm font-black text-[#1E55AA]" x-text="item.name"></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5" x-show="item.clave_prodserv" x-text="'SAT: ' + item.clave_prodserv"></div>
                                </td>

                                {{-- CAMBIO 4: Mostrar Stock y Unidad en lugar de Modalidad/Descripción --}}
                                <td class="p-5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-black" :class="item.stock <= 5 ? 'text-amber-500' : 'text-slate-700'" x-text="item.stock || '0'"></span>
                                        <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded" x-text="item.unit || 'N/A'"></span>
                                    </div>
                                </td>

                                {{-- Precio Unitario --}}
                                <td class="p-5 text-sm font-black text-[#1E55AA]" x-text="formatMoney(item.price)"></td>

                                {{-- Switch Interactivo de Estado (Reutilizamos la misma función de pos.js) --}}
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
                                        {{-- CAMBIO 5: Pasamos 'supplies' como categoría --}}
                                        <button @click="openViewModal(item, 'supplies')" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-[#1E55AA] hover:bg-[#F4F8FC] transition-colors" title="Ver Detalles">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>
                                        <button @click="openEditModal(item, 'supplies')" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-emerald-500 hover:bg-emerald-50 transition-colors" title="Editar Producto">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                        <button @click="openDeleteModal(item, 'supplies')" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-rose-500 hover:bg-rose-50 transition-colors" title="Eliminar Producto">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="supplies.length === 0" class="p-8 text-center text-slate-400 font-bold">
                    No hay productos registrados en el catálogo.
                </div>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('js/pos.js') }}"></script>
@endsection
