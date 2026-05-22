@extends('layouts.app')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
@endsection

@section('content')
<style>
    [x-cloak] {
        display: none !important;
    }

    .font-nunito {
        font-family: 'Nunito', sans-serif;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: scale(0.95);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.2s ease-out forwards;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    .animate-float-delayed {
        animation: float 8s ease-in-out infinite;
        animation-delay: 2s;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<div x-data="clientManager" x-cloak class="font-nunito relative min-h-[85vh] bg-[#F4F8FC] text-[#1E55AA] selection:bg-[#FFE63C] selection:text-[#1E55AA] rounded-3xl p-4 md:p-6 2xl:p-10 z-10 overflow-hidden">

    {{-- Fondo Decorativo --}}
    <div class="absolute inset-0 -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-5%] w-[40vw] h-[40vw] rounded-full bg-[#1E55AA]/5 blur-[100px] animate-float"></div>
        <div class="absolute bottom-[-15%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#1E55AA]/10 blur-[120px] animate-float-delayed"></div>
        <div class="absolute top-[20%] right-[15%] w-[25vw] h-[25vw] rounded-full bg-[#FFE63C]/10 blur-[80px] animate-float" style="animation-duration: 7s;"></div>
    </div>

    {{-- Encabezado --}}
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between relative z-10">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-[#FFE63C] text-[#1E55AA] rounded-xl shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-black text-[#1E55AA]">
                Clientes y Suscripciones
            </h2>
        </div>
    </div>

    {{-- Controles Superiores --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="relative w-full max-w-md">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#1E55AA]/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" x-model="searchQuery" placeholder="Buscar cliente..."
                class="w-full rounded-2xl border-2 border-slate-100 bg-white py-3.5 pl-12 pr-4 text-[#1E55AA] font-bold shadow-sm focus:border-[#1E55AA] focus:ring-4 focus:ring-[#1E55AA]/10 outline-none transition-all placeholder:text-[#1E55AA]/40">
        </div>
        <button @click="openModal('add')" class="flex items-center justify-center gap-2 rounded-2xl bg-[#1E55AA] px-6 py-3.5 font-black text-white shadow-[0_8px_20px_rgba(30,85,170,0.2)] hover:bg-[#153e7d] hover:-translate-y-0.5 transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Cliente
        </button>
    </div>

    {{-- Tarjeta de la Tabla --}}
    <div class="bg-white rounded-3xl shadow-[0_10px_40px_rgba(30,85,170,0.08)] border-2 border-slate-100 relative z-10 overflow-hidden">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full table-auto text-left whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F4F8FC] border-b border-[#1E55AA]/10">
                        <th class="min-w-[250px] px-6 py-5 font-black text-[#1E55AA] uppercase tracking-wider text-sm">Cliente</th>
                        <th class="min-w-[200px] px-6 py-5 font-black text-[#1E55AA] uppercase tracking-wider text-sm">Plan y Vigencia</th>
                        <th class="px-6 py-5 font-black text-[#1E55AA] uppercase tracking-wider text-sm text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="client in filteredClients" :key="client.id">
                        <tr class="hover:bg-[#F4F8FC]/50 transition-colors duration-200">

                            {{-- Columna de Cliente --}}
                            <td class="px-6 py-4">
                                <h5 class="font-black text-lg text-[#1E55AA]" x-text="client.name"></h5>

                                {{-- Teléfono con icono --}}
                                <p class="font-bold text-[#1E55AA]/60 text-sm mt-0.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    <span x-text="client.phone || 'Sin teléfono'"></span>
                                </p>

                                {{-- Badge de Facturación --}}
                                <div x-show="client.rfc" class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-100 text-[10px] uppercase font-black text-emerald-600">
                                    <span>Factura:</span> <span x-text="client.rfc"></span>
                                </div>
                            </td>

                            {{-- Columna de Plan y Vigencia --}}
                            <td class="px-6 py-4">
                                <p class="font-black text-[#1E55AA]" x-text="client.subscription_name || 'Sin suscripción'"></p>
                                <p class="font-bold text-slate-500 text-sm mt-0.5"
                                    x-show="client.end_subscription"
                                    x-text="'Vence el ' + formatDate(client.end_subscription)">
                                </p>
                            </td>

                            {{-- Columna de Acciones --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openModal('view', client)" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-[#1E55AA] hover:bg-[#F4F8FC] transition-colors" title="Ver Detalles">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    </button>

                                    <button @click="openModal('edit', client)" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-emerald-500 hover:bg-emerald-50 transition-colors" title="Editar Cliente">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </button>

                                    <button @click="deleteClient(client.id)" class="p-2 rounded-xl text-[#1E55AA]/40 hover:text-rose-500 hover:bg-rose-50 transition-colors" title="Eliminar Cliente">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL --}}
    <div x-show="isModalOpen" class="fixed inset-0 z-99999 flex items-center justify-center p-4 bg-[#1E55AA]/40 backdrop-blur-sm" x-transition.opacity.duration.200ms style="display: none;">
        <div class="absolute inset-0" @click="closeModal()"></div>

        <div class="relative w-full max-w-2xl max-h-[80vh] overflow-y-auto custom-scrollbar bg-white rounded-3xl shadow-[0_20px_60px_rgba(30,85,170,0.15)] border-2 border-slate-100 p-8 animate-fade-in" @click.stop>

            <h3 class="text-2xl font-black text-[#1E55AA] mb-6" x-text="modalMode === 'add' ? 'Registrar Cliente' : (modalMode === 'edit' ? 'Editar Información' : 'Detalles del Cliente')"></h3>

            <form @submit.prevent="saveClient" class="space-y-6">

                {{-- 1. DATOS GENERALES --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-black text-slate-400 uppercase tracking-wider mb-2">Nombre del Cliente</label>
                        <input type="text" x-model="currentClient.name" :disabled="modalMode === 'view'" required
                            class="w-full rounded-2xl border-2 border-slate-100 bg-[#F4F8FC] py-3.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] focus:bg-white focus:ring-4 focus:ring-[#1E55AA]/10 disabled:opacity-60 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-400 uppercase tracking-wider mb-2">Teléfono</label>
                        <input type="text" x-model="currentClient.phone" :disabled="modalMode === 'view'"
                            :placeholder="modalMode === 'add' ? '427 123 4567' : 'No proporcionado'"
                            class="w-full rounded-2xl border-2 border-slate-100 bg-[#F4F8FC] py-3.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] focus:bg-white focus:ring-4 focus:ring-[#1E55AA]/10 disabled:opacity-60 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-400 uppercase tracking-wider mb-2">Correo Electrónico</label>
                        <input type="email" x-model="currentClient.email" :disabled="modalMode === 'view'"
                            :placeholder="modalMode === 'add' ? 'correo@ejemplo.com' : 'No proporcionado'"
                            class="w-full rounded-2xl border-2 border-slate-100 bg-[#F4F8FC] py-3.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] focus:bg-white focus:ring-4 focus:ring-[#1E55AA]/10 disabled:opacity-60 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-400 uppercase tracking-wider mb-2">Plan de Suscripción</label>
                        <select x-model="currentClient.subscription_id" :disabled="modalMode === 'view'" @change="actualizarFechaVencimiento()"
                            class="w-full rounded-2xl border-2 border-slate-100 bg-[#F4F8FC] py-3.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] focus:bg-white focus:ring-4 focus:ring-[#1E55AA]/10 disabled:opacity-60 transition-all appearance-none cursor-pointer">
                            <option value="">Ninguna</option>
                            <template x-for="plan in planesPOS" :key="plan.id">
                                <option :value="plan.id" x-text="plan.name"></option>
                            </template>
                        </select>
                    </div>

                    <div x-show="currentClient.subscription_id" class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-black text-slate-400 uppercase tracking-wider mb-2">Fecha de Inicio</label>
                        <input type="date" x-model="currentClient.start_subscription" :disabled="modalMode === 'view'"
                            class="w-full rounded-2xl border-2 border-slate-100 bg-[#F4F8FC] py-3.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] focus:bg-white focus:ring-4 focus:ring-[#1E55AA]/10 disabled:opacity-60 transition-all text-[#1E55AA]">
                    </div>
                </div>

                {{-- 2. DIRECCIÓN OPERATIVA (GENERAL) --}}
                <div class="mt-8 border-t border-slate-100 pt-6">
                    <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4">Dirección General / De Envío</h4>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="col-span-1 md:col-span-6">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">Calle</label>
                            <input type="text" x-model="currentClient.calle" :disabled="modalMode === 'view'"
                                :placeholder="modalMode === 'add' ? 'Ej. Av. Universidad' : 'No proporcionado'"
                                class="w-full rounded-xl border-2 border-slate-100 bg-[#F4F8FC] py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA]">
                        </div>

                        <div class="col-span-1 md:col-span-3">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">Nº Ext.</label>
                            <input type="text" x-model="currentClient.numero_exterior" :disabled="modalMode === 'view'"
                                :placeholder="modalMode === 'add' ? '123' : 'S/N'"
                                class="w-full rounded-xl border-2 border-slate-100 bg-[#F4F8FC] py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA]">
                        </div>

                        <div class="col-span-1 md:col-span-3">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">Nº Int.</label>
                            <input type="text" x-model="currentClient.numero_interior" :disabled="modalMode === 'view'"
                                :placeholder="modalMode === 'add' ? 'Apto 4' : 'N/A'"
                                class="w-full rounded-xl border-2 border-slate-100 bg-[#F4F8FC] py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA]">
                        </div>

                        <div class="col-span-1 md:col-span-5">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">Colonia</label>
                            <input type="text" x-model="currentClient.colonia" :disabled="modalMode === 'view'"
                                :placeholder="modalMode === 'add' ? 'Ej. Centro Histórico' : 'No proporcionado'"
                                class="w-full rounded-xl border-2 border-slate-100 bg-[#F4F8FC] py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA]">
                        </div>

                        <div class="col-span-1 md:col-span-4">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">Ciudad / Municipio</label>
                            <input type="text" x-model="currentClient.ciudad" :disabled="modalMode === 'view'"
                                :placeholder="modalMode === 'add' ? 'San Juan del Río' : 'No proporcionado'"
                                class="w-full rounded-xl border-2 border-slate-100 bg-[#F4F8FC] py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA]">
                        </div>

                        <div class="col-span-1 md:col-span-3">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">C.P.</label>
                            <input type="text" x-model="currentClient.codigo_postal" :disabled="modalMode === 'view'"
                                :placeholder="modalMode === 'add' ? '76800' : 'No proporcionado'" maxlength="5"
                                class="w-full rounded-xl border-2 border-slate-100 bg-[#F4F8FC] py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA]">
                        </div>

                        <div class="col-span-1 md:col-span-12">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">Estado</label>
                            <input type="text" x-model="currentClient.estado" :disabled="modalMode === 'view'"
                                :placeholder="modalMode === 'add' ? 'Querétaro' : 'No proporcionado'"
                                class="w-full rounded-xl border-2 border-slate-100 bg-[#F4F8FC] py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA]">
                        </div>
                    </div>
                </div>

                {{-- 3. DATOS FISCALES (CFDI) --}}
                <div class="mt-8">
                    {{-- Activar/Desactivar Facturación --}}
                    <div class="flex items-center gap-3 bg-indigo-50/50 p-4 rounded-2xl border-2 border-indigo-100/50 transition-colors hover:border-indigo-200">
                        <input type="checkbox" id="wantsBilling" x-model="currentClient.wantsBilling" :disabled="modalMode === 'view'"
                            class="w-5 h-5 text-indigo-600 rounded-md border-slate-300 focus:ring-indigo-600 cursor-pointer">
                        <label for="wantsBilling" class="font-black text-indigo-900 cursor-pointer select-none">El cliente requiere facturar (CFDI 4.0)</label>
                    </div>

                    {{-- Bloque Fiscal Colapsable --}}
                    <div x-show="currentClient.wantsBilling" x-collapse class="mt-4">
                        <div class="bg-white p-6 rounded-2xl border-2 border-indigo-50 shadow-sm">

                            {{-- Info de la Empresa --}}
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
                                <div class="col-span-1 md:col-span-8">
                                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">Razón Social</label>
                                    <input type="text" x-model="currentClient.razon_social" :disabled="modalMode === 'view'"
                                        :placeholder="modalMode === 'add' ? 'NOMBRE APELLIDO / EMPRESA' : 'No proporcionado'"
                                        class="w-full rounded-xl border-2 border-slate-200 bg-white py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-indigo-400 uppercase">
                                </div>

                                <div class="col-span-1 md:col-span-4">
                                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">RFC</label>
                                    <input type="text" x-model="currentClient.rfc" :disabled="modalMode === 'view'"
                                        :placeholder="modalMode === 'add' ? 'XAXX010101000' : 'No proporcionado'" maxlength="13"
                                        class="w-full rounded-xl border-2 border-slate-200 bg-white py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-indigo-400 uppercase">
                                </div>

                                <div class="col-span-1 md:col-span-12">
                                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-wider mb-1">Régimen Fiscal</label>
                                    <select x-model="currentClient.regimen_fiscal" :disabled="modalMode === 'view'"
                                        class="w-full rounded-xl border-2 border-slate-200 bg-white py-2.5 px-4 font-bold text-[#1E55AA] outline-none focus:border-indigo-400 appearance-none cursor-pointer">
                                        <option value="">Seleccionar...</option>
                                        <option value="616">616 - Sin obligaciones fiscales</option>
                                        <option value="601">601 - General de Ley PM</option>
                                        <option value="605">605 - Sueldos y Salarios</option>
                                        <option value="626">626 - RESICO</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Checkbox Misma Dirección --}}
                            <div class="border-t border-slate-100 pt-4 mb-4">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="same_billing_address" x-model="currentClient.same_billing_address" :disabled="modalMode === 'view'"
                                        class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-600 cursor-pointer">
                                    <label for="same_billing_address" class="text-xs font-black text-slate-600 cursor-pointer select-none">La dirección fiscal es igual a la operativa (arriba)</label>
                                </div>
                            </div>

                            {{-- Formulario de Dirección Fiscal Independiente --}}
                            <div x-show="!currentClient.same_billing_address" x-collapse>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-indigo-50/30 p-4 rounded-xl border border-indigo-50">

                                    <div class="col-span-1 md:col-span-6">
                                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-wider mb-1">Calle Fiscal</label>
                                        <input type="text" x-model="currentClient.fiscal_calle" :disabled="modalMode === 'view'"
                                            class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm font-bold text-[#1E55AA] outline-none focus:border-indigo-400">
                                    </div>

                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-wider mb-1">Nº Ext.</label>
                                        <input type="text" x-model="currentClient.fiscal_numero_exterior" :disabled="modalMode === 'view'"
                                            class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm font-bold text-[#1E55AA] outline-none focus:border-indigo-400">
                                    </div>

                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-wider mb-1">Nº Int.</label>
                                        <input type="text" x-model="currentClient.fiscal_numero_interior" :disabled="modalMode === 'view'"
                                            class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm font-bold text-[#1E55AA] outline-none focus:border-indigo-400">
                                    </div>

                                    <div class="col-span-1 md:col-span-5">
                                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-wider mb-1">Colonia Fiscal</label>
                                        <input type="text" x-model="currentClient.fiscal_colonia" :disabled="modalMode === 'view'"
                                            class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm font-bold text-[#1E55AA] outline-none focus:border-indigo-400">
                                    </div>

                                    <div class="col-span-1 md:col-span-4">
                                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-wider mb-1">Ciudad / Municipio</label>
                                        <input type="text" x-model="currentClient.fiscal_ciudad" :disabled="modalMode === 'view'"
                                            class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm font-bold text-[#1E55AA] outline-none focus:border-indigo-400">
                                    </div>

                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-wider mb-1">C.P. Fiscal *</label>
                                        <input type="text" x-model="currentClient.fiscal_codigo_postal" :disabled="modalMode === 'view'" maxlength="5"
                                            class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm font-bold text-[#1E55AA] outline-none focus:border-indigo-400">
                                    </div>

                                    <div class="col-span-1 md:col-span-12">
                                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-wider mb-1">Estado Fiscal</label>
                                        <input type="text" x-model="currentClient.fiscal_estado" :disabled="modalMode === 'view'"
                                            class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm font-bold text-[#1E55AA] outline-none focus:border-indigo-400">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botones Inferiores --}}
                <div class="flex justify-end gap-3 pt-6 mt-2 border-t border-slate-100">
                    <button type="button" @click="closeModal" class="rounded-2xl bg-[#F4F8FC] px-8 py-3.5 font-black text-[#1E55AA]/60 hover:bg-slate-200 hover:text-[#1E55AA] transition-all">
                        Cancelar
                    </button>
                    <button x-show="modalMode !== 'view'" type="submit" class="rounded-2xl bg-[#1E55AA] px-10 py-3.5 font-black text-white shadow-[0_8px_20px_rgba(30,85,170,0.2)] hover:bg-[#153e7d] hover:-translate-y-0.5 transition-all"
                        x-text="modalMode === 'add' ? 'Guardar Cliente' : 'Actualizar Cliente'">
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script src="{{ asset('js/clientes.js') }}"></script>

@endsection
