@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 bg-slate-50 min-h-screen text-slate-800">
    
    {{-- ENCABEZADO PRINCIPAL --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-blue-900 flex items-center gap-2">
                Corte de Caja 
                <span id="reloj-vivo" class="text-sm font-mono font-normal text-slate-500 bg-slate-200 px-2 py-0.5 rounded">00:00:00</span>
            </h1>
        </div>
        <div class="flex gap-3">
            {{-- BOTÓN FACTURA GLOBAL --}}
            <button onclick="toggleFacturaGlobal(true)" 
                class="bg-white border border-blue-200 hover:border-blue-400 hover:bg-blue-50 text-blue-700 font-medium text-sm py-2.5 px-5 rounded-xl shadow-sm transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Generar Factura Global
            </button>

            {{-- BOTÓN CERRAR TURNO / GENERAR CORTE --}}
            <button onclick="toggleModal(true)" class="bg-blue-700 hover:bg-blue-800 text-white font-medium text-sm py-2.5 px-5 rounded-xl shadow-sm transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cerrar Turno / Generar Corte
            </button>
        </div>
    </div>

    {{-- CARDS / INDICADORES SUPERIORES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-100 flex items-center gap-4">
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            </div>
            <div>
                <span class="text-2xl font-bold text-slate-900">${{ number_format($totalBruto ?? 0, 2) }}</span>
                <p class="text-xs font-medium text-slate-400 mt-0.5">Ingresos de Hoy</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-100 flex items-center gap-4">
            <div class="p-3 rounded-xl bg-rose-50 text-rose-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" /></svg>
            </div>
            <div>
                <span id="card-gastos-total" class="text-2xl font-bold text-slate-900">${{ number_format(($gastosOperativos ?? 0) + ($retirosAutorizados ?? 0), 2) }}</span>
                <p class="text-xs font-medium text-slate-400 mt-0.5">Gastos y Salidas</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-100 flex items-center gap-4">
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 010-18z" /></svg>
            </div>
            <div>
                <span id="txt-efectivo-final-card" class="text-2xl font-bold text-slate-900">${{ number_format($efectivoFinal ?? 0, 2) }}</span>
                <p class="text-xs font-medium text-slate-400 mt-0.5">Efectivo Esperado en Caja</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-100 flex items-center gap-4">
            <div class="p-3 rounded-xl bg-purple-50 text-purple-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
            </div>
            <div>
                <span class="text-2xl font-bold text-slate-900">${{ number_format(($totalBruto ?? 0) - ($ingresosEfectivo ?? 0), 2) }}</span>
                <p class="text-xs font-medium text-slate-400 mt-0.5">Pagos Digitales (Otros)</p>
            </div>
        </div>
    </div>

    {{-- SECCIÓN: BALANCE DEL TURNO --}}
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-100 mb-6">
        <h2 class="text-sm font-bold text-blue-900 uppercase tracking-wider mb-4">Balance del Turno</h2>
        <div class="bg-slate-50 rounded-xl p-4 grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200 text-center gap-4 md:gap-0">
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Total Ingresos</p>
                <p class="text-xl font-bold text-emerald-600">${{ number_format($totalBruto ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Total Gastos</p>
                <p id="balance-gastos-txt" class="text-xl font-bold text-rose-600">${{ number_format(($gastosOperativos ?? 0) + ($retirosAutorizados ?? 0), 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium mb-1">Neto</p>
                <p id="balance-neto-txt" class="text-xl font-bold text-blue-800">${{ number_format(($totalBruto ?? 0) - (($gastosOperativos ?? 0) + ($retirosAutorizados ?? 0)), 2) }}</p>
            </div>
        </div>
    </div>

    {{-- WORKSPACE: SECCIÓN DE DESGLOSE Y CAJA CHICA --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-100 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    Desglose por Servicio
                </h3>
                <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto pr-1">
                    @forelse($desgloseServicios as $item)
                        <div class="flex justify-between items-center py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-700">{{ $item->servicio }}</span>
                                <span class="bg-slate-100 text-slate-600 font-mono text-xs px-2 py-0.5 rounded-full">x{{ number_format($item->quantity, 0) }}</span>
                            </div>
                            <span class="text-sm font-semibold text-slate-900">${{ number_format($item->total_recaudado, 2) }}</span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-sm">
                            No se han registrado ventas en este turno.
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="border-t border-slate-100 pt-4 mt-4 flex justify-between items-center">
                <span class="text-sm font-bold text-slate-800">Total bruto</span>
                <span class="text-base font-bold text-blue-900">${{ number_format($totalBruto ?? 0, 2) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-100 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm-5-4h.01M6 16h.01" /></svg>
                    Caja — efectivo
                </h3>
                
                <div class="space-y-3.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Fondo inicial</span>
                        <span class="font-medium text-slate-800">${{ number_format($fondoInicial ?? 500, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Ingresos efectivo</span>
                        <span class="font-medium text-emerald-600">+ ${{ number_format($ingresosEfectivo ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Retiros autorizados</span>
                        <span class="font-medium text-rose-600">- $<span id="txt-retiros-autorizados">{{ number_format($retirosAutorizados ?? 0, 2) }}</span></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Gastos Adicionales</span>
                        <span class="font-medium text-rose-600">- $<span id="txt-gastos-operativos">{{ number_format($gastosOperativos ?? 0, 2) }}</span></span>
                    </div>
                </div>

                {{-- BOTONES ACCIONADORES ENLAZADOS --}}
                <div class="grid grid-cols-2 gap-3 mt-5">
                    <button onclick="toggleGasto(true)" class="border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 font-medium text-xs py-2 px-3 rounded-xl transition flex items-center justify-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Gasto Operativo
                    </button>
                    <button onclick="toggleRetiro(true)" class="border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 font-medium text-xs py-2 px-3 rounded-xl transition flex items-center justify-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 010-18z" /></svg>
                        Retiro Seguro
                    </button>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 mt-5 flex justify-between items-center">
                <span class="text-sm font-bold text-slate-800">Efectivo final esperado</span>
                <span id="txt-efectivo-final" class="text-base font-bold text-blue-900">${{ number_format($efectivoFinal ?? 0, 2) }}</span>
            </div>
        </div>

    </div>
</div>

{{-- MODAL DE ARQUEO Y CIERRE FINAL --}}
<div id="modal-cierre" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden transition-all">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-xl overflow-hidden m-4 transform scale-100 transition-all border border-slate-100">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-base font-bold text-slate-800">Cierre de Caja del Turno</h3>
            <button onclick="toggleModal(false)" class="text-slate-400 hover:text-slate-600 transition text-xl">&times;</button>
        </div>
        
        <form onsubmit="procesarCierreCaja(event)" class="p-6" action="{{ route('caja.generarCorte') }}" method="POST">
            @csrf
            <input type="hidden" name="efectivo_real" id="hidden-efectivo-real" value="0">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Desglose de Billetes/Monedas</h4>
                    <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1">
                        @foreach([500, 200, 100, 50, 20] as $billete)
                        <div class="flex items-center gap-3 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                            <span class="text-xs font-medium text-slate-600 w-12">${{ $billete }}</span>
                            <input type="number" min="0" data-denominacion="{{ $billete }}" oninput="calcularDenominaciones()" placeholder="0" class="input-denominacion w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-sm text-right focus:outline-none focus:border-blue-500 font-mono">
                        </div>
                        @endforeach
                        <div class="flex items-center gap-3 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                            <span class="text-xs font-medium text-slate-600 w-12">Monedas</span>
                            <input type="number" min="0" step="0.01" id="input-monedas" oninput="calcularDenominaciones()" placeholder="0.00" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-sm text-right focus:outline-none focus:border-blue-500 font-mono">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col justify-between bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs text-slate-400 font-medium">Esperado en caja</span>
                            <p id="efectivo-esperado" data-valor="{{ $efectivoFinal }}" class="text-xl font-bold text-slate-800">${{ number_format($efectivoFinal, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 font-medium">Contado real en caja</span>
                            <div class="relative mt-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-medium text-sm">$</span>
                                <input type="number" id="efectivo-real" readonly value="0.00" class="w-full bg-white border border-slate-200 rounded-xl pl-6 pr-4 py-2 font-mono font-bold text-lg text-slate-900 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <div id="contenedor-diferencia" class="p-3.5 rounded-xl border border-gray-100 bg-gray-50 mt-4">
                        <span class="text-xs text-slate-400 font-medium">Diferencia (Descuadre)</span>
                        <p id="texto-diferencia" class="text-lg font-bold text-slate-500">$0.00</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
                <button type="button" onclick="toggleModal(false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-xl transition">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium rounded-xl shadow-xs transition">Confirmar Cierre de Turno</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: REGISTRAR GASTO --}}
<div id="modal-gasto" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-xl m-4 border border-slate-100">
        <div class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-2xl">
            <h3 class="text-sm font-bold text-slate-800">Registrar Gasto Operativo</h3>
            <button type="button" onclick="toggleGasto(false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>
        <form onsubmit="agregarGastoLocal(event)" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Monto ($)</label>
                <input type="number" id="val-gasto-monto" min="0.01" step="0.01" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Concepto o Descripción</label>
                <input type="text" id="val-gasto-concepto" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-2.5 pt-2">
                <button type="button" onclick="toggleGasto(false)" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium rounded-lg transition">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-medium rounded-lg transition">Aplicar Gasto</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: RETIRO SEGURO --}}
<div id="modal-retiro" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-xl m-4 border border-slate-100">
        <div class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-2xl">
            <h3 class="text-sm font-bold text-slate-800">Registrar Retiro Seguro</h3>
            <button type="button" onclick="toggleRetiro(false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>
        <form onsubmit="agregarRetiroLocal(event)" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Monto a retirar ($)</label>
                <input type="number" id="val-retiro-monto" min="0.01" step="0.01" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Quién Recibe (Responsable)</label>
                <input type="text" id="val-retiro-recibe" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-2.5 pt-2">
                <button type="button" onclick="toggleRetiro(false)" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium rounded-lg transition">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-medium rounded-lg transition">Autorizar Retiro</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: CONFIRMACIÓN DE CIERRE --}}
<div id="modal-confirmar-cierre" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-[60] hidden">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl m-4 border border-slate-100">
        <div class="p-6 text-center">
            <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1">¿Cerrar turno?</h3>
            <p class="text-sm text-slate-500 mb-6">Esta acción cerrará el turno actual. Asegúrate de haber verificado el arqueo antes de continuar.</p>
            <div class="flex gap-3">
                <button onclick="cancelarCierre()" class="flex-1 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-xl transition">
                    Cancelar
                </button>
                <button onclick="confirmarCierre()" class="flex-1 px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium rounded-xl transition">
                    Sí, cerrar turno
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: ÉXITO DE CIERRE --}}
<div id="modal-exito-cierre" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-[60] hidden">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl m-4 border border-slate-100">
        <div class="p-6 text-center">
            <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1">¡Cierre Realizado con Éxito!</h3>
            <p class="text-sm text-slate-500">El turno ha sido cerrado correctamente. Procesando...</p>
        </div>
    </div>
</div>

{{-- MODAL: FACTURA GLOBAL --}}
<div id="modal-factura-global" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-xl m-4 border border-slate-100">
        
        <div class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-2xl">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Factura Global del Turno
            </h3>
            <button type="button" onclick="toggleFacturaGlobal(false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        {{-- RESUMEN DE LO QUE SE VA A FACTURAR --}}
        <div class="px-5 pt-4">
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 space-y-2">
                <p class="text-xs font-bold text-blue-700 uppercase tracking-wide">Resumen del turno a facturar</p>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Cliente</span>
                    <span class="font-medium text-slate-700">Público en General</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">RFC</span>
                    <span class="font-mono font-medium text-slate-700">XAXX010101000</span>
                </div>
                <!--
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Forma de pago</span>
                    <span class="font-medium text-slate-700">99 - Por definir</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Método de pago</span>
                    <span class="font-medium text-slate-700">PPD - Parcialidades o Diferido</span>
                </div>
                -->
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Periodo</span>
                    <span class="font-medium text-slate-700">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between text-sm border-t border-blue-100 pt-2 mt-1">
                    <span class="text-slate-500">Ventas a facturar</span>
                    <span class="font-bold text-blue-700" id="fg-aviso-ventas">Solo ventas sin factura previa</span>
                </div>
            </div>
        </div>

        <form onsubmit="generarFacturaGlobal(event)" class="p-5 space-y-4">
            @csrf

            <!-- Método para seleccionar la forma de pago.
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Forma de Pago</label>
                <select id="fg-forma-pago" name="payment_form" 
                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                    <option value="01">01 - Efectivo</option>
                    <option value="03">03 - Transferencia electrónica</option>
                    <option value="04">04 - Tarjeta de crédito</option>
                    <option value="28">28 - Tarjeta de débito</option>
                    <option value="99">99 - Por definir</option>
                </select>
            </div>
            -->

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Periodicidad</label>
                <select id="fg-periodicidad" name="periodicidad"
                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                    <option value="01">01 - Diaria</option>
                    <option value="02">02 - Semanal</option>
                    <option value="03">03 - Quincenal</option>
                    <option value="04">04 - Mensual</option>
                </select>
            </div>

            {{-- ESTADO DEL PROCESO --}}
            <div id="fg-estado" class="hidden text-xs text-center py-2 px-3 rounded-lg bg-blue-50 text-blue-600 font-medium">
                Generando factura, por favor espera...
            </div>

            <div class="flex justify-end gap-2.5 pt-1">
                <button type="button" onclick="toggleFacturaGlobal(false)" 
                    class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium rounded-lg transition">
                    Cancelar
                </button>
                <button type="submit" id="fg-btn-submit"
                    class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-medium rounded-lg transition flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Generar Factura Global
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JAVASCRIPT CON LOGICA COMPLETA --}}
<script>
    let fondoInicial = parseFloat("{{ $fondoInicial ?? 500 }}") || 0;
    let ingresosEfectivo = parseFloat("{{ $ingresosEfectivo ?? 0 }}") || 0;
    let retirosAutorizados = parseFloat("{{ $retirosAutorizados ?? 0 }}") || 0;
    let gastosOperativos = parseFloat("{{ $gastosOperativos ?? 0 }}") || 0;
    let totalBruto = parseFloat("{{ $totalBruto ?? 0 }}") || 0;

    let efectivoFinal = fondoInicial + ingresosEfectivo - retirosAutorizados - gastosOperativos;

    let formularioCierre = null;

    function actualizarReloj() {
        const ahora = new Date();
        document.getElementById('reloj-vivo').innerText = ahora.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
    }
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    function actualizarInterfazCaja() {
        efectivoFinal = fondoInicial + ingresosEfectivo - retirosAutorizados - gastosOperativos;
        const totalGastosYRetiros = gastosOperativos + retirosAutorizados;
        const netoTurno = totalBruto - totalGastosYRetiros;

        const formatter = new Intl.NumberFormat('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('txt-retiros-autorizados').innerText = formatter.format(retirosAutorizados);
        document.getElementById('txt-gastos-operativos').innerText = formatter.format(gastosOperativos);
        document.getElementById('txt-efectivo-final').innerText = "$" + formatter.format(efectivoFinal);
        
        document.getElementById('txt-efectivo-final-card').innerText = "$" + formatter.format(efectivoFinal);
        document.getElementById('card-gastos-total').innerText = "$" + formatter.format(totalGastosYRetiros);

        document.getElementById('balance-gastos-txt').innerText = "$" + formatter.format(totalGastosYRetiros);
        document.getElementById('balance-neto-txt').innerText = "$" + formatter.format(netoTurno);

        const domEsperado = document.getElementById('efectivo-esperado');
        if(domEsperado) {
            domEsperado.setAttribute('data-valor', efectivoFinal);
            domEsperado.innerText = "$" + formatter.format(efectivoFinal);
        }
        calcularDiferencia();
    }

    function agregarGastoLocal(e) {
        e.preventDefault();
        const montoInput = document.getElementById('val-gasto-monto');
        const conceptoInput = document.getElementById('val-gasto-concepto');
        const monto = parseFloat(montoInput.value) || 0;
        const concepto = conceptoInput.value.trim();

        if(monto <= 0 || concepto === "") return;

        fetch("{{ route('caja.movimiento') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: JSON.stringify({ tipo: 'gasto', monto: monto, concepto_o_responsable: concepto })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                gastosOperativos += data.monto;
                actualizarInterfazCaja();
                montoInput.value = ''; conceptoInput.value = '';
                toggleGasto(false);
            } else { alert("Error: " + data.message); }
        }).catch(() => alert("Error de comunicación con el servidor."));
    }

    function agregarRetiroLocal(e) {
        e.preventDefault();
        const montoInput = document.getElementById('val-retiro-monto');
        const recibeInput = document.getElementById('val-retiro-recibe');
        const monto = parseFloat(montoInput.value) || 0;
        const recibe = recibeInput.value.trim();

        if(monto <= 0 || recibe === "") return;
        if(monto > efectivoFinal) {
            alert('⚠️ ¡Cuidado! No puedes retirar más dinero del efectivo disponible.');
            return;
        }

        fetch("{{ route('caja.movimiento') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: JSON.stringify({ tipo: 'retiro', monto: monto, concepto_o_responsable: recibe })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                retirosAutorizados += data.monto;
                actualizarInterfazCaja();
                montoInput.value = ''; recibeInput.value = '';
                toggleRetiro(false);
            } else { alert("Error: " + data.message); }
        }).catch(() => alert("Error de comunicación con el servidor."));
    }

    // CONTROL DE MODALES REMOVIENDO LA CLASE "HIDDEN"
    function toggleModal(show) {
        const modal = document.getElementById('modal-cierre');
        if(show) { modal.classList.remove('hidden'); calcularDiferencia(); }
        else { modal.classList.add('hidden'); }
    }
    function toggleGasto(show) {
        const modal = document.getElementById('modal-gasto');
        if(show) modal.classList.remove('hidden'); else modal.classList.add('hidden');
    }
    function toggleRetiro(show) {
        const modal = document.getElementById('modal-retiro');
        if(show) modal.classList.remove('hidden'); else modal.classList.add('hidden');
    }

    function calcularDenominaciones() {
        let total = 0;
        document.querySelectorAll('.input-denominacion').forEach(input => {
            const valorBillete = parseFloat(input.getAttribute('data-denominacion'));
            const cantidad = parseFloat(input.value) || 0;
            total += (valorBillete * cantidad);
        });
        const monedas = parseFloat(document.getElementById('input-monedas').value) || 0;
        total += monedas;
        document.getElementById('efectivo-real').value = total.toFixed(2);
        calcularDiferencia();
    }

    function calcularDiferencia() {
        const esperadoElement = document.getElementById('efectivo-esperado');
        if (!esperadoElement) return;

        const esperado = parseFloat(esperadoElement.getAttribute('data-valor')) || 0;
        const real = parseFloat(document.getElementById('efectivo-real').value) || 0;
        const diferencia = real - esperado;

        const textoDif = document.getElementById('texto-diferencia');
        const contenedor = document.getElementById('contenedor-diferencia');

        if (Math.abs(diferencia) < 0.01) {
            textoDif.innerText = "$0.00 (Caja Cuadrada)";
            textoDif.className = "text-base font-bold text-slate-500";
            contenedor.className = "p-3.5 rounded-xl border border-slate-100 bg-slate-100/50 mt-4";
        } else if (diferencia > 0) {
            textoDif.innerText = "+$" + diferencia.toFixed(2) + " (Sobrante)";
            textoDif.className = "text-base font-bold text-emerald-600";
            contenedor.className = "p-3.5 rounded-xl border border-emerald-100 bg-emerald-50/60 mt-4";
        } else {
            textoDif.innerText = "-$" + Math.abs(diferencia).toFixed(2) + " (Faltante)";
            textoDif.className = "text-base font-bold text-rose-600";
            contenedor.className = "p-3.5 rounded-xl border border-rose-100 bg-rose-50/60 mt-4";
        }
    }

    function procesarCierreCaja(e) {
        e.preventDefault();
        formularioCierre = e.target;
        // En lugar del confirm nativo, abrimos nuestro modal
        document.getElementById('modal-confirmar-cierre').classList.remove('hidden');
    }

    function cancelarCierre() {
        document.getElementById('modal-confirmar-cierre').classList.add('hidden');
        formularioCierre = null;
    }

    function confirmarCierre() {
        document.getElementById('modal-confirmar-cierre').classList.add('hidden');
        document.getElementById('modal-exito-cierre').classList.remove('hidden');

        const efectivoReal = document.getElementById('efectivo-real').value;
        document.getElementById('hidden-efectivo-real').value = efectivoReal;

        const formData = new FormData(formularioCierre);

        fetch("{{ route('caja.generarCorte') }}", {
            method: 'POST',
            body: formData
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            
            //Código para descargar de manera automática el PDF generado, actualmente se abre en una nueva pestaña para que el usuario pueda revisar el contenido antes de descargarlo.
            /*const a = document.createElement('a');
            a.href = url;
            a.download = 'corte_caja_{{ now()->format("Y-m-d_H-i") }}.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);*/

            window.open(url, '_blank');

            setTimeout(() => {
                document.getElementById('modal-exito-cierre').classList.add('hidden');
                document.getElementById('modal-cierre').classList.add('hidden');
            }, 800);
        })
        .catch(() => {
            alert('Ocurrió un error al generar el PDF. Intenta de nuevo.');
            document.getElementById('modal-exito-cierre').classList.add('hidden');
        });
    }

    //Funcionalidad de la factura global
    function toggleFacturaGlobal(show) {
        const modal = document.getElementById('modal-factura-global');
        if (show) modal.classList.remove('hidden');
        else modal.classList.add('hidden');
    }

    function generarFacturaGlobal(e) {
        e.preventDefault();

        const btnSubmit = document.getElementById('fg-btn-submit');
        const estado    = document.getElementById('fg-estado');

        btnSubmit.disabled = true;
        btnSubmit.innerText = 'Procesando...';
        estado.classList.remove('hidden');
        estado.innerText = 'Generando factura, por favor espera...';
        estado.className = 'text-xs text-center py-2 px-3 rounded-lg bg-blue-50 text-blue-600 font-medium';

        const periodicidad = document.getElementById('fg-periodicidad').value;

        fetch("{{ route('caja.facturaGlobal') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ periodicidad: periodicidad })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Error desconocido');
            }

            // Construimos los enlaces de descarga usando la ruta de FacturaController
            const pdfUrl = `/factura/archivo/${data.factura_id}/pdf`;
            const zipUrl = `/factura/archivo/${data.factura_id}/zip`;

            estado.innerHTML = `
                <p class="font-bold text-emerald-700 mb-2">✅ Factura global generada — ${data.ventas_count} venta(s) timbradas</p>
                <div class="flex justify-center gap-3 mt-1">
                    <a href="${pdfUrl}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Ver PDF
                    </a>
                    <a href="${zipUrl}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar ZIP (PDF + XML)
                    </a>
                </div>
            `;
            estado.className = 'text-xs text-center py-3 px-3 rounded-lg bg-emerald-50 border border-emerald-100';

            btnSubmit.disabled  = false;
            btnSubmit.innerText = 'Generar factura global';
        })
        .catch(err => {
            let mensaje = 'Error al generar la factura.';
            try {
                const json = JSON.parse(err.message);
                if (json.message) mensaje = '' + json.message;
            } catch {}
            if (err.message && !err.message.startsWith('{')) mensaje = '' + err.message;

            estado.innerText  = mensaje;
            estado.className  = 'text-xs text-center py-2 px-3 rounded-lg bg-rose-50 text-rose-600 font-medium';
            btnSubmit.disabled  = false;
            btnSubmit.innerText = 'Generar factura global';
        });
    }
</script>
@endsection