@extends('layouts.app')

@section('content')

<style>
    @media screen {
        #zona-impresion { display: none !important; }
    }
    @media print {
        body * { visibility: hidden; }
        #zona-impresion, #zona-impresion * { visibility: visible; }
        #zona-impresion {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 58mm !important;
            margin: 0 !important;
            padding: 5px !important;
        }
        @page { margin: 0; }
    }
</style>

<div x-data="historialSystem" class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 font-nunito relative">

    {{-- TICKET OCULTO PARA IMPRESIÓN --}}
    <div id="zona-impresion">
        <template x-if="ticketAImprimir">
            <div style="font-family: 'Courier New', monospace; color: #000; background: #fff;">
                <div style="text-align: center; margin-bottom: 8px;">
                    
                    {{-- Logo en impresión --}}
                    <template x-if="$store.ticketConfig.logo">
                        <img :src="$store.ticketConfig.logo" style="max-height: 60px; margin: 0 auto 5px auto; display: block; max-width: 100%; object-fit: contain;">
                    </template>

                    <h1 style="font-size: 18px; margin: 0; font-weight: 900; letter-spacing: 1px;" x-text="$store.ticketConfig.titulo"></h1>
                    
                    {{-- 3 Campos Extra --}}
                    <p style="margin: 2px 0; font-size: 10px;" x-show="$store.ticketConfig.direccion" x-text="$store.ticketConfig.direccion"></p>
                    <p style="margin: 2px 0; font-size: 10px;" x-show="$store.ticketConfig.telefono" x-text="$store.ticketConfig.telefono"></p>
                    <p style="margin: 2px 0; font-size: 10px;" x-show="$store.ticketConfig.extra" x-text="$store.ticketConfig.extra"></p>
                    
                    <p style="margin: 4px 0 2px 0; font-size: 10px; border-top: 1px dotted #ccc; padding-top: 2px;">Ticket de Venta</p>
                </div>
                <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>
                <div style="font-size: 10px; margin: 4px 0;">
                    <div style="display: flex; justify-content: space-between;"><span>Folio:</span> <strong x-text="ticketAImprimir.folio"></strong></div>
                    <div style="display: flex; justify-content: space-between;"><span>Fecha:</span> <span x-text="ticketAImprimir.fecha"></span></div>
                    <div style="display: flex; justify-content: space-between;"><span>Atendió:</span> <span class="uppercase" x-text="ticketAImprimir.nombre_vendedor"></span></div>
                </div>
                <table style="width: 100%; border-collapse: collapse; margin-top: 8px;">
                    <thead>
                        <tr>
                            <th style="text-align: left; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; font-size: 10px;">CANT/DESC</th>
                            <th style="text-align: right; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; font-size: 10px;">IMPORTE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in getDetalles(ticketAImprimir)" :key="item.id">
                            <tr>
                                <td style="padding: 4px 0; text-align: left; font-size: 10px; border-bottom: 1px dotted #ccc;">
                                    <div style="font-weight: bold; text-transform: uppercase;" x-text="item.name_snapshot"></div>
                                    <div style="color: #333;" x-text="item.quantity + ' x $' + parseFloat(item.price_snapshot).toFixed(2)"></div>
                                </td>
                                <td style="padding: 4px 0; text-align: right; font-size: 11px; font-weight: bold; vertical-align: bottom; border-bottom: 1px dotted #ccc;" x-text="'$' + (item.price * item.quantity).toFixed(2)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div style="margin-top: 10px; border-top: 1px dashed #000; padding-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 14px; font-weight: 900;">TOTAL:</span>
                    <span style="font-size: 16px; font-weight: 900;" x-text="'$' + parseFloat(ticketAImprimir.total).toFixed(2)"></span>
                </div>
                <div style="text-align: center; font-size: 10px; margin-top: 15px; white-space: pre-line;" x-text="$store.ticketConfig.mensaje"></div>
            </div>
        </template>
    </div>

    {{-- MODAL DE CONFIRMACIÓN --}}
    <div x-show="confirmModal.open" class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-[#1E55AA]/40 backdrop-blur-sm" x-transition.opacity.duration.200ms>
        <div class="absolute inset-0" @click="cerrarConfirmacion()"></div>
        <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in border border-[#1E55AA]/10">
            <div class="p-8 text-center bg-white">
                <div class="w-16 h-16 bg-[#F4F8FC] rounded-full flex items-center justify-center mx-auto mb-4 text-[#1E55AA] border-2 border-[#1E55AA]/10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h2 class="text-xl font-black text-[#1E55AA] mb-2" x-text="confirmModal.title"></h2>
                <p class="text-[#1E55AA]/60 font-bold mb-8 text-sm" x-text="confirmModal.message"></p>
                <div class="grid grid-cols-2 gap-3">
                    <button @click="cerrarConfirmacion()" class="py-3 px-4 bg-[#F4F8FC] text-[#1E55AA] font-bold rounded-xl hover:bg-slate-100 transition-colors">Cancelar</button>
                    <button @click="ejecutarConfirmacion()" class="py-3 px-4 bg-rose-500 text-white font-bold rounded-xl hover:bg-rose-600 transition-colors shadow-sm">Sí, eliminar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-3xl font-black text-[#1E55AA] tracking-tight">Historial de Ventas</h2>
        
        @if(auth()->user()->isAdmin())
            <button @click="borrarHistorialFiltrado()" x-show="ventasFiltradas.length > 0"
                    class="inline-flex items-center gap-2 bg-white border-2 border-rose-100 text-rose-500 px-4 py-2 rounded-xl text-sm font-black hover:bg-rose-500 hover:border-rose-500 hover:text-white transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span x-text="tipoFiltro === 'dia' ? 'Limpiar ventas del día' : (tipoFiltro === 'mes' ? 'Limpiar ventas del mes' : 'Vaciar todo el historial')"></span>
            </button>
        @endif
    </div>

    {{-- FILTROS --}}
    <div class="mb-6 bg-white border-2 border-slate-100 rounded-2xl p-4 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Filtrar por:</span>
            <select x-model="tipoFiltro" class="bg-[#F4F8FC] border-2 border-[#1E55AA]/10 text-[#1E55AA] text-sm font-bold rounded-xl px-4 py-2 focus:outline-none focus:border-[#1E55AA] cursor-pointer">
                <option value="todas">Historial Completo</option>
                <option value="mes">Mes Específico</option>
                <option value="dia">Día Exacto</option>
                <option value="folio">Folio</option>
            </select>
            <input type="text" x-show="tipoFiltro === 'mes'" x-ref="filtroMes" class="bg-[#FFE63C]/10 border-2 border-[#FFE63C]/50 text-[#1E55AA] text-sm font-bold rounded-xl px-4 py-2 focus:outline-none focus:border-[#FFE63C] cursor-pointer" placeholder="Seleccionar mes">
            <input type="text" x-show="tipoFiltro === 'dia'" x-ref="filtroDia" class="bg-emerald-50 border-2 border-emerald-200 text-emerald-700 text-sm font-bold rounded-xl px-4 py-2 focus:outline-none focus:border-emerald-400 cursor-pointer" placeholder="Seleccionar día">
            <input type="text" x-show="tipoFiltro === 'folio'" x-model.debounce.500ms="valorFiltro" class="bg-rose-50 border-2 border-rose-100 text-rose-600 text-sm font-bold rounded-xl px-4 py-2 focus:outline-none focus:border-rose-400" placeholder="Ej. BK-0001">
        </div>

        <div class="inline-flex flex-col items-end rounded-xl bg-emerald-50 px-5 py-2 border border-emerald-200">
            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest" x-text="tipoFiltro === 'dia' ? 'Total del Día' : (tipoFiltro === 'mes' ? 'Total del Mes' : 'Total Histórico')"></span>
            <span class="text-xl font-black text-emerald-700" x-text="formatMoney(totalFiltro)"></span>
        </div>
    </div>

    {{-- GRID PRINCIPAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- TABLA DE HISTORIAL --}}
        <div class="lg:col-span-8">
            <div class="rounded-2xl border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-sm sm:px-7.5 xl:pb-1 flex flex-col justify-between min-h-[500px]">
                <div class="max-w-full overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-2 text-left text-[#1E55AA]">
                                <th class="py-4 px-4 font-black">Folio</th>
                                <th class="py-4 px-4 font-black">Fecha</th>
                                <th class="py-4 px-4 font-black">Atendió</th>
                                <th class="py-4 px-4 font-black">Servicios</th>
                                <th class="py-4 px-4 font-black text-right">Total</th>
                                @if(auth()->user()->isAdmin())
                                    <th class="py-4 px-4 font-black text-center">Acción</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="ventasFiltradas.length === 0">
                                <tr>
                                    <td colspan="{{ auth()->user()->isAdmin() ? '6' : '5' }}" class="py-12 text-center text-slate-400 font-bold text-lg"
                                        x-text="tipoFiltro === 'todas' ? 'Aún no hay ventas registradas.' : 'No hay ventas en esta fecha.'"></td>
                                </tr>
                            </template>
                            <template x-for="venta in ventasFiltradas" :key="venta.id">
                                <tr class="border-b border-[#eee] transition-colors cursor-pointer"
                                    @click="verTicket(venta)"
                                    :class="ticketActivo && ticketActivo.id === venta.id ? 'bg-[#1E55AA]/5 border-l-4 border-l-[#1E55AA]' : 'hover:bg-slate-50 border-l-4 border-l-transparent'">
                                    <td class="py-4 px-4"><span class="text-[#1E55AA] font-black" x-text="venta.folio"></span></td>
                                    <td class="py-4 px-4 text-xs font-bold text-slate-500" x-text="venta.fecha"></td>
                                    
                                    {{-- COLUMNA DE VENDEDOR --}}
                                    <td class="py-4 px-4">
                                        <span class="bg-[#F4F8FC] border border-[#1E55AA]/10 text-[#1E55AA] text-[10px] uppercase font-black px-2 py-1 rounded-md" 
                                              x-text="venta.nombre_vendedor"></span>
                                    </td>

                                    <td class="py-4 px-4">
                                        <div class="flex flex-col gap-1">
                                            <template x-for="item in getDetalles(venta)" :key="item.id">
                                                <div class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                                                    <div class="w-1 h-1 rounded-full bg-[#FFE63C]"></div>
                                                    <span x-text="item.name_snapshot"></span>
                                                    <span class="text-[#1E55AA] bg-white border border-slate-200 px-1 rounded" x-text="'x' + item.quantity"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-black text-[#1E55AA] text-right text-lg" x-text="formatMoney(venta.total)"></td>
                                    
                                    @if(auth()->user()->isAdmin())
                                        <td class="py-4 px-4 text-center">
                                            <button @click.stop="borrarVenta(venta.id)" class="inline-flex items-center justify-center rounded-xl bg-rose-50 border-2 border-rose-100 py-1.5 px-3 text-xs font-bold text-rose-500 hover:bg-rose-500 hover:border-rose-500 hover:text-white transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- PAGINACIÓN --}}
                <div class="flex items-center justify-between border-t border-slate-100 bg-white pt-4 pb-2 mt-4" x-show="pagination.last_page > 1">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-bold">
                                Página <span class="font-black text-[#1E55AA]" x-text="pagination.current_page"></span> de <span class="font-black text-[#1E55AA]" x-text="pagination.last_page"></span>
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-xl shadow-sm" aria-label="Pagination">
                                <template x-for="(link, index) in pagination.links" :key="index">
                                    <button
                                        @click="if(link.url) cargarDatos(link.url)"
                                        x-html="link.label"
                                        :disabled="!link.url"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-bold transition-colors focus:z-20 first:rounded-l-xl last:rounded-r-xl border"
                                        :class="[
                                            link.active ? 'z-10 bg-[#1E55AA] text-white border-[#1E55AA] focus-visible:outline-[#1E55AA]' : 'text-[#1E55AA] border-slate-200 hover:bg-[#F4F8FC] focus:outline-offset-0',
                                            !link.url ? 'opacity-40 cursor-not-allowed bg-slate-50 text-slate-400 hover:bg-slate-50' : ''
                                        ]"
                                    ></button>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- VISUALIZADOR DE TICKET CON EDICIÓN --}}
        <div class="lg:col-span-4 relative">
            <div class="sticky top-8 bg-white border-2 border-slate-100 rounded-[2rem] shadow-sm flex flex-col overflow-hidden h-[calc(100vh-8rem)]">

                <div class="p-4 bg-[#F4F8FC] border-b border-slate-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-[#1E55AA] text-white rounded-xl shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="font-black text-[#1E55AA]">Visualizador</h3>
                    </div>
                    
                    @if(auth()->user()->isAdmin())
                        <button @click="$store.ticketConfig.toggleEdit()" 
                                class="flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-lg transition-colors shadow-sm border"
                                :class="$store.ticketConfig.isEditing ? 'bg-amber-100 text-amber-700 border-amber-300' : 'bg-white text-[#1E55AA] border-slate-200 hover:bg-slate-50'">
                            <template x-if="!$store.ticketConfig.isEditing">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </template>
                            <template x-if="$store.ticketConfig.isEditing">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <span x-text="$store.ticketConfig.isEditing ? 'Guardar Diseño' : 'Editar Diseño'"></span>
                        </button>
                    @endif
                </div>

                <div x-show="!ticketActivo" class="flex-1 flex flex-col items-center justify-center text-center p-8 opacity-60">
                    <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                    <p class="font-bold text-slate-500">Haz clic en una venta para previsualizar el ticket aquí.</p>
                </div>

                <div x-show="ticketActivo" class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-slate-50 flex justify-center items-start">
                    
                    {{-- CONTENEDOR DEL TICKET (Editable) --}}
                    <div class="bg-white p-4 shadow-sm w-full max-w-[250px] font-mono text-black border border-slate-200 transition-all duration-300 flex flex-col"
                         :class="$store.ticketConfig.isEditing ? 'ring-4 ring-amber-200' : ''">
                        
                        <div class="text-center mb-3 flex flex-col items-center w-full">
                            
                            {{-- LOGO --}}
                            <template x-if="$store.ticketConfig.logo">
                                <div class="relative mb-2 inline-block">
                                    <img :src="$store.ticketConfig.logo" class="max-h-16 max-w-[150px] object-contain rounded">
                                    <button x-show="$store.ticketConfig.isEditing" @click="$store.ticketConfig.removeLogo()" class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full p-1 shadow-md hover:bg-rose-600 transition-transform hover:scale-110" title="Eliminar Logo">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>

                            {{-- SUBIR LOGO (Solo en edición) --}}
                            <div x-show="$store.ticketConfig.isEditing && !$store.ticketConfig.logo" class="mb-3 w-full">
                                <label class="cursor-pointer bg-amber-50 border border-dashed border-amber-400 rounded-lg p-3 flex flex-col items-center justify-center text-amber-700 text-[10px] hover:bg-amber-100 transition-colors">
                                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span class="font-bold">Subir Logo (Opcional)</span>
                                    <input type="file" class="hidden" accept="image/*" @change="$store.ticketConfig.handleLogoUpload($event)">
                                </label>
                            </div>

                            {{-- TITULO --}}
                            <h1 x-show="!$store.ticketConfig.isEditing" class="text-lg font-black uppercase tracking-widest mb-1 leading-tight" x-text="$store.ticketConfig.titulo"></h1>
                            <input x-show="$store.ticketConfig.isEditing" type="text" x-model="$store.ticketConfig.titulo" class="w-full text-center text-base font-black uppercase tracking-widest bg-amber-50 border border-dashed border-amber-400 rounded outline-none p-1 mb-1 focus:ring-2 focus:ring-amber-300" placeholder="Nombre Negocio">
                            
                            {{-- LOS 3 CAMPOS EXTRA --}}
                            <p x-show="!$store.ticketConfig.isEditing && $store.ticketConfig.direccion" class="text-[10px] mb-0.5 leading-tight" x-text="$store.ticketConfig.direccion"></p>
                            <input x-show="$store.ticketConfig.isEditing" type="text" x-model="$store.ticketConfig.direccion" class="w-full text-center text-[10px] bg-amber-50 border border-dashed border-amber-400 rounded outline-none p-1 mb-1 focus:ring-2 focus:ring-amber-300" placeholder="Dirección">
                            
                            <p x-show="!$store.ticketConfig.isEditing && $store.ticketConfig.telefono" class="text-[10px] mb-0.5 leading-tight" x-text="$store.ticketConfig.telefono"></p>
                            <input x-show="$store.ticketConfig.isEditing" type="text" x-model="$store.ticketConfig.telefono" class="w-full text-center text-[10px] bg-amber-50 border border-dashed border-amber-400 rounded outline-none p-1 mb-1 focus:ring-2 focus:ring-amber-300" placeholder="Teléfono">
                            
                            <p x-show="!$store.ticketConfig.isEditing && $store.ticketConfig.extra" class="text-[10px] mb-1 leading-tight" x-text="$store.ticketConfig.extra"></p>
                            <input x-show="$store.ticketConfig.isEditing" type="text" x-model="$store.ticketConfig.extra" class="w-full text-center text-[10px] bg-amber-50 border border-dashed border-amber-400 rounded outline-none p-1 mb-1 focus:ring-2 focus:ring-amber-300" placeholder="RFC o Info Extra">
                            
                            <p class="text-[10px] mt-1 border-t border-slate-200 w-full pt-1">Ticket de Venta</p>
                        </div>

                        <div class="border-t border-dashed border-slate-400 my-2"></div>
                        <div class="text-[10px] space-y-1 mb-3">
                            <div class="flex justify-between"><span>Folio:</span> <strong x-text="ticketActivo?.folio"></strong></div>
                            <div class="flex justify-between"><span>Fecha:</span> <span x-text="ticketActivo?.fecha"></span></div>
                            <div class="flex justify-between"><span>Atendió:</span> <span class="uppercase font-bold" x-text="ticketActivo?.nombre_vendedor"></span></div>
                        </div>
                        <table class="w-full text-[10px]">
                            <thead>
                                <tr class="border-y border-dashed border-slate-400">
                                    <th class="py-1.5 text-left font-bold">CANT/DESC</th>
                                    <th class="py-1.5 text-right font-bold">IMPORTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in getDetalles(ticketActivo)" :key="item.id">
                                    <tr class="border-b border-dotted border-slate-200">
                                        <td class="py-2 text-left">
                                            <div class="font-bold uppercase" x-text="item.name_snapshot"></div>
                                            <div class="text-slate-500 mt-0.5" x-text="item.quantity + ' x ' + formatMoney(item.price_snapshot)"></div>
                                        </td>
                                        <td class="py-2 text-right font-bold align-bottom" x-text="formatMoney(item.price_snapshot * item.quantity)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="border-t border-dashed border-slate-400 mt-3 pt-3 flex justify-between items-center">
                            <span class="font-bold text-xs">TOTAL:</span>
                            <span class="font-black text-sm" x-text="formatMoney(ticketActivo?.total || 0)"></span>
                        </div>
                        
                        <div class="text-center text-[9px] mt-6 text-slate-600">
                            {{-- Mensaje Final Editable --}}
                            <div x-show="!$store.ticketConfig.isEditing" class="whitespace-pre-line" x-text="$store.ticketConfig.mensaje"></div>
                            <textarea x-show="$store.ticketConfig.isEditing" x-model="$store.ticketConfig.mensaje" rows="3" class="w-full text-center text-[9px] bg-amber-50 border border-dashed border-amber-400 rounded outline-none p-1 resize-none focus:ring-2 focus:ring-amber-300 transition-all" placeholder="Mensaje de agradecimiento"></textarea>
                        </div>
                    </div>
                </div>

                <div x-show="ticketActivo" class="p-4 bg-white border-t border-slate-100">
                    <button @click="imprimirDirecto(ticketActivo)" class="w-full py-3 bg-[#1E55AA] text-white font-black rounded-xl hover:bg-[#153e7d] transition-all shadow-md flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Imprimir Ahora
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/historial.js') }}"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('ticketConfig', {
            logo: localStorage.getItem('bk_ticket_logo') || '',
            titulo: localStorage.getItem('bk_ticket_titulo') || 'BUNNYKLIN',
            direccion: localStorage.getItem('bk_ticket_direccion') || '',
            telefono: localStorage.getItem('bk_ticket_telefono') || '',
            extra: localStorage.getItem('bk_ticket_extra') || '',
            mensaje: localStorage.getItem('bk_ticket_mensaje') || '¡Gracias por su compra!\n*** CONSERVE ESTE TICKET ***',
            isEditing: false, 
            
            toggleEdit() {
                this.isEditing = !this.isEditing;
                
                // Si apaga edición, guarda todo
                if (!this.isEditing) {
                    localStorage.setItem('bk_ticket_titulo', this.titulo);
                    localStorage.setItem('bk_ticket_direccion', this.direccion);
                    localStorage.setItem('bk_ticket_telefono', this.telefono);
                    localStorage.setItem('bk_ticket_extra', this.extra);
                    localStorage.setItem('bk_ticket_mensaje', this.mensaje);
                    localStorage.setItem('bk_ticket_logo', this.logo);
                }
            },

            // Función para convertir la imagen a código base64 y guardarla localmente
            handleLogoUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.logo = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            // Función para borrar el logo
            removeLogo() {
                this.logo = '';
                localStorage.removeItem('bk_ticket_logo');
            }
        });
    });
</script>

@endsection