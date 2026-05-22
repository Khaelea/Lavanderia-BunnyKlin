@extends('layouts.app')

@section('content')
    {{-- CONTENEDOR DE LA CABECERA --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-[#1E55AA] text-white rounded-xl shadow-md shadow-blue-500/10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5M4.5 19.5h15M5.25 4.5V1c0-.414.336-.75.75-.75h12c.414 0 .75.336.75.75v3.5m-13.5 0A2.25 2.25 0 0 0 3 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 17.25V6.75A2.25 2.25 0 0 0 18.75 4.5m-13.5 0h13.5M9 10.5h.008v.008H9V10.5Zm3 0h.008v.008H12V10.5Zm3 0h.008v.008H15V10.5Zm-6 3h.008v.008H9v-.008Zm3 0h.008v.008H12v-.008Zm3 0h.008v.008H15v-.008Z" />
                </svg>
            </div>
            <h1 class="text-3xl font-black text-[#1E55AA] tracking-tight">Corte de Caja</h1>
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800/60 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 self-start sm:self-auto font-medium">
            Turno: <span id="reloj-vivo" class="font-bold text-gray-800 dark:text-white">--:--:--</span> — 
            <span class="text-[#1E55AA] dark:text-blue-400 font-bold">en curso</span>
        </div>
    </div>
    
    {{-- CONTENEDOR PRINCIPAL --}}
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12 shadow-sm">
        <div class="w-full mx-auto">

            {{-- Métodos de Pago --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Efectivo en caja</span>
                    {{-- 🛠️ ID añadido para actualizar los widgets de arriba --}}
                    <span id="widget-efectivo-caja" class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 block">${{ number_format($ingresosEfectivo ?? 0, 2) }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 block mt-2">Monitoreo de turno</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Tarjeta / Stripe</span>
                    <span class="text-3xl font-bold text-blue-600 dark:text-blue-400 block">$0.00</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 block mt-2">vía Terminal</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 block mb-1">Transferencias</span>
                    <span class="text-3xl font-bold text-purple-600 dark:text-purple-400 block">$0.00</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 block mt-2">0 operaciones</span>
                </div>
            </div>

            {{-- Desglose y flujo de caja --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Columna Izquierda: Desglose por servicio REAL --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h4 class="text-base font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-800 pb-2">Desglose por servicio</h4>
                        
                        <div class="space-y-3 text-sm">
                            @forelse($desgloseServicios as $item)
                                <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                    <span>
                                        {{ $item->servicio }} 
                                        <span class="text-gray-400 dark:text-gray-500">({{ number_format($item->cantidad, 0) }})</span>
                                    </span>
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                        ${{ number_format($item->total_recaudado, 2) }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center text-gray-400 py-4">
                                    No se han registrado ventas en este turno.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Total Bruto Dinámico --}}
                    <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200 dark:border-gray-800">
                        <span class="text-base font-bold text-gray-800 dark:text-white">Total bruto</span>
                        <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            ${{ number_format($totalBruto ?? 0, 2) }}
                        </span>
                    </div>
                </div>

                {{-- Columna Derecha: Caja — efectivo DINÁMICA CON BOTONES ACCIONABLES --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm flex flex-col justify-between h-full">
                    <div>
                        <h4 class="text-base font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-800 pb-2">Caja — efectivo</h4>
                        
                        {{-- Listado de conceptos con IDs fijos para manipular con JS --}}
                        <div class="space-y-3 text-sm mb-6">
                            {{-- Fondo Inicial --}}
                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                <span>Fondo inicial</span>
                                <span class="font-semibold text-gray-800 dark:text-white">
                                    $<span id="txt-fondo-inicial">{{ number_format($fondoInicial ?? 500, 2) }}</span>
                                </span>
                            </div>

                            {{-- Ingresos en Efectivo --}}
                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                <span>Ingresos efectivo</span>
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                    +$<span id="txt-ingresos-efectivo">{{ number_format($ingresosEfectivo ?? 0, 2) }}</span>
                                </span>
                            </div>

                            {{-- Retiros Autorizados --}}
                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                <span>Retiros autorizados</span>
                                <span class="font-semibold text-red-600 dark:text-red-400">
                                    -$<span id="txt-retiros-autorizados">{{ number_format($retirosAutorizados ?? 0, 2) }}</span>
                                </span>
                            </div>

                            {{-- Gastos Operativos --}}
                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                <span>Gastos operativos</span>
                                <span class="font-semibold text-red-600 dark:text-red-400">
                                    -$<span id="txt-gastos-operativos">{{ number_format($gastosOperativos ?? 0, 2) }}</span>
                                </span>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-800 my-4">

                        {{-- SECCIÓN DE BOTONES DE ACCIÓN RÁPIDA --}}
                        <div class="grid grid-cols-2 gap-3 my-4">
                            {{-- Botón + Gasto --}}
                            <button type="button" onclick="toggleGasto(true)" class="flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:bg-red-950/30 dark:hover:bg-red-950/50 border border-red-200 dark:border-red-900/50 rounded-xl transition-all duration-200 shadow-sm active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Gasto
                            </button>

                            {{-- Botón Retiro Seguro --}}
                            <button type="button" onclick="toggleRetiro(true)" class="flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-950/30 dark:hover:bg-amber-950/50 border border-amber-200 dark:border-amber-900/50 rounded-xl transition-all duration-200 shadow-sm active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Retiro Seguro
                            </button>
                        </div>
                    </div>

                    {{-- Efectivo Final Calculado --}}
                    <div class="flex justify-between items-center pt-4 mt-2 border-t border-gray-200 dark:border-gray-800">
                        <span class="text-base font-bold text-gray-800 dark:text-white">Efectivo final</span>
                        <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            $<span id="txt-efectivo-final">{{ number_format($efectivoFinal ?? 0, 2) }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button class="w-full bg-white dark:bg-gray-900/50 hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-800 text-gray-700 dark:text-gray-300 font-medium py-3 rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-sm">
                    Imprimir corte
                </button>
                <button onclick="toggleModal(true)" class="w-full bg-red-600 hover:bg-red-700 dark:bg-red-950 dark:hover:bg-red-900 border border-transparent dark:border-red-800 text-white dark:text-red-200 font-medium py-3 rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-md shadow-red-500/10 dark:shadow-red-950/50">
                    Cerrar caja del turno
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL DE CIERRE CON ARQUEO --}}
    <div id="modal-cierre" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex justify-center items-center p-4 overflow-y-auto">
        <div class="bg-white dark:bg-[#0d1321] w-full max-w-2xl rounded-2xl border border-gray-200 dark:border-gray-800 shadow-2xl p-6 transform transition-all">
            
            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-800 pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    Cierre de Caja
                </h3>
                <button onclick="toggleModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold">&times;</button>
            </div>

            {{-- 🛠️ Interceptamos el submit con la función procesarCierreCaja --}}
            <form onsubmit="procesarCierreCaja(event)" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Desglose de Billetes/Monedas</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                        <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900 p-1.5 rounded-lg border border-gray-200 dark:border-gray-800">
                            <span class="w-8 font-semibold text-gray-500">$500:</span>
                            <input type="number" data-denominacion="500" oninput="calcularDenominaciones()" class="input-denominacion w-full bg-white dark:bg-gray-800 text-center rounded p-1 border dark:border-gray-700" min="0" placeholder="0">
                        </div>
                        <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900 p-1.5 rounded-lg border border-gray-200 dark:border-gray-800">
                            <span class="w-8 font-semibold text-gray-500">$200:</span>
                            <input type="number" data-denominacion="200" oninput="calcularDenominaciones()" class="input-denominacion w-full bg-white dark:bg-gray-800 text-center rounded p-1 border dark:border-gray-700" min="0" placeholder="0">
                        </div>
                        <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900 p-1.5 rounded-lg border border-gray-200 dark:border-gray-800">
                            <span class="w-8 font-semibold text-gray-500">$100:</span>
                            <input type="number" data-denominacion="100" oninput="calcularDenominaciones()" class="input-denominacion w-full bg-white dark:bg-gray-800 text-center rounded p-1 border dark:border-gray-700" min="0" placeholder="0">
                        </div>
                        <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900 p-1.5 rounded-lg border border-gray-200 dark:border-gray-800">
                            <span class="w-8 font-semibold text-gray-500">$50:</span>
                            <input type="number" data-denominacion="50" oninput="calcularDenominaciones()" class="input-denominacion w-full bg-white dark:bg-gray-800 text-center rounded p-1 border dark:border-gray-700" min="0" placeholder="0">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs mt-2">
                        <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900 p-1.5 rounded-lg border border-gray-200 dark:border-gray-800">
                            <span class="w-8 font-semibold text-gray-500">$20:</span>
                            <input type="number" data-denominacion="20" oninput="calcularDenominaciones()" class="input-denominacion w-full bg-white dark:bg-gray-800 text-center rounded p-1 border dark:border-gray-700" min="0" placeholder="0">
                        </div>
                        <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900 p-1.5 rounded-lg border border-gray-200 dark:border-gray-800">
                            <span class="w-8 font-semibold text-gray-500">Mond:</span>
                            <input type="number" id="input-monedas" oninput="calcularDenominaciones()" class="w-full bg-white dark:bg-gray-800 text-center rounded p-1 border dark:border-gray-700" min="0" placeholder="$ Suma">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-800">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 py-2">
                    <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block">Esperado en Caja</span>
                        <span id="efectivo-esperado" data-valor="{{ $efectivoFinal ?? 0 }}" class="text-xl font-bold text-gray-700 dark:text-gray-300">
                            ${{ number_format($efectivoFinal ?? 0, 2) }}
                        </span>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block">Total Contado (Real)</span>
                        <input type="number" step="0.01" id="efectivo-real" name="efectivo_real_fisico" oninput="calcularDiferencia()" class="w-full bg-transparent text-xl font-bold text-blue-600 dark:text-blue-400 focus:outline-none" value="0.00">
                    </div>
                    <div id="contenedor-diferencia" class="bg-gray-50 dark:bg-gray-900 p-3 rounded-xl border border-gray-100 dark:border-gray-800 flex flex-col justify-between">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block">Diferencia (Descuadre)</span>
                        <span id="texto-diferencia" class="text-xl font-bold text-gray-500">$0.00</span>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="toggleModal(false)" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow-md shadow-red-500/10">Confirmar y Cerrar Turno</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DE CONFIRMACIÓN ESTILIZADO --}}
    <div id="modal-alerta-confirmacion" class="fixed inset-0 z-[60] hidden bg-slate-900/70 backdrop-blur-sm flex justify-center items-center p-4">
        <div class="bg-white dark:bg-[#0d1321] w-full max-w-md rounded-2xl border border-gray-200 dark:border-gray-800 shadow-2xl p-6 transform transition-all text-center">
            
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 mb-4 shadow-sm border border-red-100 dark:border-red-900/30">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">
                ¿Confirmar Cierre de Caja?
            </h3>
            
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Estás a punto de finalizar el turno operativo de la caja. Esta acción no se puede deshacer.
            </p>

            <div class="bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 rounded-xl p-3.5 mb-6 text-left text-xs space-y-2">
                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                    <span>Total Contado Físico:</span>
                    <span id="conf-monto-real" class="font-bold text-blue-600 dark:text-blue-400">$0.00</span>
                </div>
                <div class="flex justify-between items-center pt-1.5 border-t border-gray-200/60 dark:border-gray-800/60">
                    <span>Auditoría de Cierre:</span>
                    <span id="conf-mensaje-descuadre" class="font-bold">Calculando...</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="cancelarCierreEstilizado()" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                    Revisar Caja
                </button>
                <button type="button" onclick="redirigirCierreExitoso()" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow-md shadow-red-500/10 transition-colors">
                    Sí, Cerrar Turno
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL ADICIONAL: REGISTRAR + GASTO --}}
    <div id="modal-gasto" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex justify-center items-center p-4">
        <div class="bg-white dark:bg-[#0d1321] w-full max-w-md rounded-2xl border border-gray-200 dark:border-gray-800 shadow-2xl p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                Registrar Gasto Operativo
            </h3>
            {{-- 🛠️ Capturamos el submit con JS para guardar localmente --}}
            <form onsubmit="agregarGastoLocal(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Monto ($)</label>
                    <input type="number" id="val-gasto-monto" step="0.01" required placeholder="0.00" class="w-full bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white rounded-xl p-2.5 border border-gray-200 dark:border-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500/30">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Concepto o Descripción</label>
                    <input type="text" id="val-gasto-concepto" required placeholder="Ej: Jabón de emergencia, bolsas..." class="w-full bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white rounded-xl p-2.5 border border-gray-200 dark:border-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500/30">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="toggleGasto(false)" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-900">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-md">Aplicar Gasto</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL ADICIONAL: RETIRO SEGURO --}}
    <div id="modal-retiro" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex justify-center items-center p-4">
        <div class="bg-white dark:bg-[#0d1321] w-full max-w-md rounded-2xl border border-gray-200 dark:border-gray-800 shadow-2xl p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                Registrar Retiro Seguro
            </h3>
            {{-- 🛠️ Capturamos el submit con JS para guardar localmente --}}
            <form onsubmit="agregarRetiroLocal(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Monto a retirar ($)</label>
                    <input type="number" id="val-retiro-monto" step="0.01" required placeholder="0.00" class="w-full bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white rounded-xl p-2.5 border border-gray-200 dark:border-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Quién Recibe (Responsable)</label>
                    <input type="text" id="val-retiro-recibe" required placeholder="Ej: Administrador Carlos" class="w-full bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white rounded-xl p-2.5 border border-gray-200 dark:border-gray-800 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="toggleRetiro(false)" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-900">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-md">Autorizar Retiro</button>
                </div>
            </form>
        </div>
    </div>


    {{-- LÓGICA JAVASCRIPT DINÁMICA LOCAL --}}
    <script>
        // 1. VARIABLES LOCALES DEL TURNO (Cargadas con los datos del controlador)
        let fondoInicial = parseFloat("{{ $fondoInicial ?? 500 }}") || 0;
        let ingresosEfectivo = parseFloat("{{ $ingresosEfectivo ?? 0 }}") || 0;
        let retirosAutorizados = parseFloat("{{ $retirosAutorizados ?? 0 }}") || 0;
        let gastosOperativos = parseFloat("{{ $gastosOperativos ?? 0 }}") || 0;
        let efectivoFinal = fondoInicial + ingresosEfectivo - retirosAutorizados - gastosOperativos;

        // Reloj en vivo
        function actualizarReloj() {
            const ahora = new Date();
            document.getElementById('reloj-vivo').innerText = ahora.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
        }
        actualizarReloj();
        setInterval(actualizarReloj, 1000);

        // 2. FUNCIÓN CENTRAL: ACTUALIZAR LOS NÚMEROS EN LA PANTALLA
        function actualizarInterfazCaja() {
            // Calcular fórmula de flujo
            efectivoFinal = fondoInicial + ingresosEfectivo - retirosAutorizados - gastosOperativos;

            // Formateador de moneda de JS
            const formatter = new Intl.NumberFormat('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // Inyectar en las etiquetas de texto
            document.getElementById('txt-retiros-autorizados').innerText = formatter.format(retirosAutorizados);
            document.getElementById('txt-gastos-operativos').innerText = formatter.format(gastosOperativos);
            document.getElementById('txt-efectivo-final').innerText = formatter.format(efectivoFinal);
            
            // Sincronizar el modal de arqueo final ("Esperado en caja")
            const domEsperado = document.getElementById('efectivo-esperado');
            if(domEsperado) {
                domEsperado.setAttribute('data-valor', efectivoFinal);
                domEsperado.innerText = "$" + formatter.format(efectivoFinal);
            }
        }

        // 3. CAPTURA LOCAL DE + GASTO
        function agregarGastoLocal(e) {
            e.preventDefault(); // Evita que la página se recargue
            
            const montoInput = document.getElementById('val-gasto-monto');
            const monto = parseFloat(montoInput.value) || 0;

            if(monto > 0) {
                gastosOperativos += monto; // Sumamos al acumulador de gastos
                actualizarInterfazCaja();  // Recalculamos la pantalla
                
                // Limpiar inputs y cerrar modal
                montoInput.value = '';
                document.getElementById('val-gasto-concepto').value = '';
                toggleGasto(false);
            }
        }

        // 4. CAPTURA LOCAL DE RETIRO SEGURO
        function agregarRetiroLocal(e) {
            e.preventDefault();
            
            const montoInput = document.getElementById('val-retiro-monto');
            const monto = parseFloat(montoInput.value) || 0;

            if(monto > 0) {
                if(monto > efectivoFinal) {
                    alert('⚠️ ¡Cuidado! No puedes retirar más dinero del efectivo disponible en caja.');
                    return;
                }
                retirosAutorizados += monto; // Sumamos al acumulador de retiros
                actualizarInterfazCaja();    // Recalculamos la pantalla
                
                // Limpiar inputs y cerrar modal
                montoInput.value = '';
                document.getElementById('val-retiro-recibe').value = '';
                toggleRetiro(false);
            }
        }

        // Control del Modal de Cierre Final
        function toggleModal(show) {
            const modal = document.getElementById('modal-cierre');
            if(show) {
                modal.classList.remove('hidden');
                calcularDiferencia();
            } else {
                modal.classList.add('hidden');
            }
        }

        // Control de los Modals Adicionales
        function toggleGasto(show) {
            const modal = document.getElementById('modal-gasto');
            if(show) modal.classList.remove('hidden');
            else modal.classList.add('hidden');
        }

        function toggleRetiro(show) {
            const modal = document.getElementById('modal-retiro');
            if(show) modal.classList.remove('hidden');
            else modal.classList.add('hidden');
        }

        // Conteo automatizado de Billetes
        function calcularDenominaciones() {
            let total = 0;
            const inputs = document.querySelectorAll('.input-denominacion');
            
            inputs.forEach(input => {
                const valorBillete = parseFloat(input.getAttribute('data-denominacion'));
                const cantidad = parseFloat(input.value) || 0;
                total += (valorBillete * cantidad);
            });

            const monedas = parseFloat(document.getElementById('input-monedas').value) || 0;
            total += monedas;

            document.getElementById('efectivo-real').value = total.toFixed(2);
            calcularDiferencia();
        }

        // Cálculo de faltante/sobrante
        function calcularDiferencia() {
            const esperadoElement = document.getElementById('efectivo-esperado');
            if (!esperadoElement) return;

            const esperado = parseFloat(esperadoElement.getAttribute('data-valor')) || 0;
            const real = parseFloat(document.getElementById('efectivo-real').value) || 0;
            const diferencia = real - esperado;
            
            const textoDif = document.getElementById('texto-diferencia');
            const contenedor = document.getElementById('contenedor-diferencia');

            if (Math.abs(diferencia) < 0.01) {
                textoDif.innerText = "$0.00";
                textoDif.className = "text-xl font-bold text-gray-500";
                contenedor.className = "p-3 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900";
            } else if (diferencia > 0) {
                textoDif.innerText = `+$${diferencia.toFixed(2)} (Sobrante)`;
                textoDif.className = "text-xl font-bold text-emerald-600 dark:text-emerald-400";
                contenedor.className = "p-3 rounded-xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/50 dark:bg-emerald-950/20";
            } else {
                textoDif.innerText = `$${diferencia.toFixed(2)} (Faltante)`;
                textoDif.className = "text-xl font-bold text-red-600 dark:text-red-400";
                contenedor.className = "p-3 rounded-xl border border-red-200 dark:border-red-900/50 bg-red-50/50 dark:bg-red-950/20";
            }
        }

        function procesarCierreCaja(event) {
            // 1. Evitar que el formulario mande datos inmediatamente
            event.preventDefault();

            // 2. Capturar valores actuales del arqueo
            const real = parseFloat(document.getElementById('efectivo-real').value) || 0;
            const esperadoElement = document.getElementById('efectivo-esperado');
            const esperado = esperadoElement ? parseFloat(esperadoElement.getAttribute('data-valor')) : 0;
            const diferencia = real - esperado;

            // Formateador de moneda
            const formatter = new Intl.NumberFormat('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // Elementos del nuevo modal
            const domMontoReal = document.getElementById('conf-monto-real');
            const domMensajeDescuadre = document.getElementById('conf-mensaje-descuadre');

            // 3. Modificar los textos del modal de confirmación en base al estado de la caja
            domMontoReal.innerText = `$${formatter.format(real)}`;

            if (Math.abs(diferencia) < 0.01) {
                domMensajeDescuadre.innerText = "✓ Todo Cuadra Exitosamente";
                domMensajeDescuadre.className = "font-bold text-emerald-600 dark:text-emerald-400";
            } else if (diferencia > 0) {
                domMensajeDescuadre.innerText = `⚠️ Sobrante de $${formatter.format(diferencia)}`;
                domMensajeDescuadre.className = "font-bold text-amber-600 dark:text-amber-400";
            } else {
                domMensajeDescuadre.innerText = `🚨 Faltante de $${formatter.format(Math.abs(diferencia))}`;
                domMensajeDescuadre.className = "font-bold text-red-600 dark:text-red-400";
            }

            // 4. Mostrar el modal visual elegante
            document.getElementById('modal-alerta-confirmacion').classList.remove('hidden');
        }

        // Función para cerrar esta alerta secundaria si deciden corregir algo
        function cancelarCierreEstilizado() {
            document.getElementById('modal-alerta-confirmacion').classList.add('hidden');
        }

        // Función final que ejecuta el redireccionamiento al aceptar
        function redirigirCierreExitoso() {
            // Aquí puedes meter una animación o un loader si lo deseas en el futuro
            window.location.href = "{{ url('/') }}";
        }
    </script>
@endsection