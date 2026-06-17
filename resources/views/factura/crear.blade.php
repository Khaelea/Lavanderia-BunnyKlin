@extends('layouts.app')

@section('content')
<div x-data="historialSystem()" class="mx-auto max-w-screen-2xl p-4 md:p-6 font-nunito">    
    {{-- Notificaciones de éxito/error arriba de todo --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {!! session('success') !!}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- COLUMNA IZQUIERDA: TABLA DE HISTORIAL (Ocupa 8 de 12 columnas) --}}
        <div class="lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-xl font-black text-[#1E55AA]">Ventas Registradas</h2>
                </div>
                
                <div class="max-w-full overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-slate-50 text-left text-[#1E55AA] border-b border-slate-100">
                                <th class="py-4 px-4 font-black">Folio</th>
                                <th class="py-4 px-4 font-black">Fecha</th>
                                <th class="py-4 px-4 font-black text-right">Total</th>
                                <th class="py-4 px-4 font-black text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Mientras carga --}}
                            <template x-if="cargando">
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-slate-400 animate-pulse">Cargando ventas...</td>
                                </tr>
                            </template>

                            {{-- 2. Si ya terminó de cargar Y el array sigue vacío --}}
                            <template x-if="!cargando && ventas.length === 0">
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-slate-400 font-bold">No hay ventas para mostrar.</td>
                                </tr>
                            </template>

                            <template x-for="venta in ventas" :key="venta.id">
                                <tr class="border-b border-slate-50 hover:bg-blue-50 transition-colors">
                                    <td class="py-4 px-4 font-bold text-[#1E55AA]" x-text="venta.reference || venta.id"></td>
                                    
                                    <td class="py-4 px-4 text-sm text-slate-500" x-text="new Date(venta.created_at).toLocaleString('es-MX')"></td>
                                    
                                    <td class="py-4 px-4 font-black text-right text-emerald-600" x-text="formatMoney(venta.total)"></td>
                                    
                                    <td class="py-4 px-4 text-center">
                                        <button 
                                            type="button"
                                            class="text-blue-600 hover:underline font-bold text-xs" 
                                            @click="seleccionarVenta(venta)">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- CONTROLES DE PAGINACIÓN --}}
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between" x-show="lastPage > 1">
                    <div class="text-sm text-slate-500">
                        Mostrando página <span class="font-bold" x-text="currentPage"></span> de <span class="font-bold" x-text="lastPage"></span>
                    </div>
                    <div class="flex gap-2">
                        <button 
                            type="button"
                            class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm font-bold transition-all"
                            :class="currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-700 hover:bg-slate-50'"
                            :disabled="currentPage === 1"
                            @click="cambiarPagina(currentPage - 1)">
                            Anterior
                        </button>
                        <button 
                            type="button"
                            class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm font-bold transition-all"
                            :class="currentPage === lastPage ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-700 hover:bg-slate-50'"
                            :disabled="currentPage === lastPage"
                            @click="cambiarPagina(currentPage + 1)">
                            Siguiente
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- COLUMNA DERECHA: FORMULARIO DE FACTURACIÓN (Ocupa 4 de 12 columnas) --}}
        <div class="lg:col-span-4">
            <div class="bg-white p-6 rounded-2xl shadow-md border border-slate-100 sticky top-6">
                <div class="flex items-center gap-2 mb-6">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Datos de Facturación</h2>
                </div>

                <form action="{{ route('venta.facturar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="venta_data" :value="JSON.stringify(ventaSeleccionada)">
                    
                    <template x-if="ventaSeleccionada">
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                            <p class="text-xs text-blue-600 font-bold uppercase">Venta Seleccionada:</p>
                            <p class="text-sm font-black text-slate-700" x-text="'Folio: ' + ventaSeleccionada.reference"></p>
                            <p class="text-sm text-emerald-600 font-bold" x-text="'Total: ' + formatMoney(ventaSeleccionada.total)"></p>
                        </div>
                    </template>

                    <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-xl relative">

                        <h3 class="font-bold text-blue-700 mb-1">
                            Cliente Registrado
                        </h3>

                        <p class="text-sm text-blue-600 mb-3">
                            Busca por nombre, RFC o correo para autocompletar los datos fiscales.
                        </p>

                        <input
                            type="text"
                            x-model="busquedaCliente"
                            @input.debounce.500ms="buscarClientes()"
                            placeholder="Buscar cliente..."
                            class="w-full border border-blue-300 rounded-lg p-2.5 bg-white"
                        >

                        <!-- Resultados -->
                        <div
                            x-show="clientesEncontrados.length > 0"
                            class="absolute left-4 right-4 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto"
                        >

                            <template
                                x-for="cliente in clientesEncontrados"
                                :key="cliente.id"
                            >

                                <div
                                    @click="seleccionarCliente(cliente)"
                                    class="p-3 border-b hover:bg-blue-50 cursor-pointer"
                                >

                                    <div
                                        class="font-semibold"
                                        x-text="cliente.name"
                                    ></div>

                                    <div
                                        class="text-sm text-slate-500"
                                        x-text="cliente.rfc"
                                    ></div>

                                </div>

                            </template>

                        </div>

                        <div
                            x-show="form.legal_name"
                            class="mt-3 p-2 bg-green-50 border border-green-200 rounded-lg"
                        >
                            <span class="text-green-700 text-sm font-semibold">
                                Cliente seleccionado:
                            </span>

                            <span
                                class="text-green-800"
                                x-text="form.legal_name"
                            ></span>
                        </div>

                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre Legal / Razón Social</label>
                            <input type="text" name="legal_name" x-model="form.legal_name" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Ej. Juan Pérez" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">RFC</label>
                            <input type="text" name="tax_id" x-model="form.tax_id" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="XAXX010101000" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Régimen Fiscal</label>
                            <select name="tax_system" x-model="form.tax_system" class="w-full border border-slate-300 rounded-xl p-2.5 bg-white">
                                <option value="NA" selected> Selecciona el Régimen Fiscal</option>
                                <option value="601">601 - General de Ley Personas Morales</option>
                                <option value="603">603 - Personas Morales con Fines no Lucrativos</option>
                                <option value="605">605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>
                                <option value="606">606 - Arrendamiento</option>
                                <option value="607">607 - Régimen de Enajenación o Adquisición de Bienes</option>
                                <option value="608">608 - Demás ingresos</option>
                                <option value="610">610 - Residentes en el Extranjero sin Establecimiento Permanente en México</option>
                                <option value="611">611 - Ingresos por Dividendos (socios y accionistas)</option>
                                <option value="612">612 - Personas Físicas con Actividades Empresariales y Profesionales</option>
                                <option value="614">614 - Ingresos por Intereses</option>
                                <option value="615">615 - Régimen de los ingresos por obtención de premios</option>
                                <option value="616">616 - Sin obligaciones fiscales</option>
                                <option value="620">620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>
                                <option value="621">621 - Incorporación Fiscal (RIF)</option>
                                <option value="622">622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras (AGAPES)</option>
                                <option value="623">623 - Opcional para Grupos de Sociedades</option>
                                <option value="624">624 - Coordinados</option>
                                <option value="625">625 - Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>
                                <option value="626">626 - Régimen Simplificado de Confianza (RESICO)</option>
                                <option value="628">628 - Hidrocarburos</option>
                                <option value="629">629 - De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales</option>
                                <option value="630">630 - Enajenación de acciones en bolsa de valores</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Uso de CFDI</label>
                            <select name="use_cfdi" class="w-full border border-slate-300 rounded-xl p-2.5 bg-white">
                                <option value="NA" selected> Selecciona el CFDI</option>
                                <option value="G01">G01 - Adquisición de mercancías</option>
                                <option value="G02">G02 - Devoluciones, descuentos o bonificaciones</option>
                                <option value="G03">G03 - Gastos en general</option>
                                
                                <option value="I01">I01 - Construcciones</option>
                                <option value="I02">I02 - Mobiliario y equipo de oficina por inversiones</option>
                                <option value="I03">I03 - Equipo de transporte</option>
                                <option value="I04">I04 - Equipo de cómputo y accesorios</option>
                                <option value="I05">I05 - Dados, troqueles, moldes, matrices y herramental</option>
                                <option value="I06">I06 - Comunicaciones telefónicas</option>
                                <option value="I07">I07 - Comunicaciones satelitales</option>
                                <option value="I08">I08 - Otra maquinaria y equipo</option>
                                
                                <option value="D01">D01 - Honorarios médicos, dentales y gastos hospitalarios</option>
                                <option value="D02">D02 - Gastos médicos por incapacidad o discapacidad</option>
                                <option value="D03">D03 - Gastos funerales</option>
                                <option value="D04">D04 - Donativos</option>
                                <option value="D05">D05 - Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>
                                <option value="D06">D06 - Aportaciones voluntarias al SAR</option>
                                <option value="D07">D07 - Primas por seguros de gastos médicos</option>
                                <option value="D08">D08 - Gastos de transportación escolar obligatoria</option>
                                <option value="D09">D09 - Depósitos en cuentas especiales para el ahorro, primas que tengan como base planes de pensiones</option>
                                <option value="D10">D10 - Pagos por servicios educativos (colegiaturas)</option>
                                
                                <option value="S01">S01 - Sin efectos fiscales</option>
                                <option value="CP01">CP01 - Pagos</option>
                                <option value="CN01">CN01 - Nómina</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Método de Pago --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Método de Pago</label>
                                <select name="payment_method" class="w-full border border-slate-300 rounded-xl p-2.5 bg-white">
                                    <option value="NA" selected>Selecciona un método de pago</option>
                                    <option value="PUE">PUE - Una sola exhibición</option>
                                    <option value="PPD">PPD - Parcialidades o Diferido</option>
                                </select>
                            </div>
                            {{-- Forma de Pago (Tú lo llamas Formato de pago) --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Forma de Pago</label>
                                <select name="payment_form" class="w-full border border-slate-300 rounded-xl p-2.5 bg-white">
                                    <option value="NA" selected>Selecciona una forma de pago</option>
                                    <option value="01">01 - Efectivo</option>
                                    <option value="02">02 - Cheque nominativo</option>
                                    <option value="03">03 - Transferencia electrónica de fondos</option>
                                    <option value="04">04 - Tarjeta de crédito</option>
                                    <option value="05">05 - Monedero electrónico</option>
                                    <option value="06">06 - Dinero electrónico</option>
                                    <option value="08">08 - Vales de despensa</option>
                                    <option value="12">12 - Dación en pago</option>
                                    <option value="13">13 - Pago por subrogación</option>
                                    <option value="14">14 - Pago por consignación</option>
                                    <option value="15">15 - Condonación</option>
                                    <option value="17">17 - Compensación</option>
                                    <option value="23">23 - Novación</option>
                                    <option value="24">24 - Confusión</option>
                                    <option value="25">25 - Remisión de deuda</option>
                                    <option value="26">26 - Prescripción o caducidad</option>
                                    <option value="27">27 - A satisfacción del acreedor</option>
                                    <option value="28">28 - Tarjeta de débito</option>
                                    <option value="29">29 - Tarjeta de servicios</option>
                                    <option value="30">30 - Aplicación de anticipos</option>
                                    <option value="31">31 - Intermediarios pagos</option>
                                    <option value="99">99 - Por definir</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" x-model="form.email" class="w-full border border-slate-300 rounded-xl p-2.5" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Código Postal</label>
                                <input type="text" name="zip" x-model="form.zip" class="w-full border border-slate-300 rounded-xl p-2.5" required>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="!ventaSeleccionada"
                            :class="!ventaSeleccionada ? 'bg-slate-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="w-full text-white font-bold py-3 rounded-xl transition-all shadow-lg"
                        >
                            <span x-show="ventaSeleccionada">Generar Factura</span>
                            <span x-show="!ventaSeleccionada">Seleccione una venta de la tabla</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function historialSystem() {
        return {
            ventas: [],
            ventaSeleccionada: null,
            cargando: true,
            error: false,
            currentPage: 1,
            lastPage: 1,
            totalFiltro: 0,

            clientesEncontrados: [],
            busquedaCliente: '',

            form: {
                legal_name: '',
                tax_id: '',
                tax_system: '',
                email: '',
                zip: ''
            },

            async init() {
                this.cargarVentas();
            },

            async cargarVentas(page = 1) {
                this.cargando = true;
                this.error = false;

                try {
                    const response = await fetch(`/ventas/api-historial?page=${page}`);
                    if (!response.ok) throw new Error('Error al obtener datos');

                    const resultado = await response.json();
                    const paginador = resultado.paginacion;

                    if (!paginador) {
                        throw new Error('Respuesta sin estructura de paginación esperada');
                    }

                    this.ventas = paginador.data ?? [];
                    this.currentPage = paginador.current_page ?? 1;
                    this.lastPage = paginador.last_page ?? 1;
                    this.totalFiltro = resultado.total_filtro ?? 0;

                } catch (error) {
                    console.error("Error cargando el historial:", error);
                    this.ventas = [];
                    this.error = true;
                } finally {
                    this.cargando = false;
                }
            },

            cambiarPagina(nuevaPagina) {
                if (nuevaPagina >= 1 && nuevaPagina <= this.lastPage) {
                    this.cargarVentas(nuevaPagina);
                }
            },

            seleccionarVenta(venta) {
                // Mapeamos los datos para que el controlador de factura los entienda
                // Si en tu base de datos la relación se llama 'items', 
                // la convertimos a 'detalles' para que tu FacturaController no falle.
                this.ventaSeleccionada = {
                    ...venta,
                    detalles: venta.items || venta.detalles // Asegura compatibilidad
                };
            },

            formatMoney(amount) {
                return '$' + Number(amount).toLocaleString('es-MX', { 
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2 
                });
            },

            async buscarClientes() {
                if (this.busquedaCliente.length < 3) {
                    this.clientesEncontrados = [];
                    return;
                }

                try {
                    const response = await fetch(
                        `/clientes/buscar?search=${encodeURIComponent(this.busquedaCliente)}`
                    );

                    this.clientesEncontrados = await response.json();
                } catch (error) {
                    console.error(error);
                    this.clientesEncontrados = [];
                }
            },

            seleccionarCliente(cliente) {
                this.form.legal_name = cliente.name;
                this.form.tax_id = cliente.rfc;
                this.form.tax_system = cliente.regimen_fiscal;
                this.form.email = cliente.email;
                this.form.zip = cliente.codigo_postal;

                this.busquedaCliente = cliente.name;

                this.clientesEncontrados = [];
            },
        }
    }
</script>
@endsection