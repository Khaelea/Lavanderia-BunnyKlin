@extends('layouts.app')

@section('content')
<div x-data="facturacionSystem()" class="p-6 bg-[#F4F8FC] min-h-screen font-nunito">
    
    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-8 max-w-[1600px] mx-auto">
        <div>
            <h1 class="text-2xl font-black text-[#1E55AA]">Facturación Electrónica</h1>
            <p class="text-sm font-bold text-slate-400 mt-1">Generación de CFDI 4.0</p>
        </div>
        <button @click="limpiarFormulario()" class="bg-white border-2 border-slate-200 hover:border-[#1E55AA] hover:text-[#1E55AA] text-slate-500 font-bold py-2.5 px-5 rounded-xl transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Nueva Factura
        </button>
    </div>

    <div class="max-w-[1600px] mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Columna Izquierda: Datos del Cliente (Receptor) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-lg font-black text-slate-700 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E55AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    Datos Fiscales del Receptor
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- RFC --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-500 mb-2">RFC <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="form.rfc" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:outline-none focus:border-[#1E55AA] focus:ring-1 focus:ring-[#1E55AA] uppercase placeholder:normal-case" placeholder="Ej. XAXX010101000">
                    </div>
                    
                    {{-- Razón Social --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-500 mb-2">Nombre o Razón Social <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="form.razon_social" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:outline-none focus:border-[#1E55AA] focus:ring-1 focus:ring-[#1E55AA] uppercase placeholder:normal-case" placeholder="Tal cual aparece en la constancia">
                    </div>

                    {{-- Código Postal --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-500 mb-2">Código Postal <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="form.cp" maxlength="5" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:outline-none focus:border-[#1E55AA] focus:ring-1 focus:ring-[#1E55AA]" placeholder="Ej. 76800">
                    </div>

                    {{-- Régimen Fiscal --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-500 mb-2">Régimen Fiscal <span class="text-rose-500">*</span></label>
                        <select x-model="form.regimen" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:outline-none focus:border-[#1E55AA] focus:ring-1 focus:ring-[#1E55AA]">
                            <option value="">Selecciona un régimen...</option>
                            <option value="601">601 - General de Ley Personas Morales</option>
                            <option value="605">605 - Sueldos y Salarios</option>
                            <option value="606">606 - Arrendamiento</option>
                            <option value="612">612 - Personas Físicas con Actividades Empresariales</option>
                            <option value="626">626 - Régimen Simplificado de Confianza (RESICO)</option>
                            <option value="616">616 - Sin obligaciones fiscales</option>
                        </select>
                    </div>

                    {{-- Uso de CFDI --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-500 mb-2">Uso de CFDI <span class="text-rose-500">*</span></label>
                        <select x-model="form.uso_cfdi" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:outline-none focus:border-[#1E55AA] focus:ring-1 focus:ring-[#1E55AA]">
                            <option value="">Selecciona el uso...</option>
                            <option value="G01">G01 - Adquisición de mercancias</option>
                            <option value="G03">G03 - Gastos en general</option>
                            <option value="P01">P01 - Por definir</option>
                            <option value="S01">S01 - Sin efectos fiscales</option>
                        </select>
                    </div>

                    {{-- Forma de Pago --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-500 mb-2">Forma de Pago <span class="text-rose-500">*</span></label>
                        <select x-model="form.forma_pago" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:outline-none focus:border-[#1E55AA] focus:ring-1 focus:ring-[#1E55AA]">
                            <option value="01">01 - Efectivo</option>
                            <option value="04">04 - Tarjeta de crédito</option>
                            <option value="28">28 - Tarjeta de débito</option>
                            <option value="03">03 - Transferencia electrónica de fondos</option>
                            <option value="99">99 - Por definir</option>
                        </select>
                    </div>
                </div>
            </div>
            
            {{-- Correo Electrónico para envío --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-lg font-black text-slate-700 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1E55AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Envío de Factura
                </h2>
                <div>
                    <label class="block text-sm font-bold text-slate-500 mb-2">Correo Electrónico del Cliente</label>
                    <input type="email" x-model="form.email" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:outline-none focus:border-[#1E55AA] focus:ring-1 focus:ring-[#1E55AA]" placeholder="correo@ejemplo.com">
                    <p class="text-xs font-bold text-slate-400 mt-2">Los archivos XML y PDF se enviarán automáticamente a esta dirección.</p>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Búsqueda de Ticket y Resumen --}}
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#1E55AA] to-blue-400"></div>
                
                <h2 class="text-lg font-black text-slate-700 mb-4 mt-2">Asociar Ticket</h2>
                
                <div class="flex gap-2">
                    <input type="text" x-model="ticketBusqueda" @keyup.enter="buscarTicket()" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 focus:outline-none focus:border-[#1E55AA] focus:ring-1 focus:ring-[#1E55AA] uppercase placeholder:normal-case" placeholder="Folio (Ej. BK-0001)">
                    <button @click="buscarTicket()" class="bg-[#1E55AA] hover:bg-[#153e7d] text-white font-bold py-3 px-4 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>

                {{-- Estado: Sin Ticket --}}
                <div x-show="!ticketEncontrado" class="mt-6 p-6 border-2 border-dashed border-slate-200 rounded-xl text-center">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-sm font-bold text-slate-400">Busca un folio para cargar los importes a facturar.</p>
                </div>

                {{-- Estado: Ticket Encontrado --}}
                <div x-show="ticketEncontrado" x-transition class="mt-6 bg-slate-50 rounded-xl p-4 border border-slate-100">
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-slate-200">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase">Folio Encontrado</p>
                            <p class="text-lg font-black text-[#1E55AA]" x-text="ticketDatos.folio"></p>
                        </div>
                        <span class="bg-emerald-100 text-emerald-600 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">Listo</span>
                    </div>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="font-bold text-slate-500">Subtotal</span>
                            <span class="font-black text-slate-700" x-text="formatMoney(ticketDatos.subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="font-bold text-slate-500">IVA (16%)</span>
                            <span class="font-black text-slate-700" x-text="formatMoney(ticketDatos.iva)"></span>
                        </div>
                        <div class="flex justify-between text-lg pt-2 border-t border-slate-200">
                            <span class="font-black text-slate-700">Total a Facturar</span>
                            <span class="font-black text-emerald-600" x-text="formatMoney(ticketDatos.total)"></span>
                        </div>
                    </div>
                </div>

                {{-- Botón de Generar --}}
                <button 
                    @click="timbrarFactura()" 
                    :disabled="!ticketEncontrado"
                    :class="!ticketEncontrado ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/30'"
                    class="w-full mt-6 font-black py-4 px-5 rounded-xl transition-all flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Timbrar Factura SAT
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('facturacionSystem', () => ({
        form: {
            rfc: '',
            razon_social: '',
            cp: '',
            regimen: '',
            uso_cfdi: '',
            forma_pago: '01',
            email: ''
        },
        ticketBusqueda: '',
        ticketEncontrado: false,
        ticketDatos: {
            folio: '',
            subtotal: 0,
            iva: 0,
            total: 0
        },

        buscarTicket() {
            if(this.ticketBusqueda.trim() === '') return;
            
            // Aquí simulamos que buscamos el ticket en tu historial de ventas
            // Como ejemplo, lo vamos a encontrar siempre con montos simulados
            let totalSimulado = 350.00;
            let subtotal = totalSimulado / 1.16;
            
            this.ticketDatos = {
                folio: this.ticketBusqueda.toUpperCase(),
                subtotal: subtotal,
                iva: totalSimulado - subtotal,
                total: totalSimulado
            };
            this.ticketEncontrado = true;
        },

        limpiarFormulario() {
            this.form = { rfc: '', razon_social: '', cp: '', regimen: '', uso_cfdi: '', forma_pago: '01', email: '' };
            this.ticketBusqueda = '';
            this.ticketEncontrado = false;
        },

        timbrarFactura() {
            // Validaciones básicas antes de enviar a Laravel
            if(!this.form.rfc || !this.form.razon_social || !this.form.cp || !this.form.regimen || !this.form.uso_cfdi) {
                alert('⚠️ Por favor completa todos los campos obligatorios marcados con *');
                return;
            }

            // Aquí enviarías el fetch() a tu controlador de facturación en Laravel
            alert('🚀 ¡Conectando con el SAT!\n\nEn un escenario real, esto enviaría el JSON al PAC para timbrar el CFDI de ' + this.form.razon_social);
            this.limpiarFormulario();
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN'
            }).format(amount);
        }
    }));
});
</script>
@endsection