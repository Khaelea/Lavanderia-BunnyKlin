@extends('layouts.app')

@section('content')
<div x-data="{ openDetail: false, info: {} }" 
     @open-calendar-modal.window="info = $event.detail; openDetail = true"
     class="max-w-[1600px] mx-auto p-4 lg:p-6">

    <!-- CABECERA ESTILO DASHBOARD -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#1E55AA]">Calendario</h1>
        </div>
        
        <!-- Controles de Navegación -->
        <!-- Este es tu botón en la parte superior derecha, fuera del calendario -->
        <div class="flex items-center bg-white dark:bg-gray-800 rounded-xl shadow-sm border p-1">
            <!-- Botón Anterior: ID btn-prev -->
            <button id="btn-prev" onclick="changeDate(this.getAttribute('data-m'), this.getAttribute('data-y'))" 
                    data-m="{{ $data['prevMonth'] }}" data-y="{{ $data['prevYear'] }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <!-- Texto Central: ID current-month-display -->
            <span id="current-month-display" class="px-4 font-bold text-slate-700 capitalize min-w-[120px] text-center">
                {{ $data['currentMonthName'] }} {{ $data['currentYear'] }}
            </span>

            <!-- Botón Siguiente: ID btn-next -->
            <button id="btn-next" onclick="changeDate(this.getAttribute('data-m'), this.getAttribute('data-y'))" 
                    data-m="{{ $data['nextMonth'] }}" data-y="{{ $data['nextYear'] }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>

    <!-- GRID PRINCIPAL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- COLUMNA IZQUIERDA: EL CALENDARIO (8/12) -->
        <div class="lg:col-span-8 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-sm border border-slate-100 dark:border-gray-800 p-2 lg:p-6">
            <div id="calendar-container">
                <x-newcalender :data="$data" />
            </div>
        </div>

        <!-- COLUMNA DERECHA: PRÓXIMOS EVENTOS (4/12) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-sm border border-slate-100 dark:border-gray-800 p-8 h-full min-h-[600px]">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-2xl text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Próximos Eventos</h2>
                </div>

                <!-- INSERTAR AQUÍ: Los botones de filtro -->
                <div class="flex gap-2 mb-6" id="filtros-sidebar">
                    <button onclick="filtrarPorEstructura('todos')" class="filter-btn active px-3 py-1 rounded-full text-[10px] font-black uppercase bg-slate-900 text-white transition">
                        Todos
                    </button>
                    <button onclick="filtrarPorEstructura('pedidos')" class="filter-btn px-3 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-500 hover:bg-blue-600 hover:text-white transition">
                        Pedidos
                    </button>
                    <button onclick="filtrarPorEstructura('suscripciones')" class="filter-btn px-3 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-500 hover:bg-purple-600 hover:text-white transition">
                        Suscripciones
                    </button>
                </div>

                <!-- LISTA CON LÍMITE Y SCROLL (Cambios aquí) -->
                <!-- Busca este bloque y asegúrate de que solo exista UNA VEZ -->
                <div class="relative flex-1"> <!-- Añadimos flex-1 para que ocupe el espacio -->
                    <div id="upcoming-events-list" 
                        class="space-y-5 overflow-y-auto pr-2 custom-scrollbar" 
                        style="max-height: 650px;"> <!-- Aumentamos un poco el alto para que quepan los 6 -->
                        <!-- Se rellena con JS -->
                    </div>
                    <!-- Sombra de desvanecimiento inferior -->
                    <div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-white dark:from-gray-900 to-transparent pointer-events-none"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DETALLE (OVERLAY) -->
    <div x-show="openDetail" 
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
         x-transition:opacity>
        
        <div @click.away="openDetail = false" 
             class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl max-w-sm w-full overflow-hidden border dark:border-gray-700 transform transition-all">
            
            <div class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-slate-50/50 dark:bg-gray-900/50">
                <h3 class="font-black text-slate-800 dark:text-white uppercase tracking-tighter" x-text="info.titulo"></h3>
                <button @click="openDetail = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-8 space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-gray-700 flex items-center justify-center text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-black">Cliente</p>
                        <p class="text-base font-bold dark:text-gray-200" x-text="info.cliente"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Fecha de Llegada -->
                    <div class="bg-slate-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-slate-100 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <p class="text-[9px] text-slate-400 uppercase font-black">Recibido</p>
                        </div>
                        <p class="text-xs font-bold dark:text-gray-200" x-text="info.fechaLlegada || 'No registrada'"></p>
                    </div>

                    <!-- Fecha de Entrega -->
                    <div class="bg-slate-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-slate-100 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                            <p class="text-[9px] text-slate-400 uppercase font-black">Entrega</p>
                        </div>
                        <p class="text-xs font-bold dark:text-gray-200" x-text="info.fechaEntrega || 'Pendiente'"></p>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-gray-900 rounded-3xl p-6 border border-slate-100 dark:border-gray-700">
                    <p class="text-[10px] text-slate-400 uppercase font-black mb-4">Resumen de Servicio</p>
                    
                    {{-- Servicio --}}
                    <div class="mb-4">
                        <p class="text-[9px] text-slate-400 uppercase font-black mb-1">Detalles</p>
                        <p class="text-sm font-bold dark:text-gray-200" x-text="info.detalles[0].name"></p>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <p class="text-[9px] text-slate-400 uppercase font-black mb-1">Estado</p>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase"
                            :class="{
                                'bg-green-100 text-green-700': info.detalles[1].name === 'Listo',
                                'bg-amber-100 text-amber-700': info.detalles[1].name === 'Recibido',
                                'bg-blue-100 text-blue-700':   info.detalles[1].name === 'En proceso',
                                'bg-slate-100 text-slate-500': !['Listo','Recibido','En proceso'].includes(info.detalles[1].name)
                            }"
                            x-text="info.detalles[1].name">
                        </span>
                    </div>

                    {{-- Total --}}
                    <div class="border-t dark:border-gray-700 mt-4 pt-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase">Total</span>
                        <span class="text-xl font-black text-blue-600 dark:text-blue-400" 
                            x-text="typeof info.total === 'number' ? '$' + info.total.toFixed(2) : '$' + info.total">
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-slate-50/50 dark:bg-gray-900/50">
                <button @click="openDetail = false" class="w-full bg-slate-900 dark:bg-blue-600 hover:scale-[1.02] text-white py-4 rounded-2xl font-bold transition-all shadow-xl active:scale-95">
                    Cerrar Detalle
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL SUSCRIPCIÓN -->
    <div x-data="{ openSub: false, sub: {} }"
        @open-subscription-modal.window="sub = $event.detail; openSub = true"
        x-show="openSub"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
        x-transition:opacity>

        <div @click.away="openSub = false"
            class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl max-w-sm w-full overflow-hidden border dark:border-gray-700">

            {{-- Header morado --}}
            <div class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-purple-50 dark:bg-purple-900/30">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-xl text-purple-600 dark:text-purple-300">
                        ✨
                    </div>
                    <h3 class="font-black text-purple-800 dark:text-purple-200 uppercase tracking-tighter">Suscripción Activa</h3>
                </div>
                <button @click="openSub = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-8 space-y-6">
                {{-- Cliente --}}
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-black">Cliente</p>
                        <p class="text-base font-bold dark:text-gray-200" x-text="sub.nombre"></p>
                    </div>
                </div>

                {{-- Plan y Status --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-2xl border border-purple-100 dark:border-purple-800">
                        <p class="text-[9px] text-purple-400 uppercase font-black mb-1">Plan</p>
                        <p class="text-sm font-bold text-purple-700 dark:text-purple-300" x-text="sub.plan"></p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-2xl border border-purple-100 dark:border-purple-800">
                        <p class="text-[9px] text-purple-400 uppercase font-black mb-1">Status</p>
                        <p class="text-sm font-bold text-purple-700 dark:text-purple-300" x-text="sub.status"></p>
                    </div>
                </div>

                {{-- Fechas --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-slate-100 dark:border-gray-700">
                        <p class="text-[9px] text-slate-400 uppercase font-black mb-1">Inicio</p>
                        <p class="text-xs font-bold dark:text-gray-200" x-text="sub.inicio || 'No registrada'"></p>
                    </div>
                    <div class="bg-slate-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-slate-100 dark:border-gray-700">
                        <p class="text-[9px] text-slate-400 uppercase font-black mb-1">Vencimiento</p>
                        <p class="text-xs font-bold dark:text-gray-200" x-text="sub.fin || 'Sin fecha'"></p>
                    </div>
                </div>

                {{-- Detalles del plan --}}
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-3xl p-6 border border-purple-100 dark:border-purple-800">
                    <p class="text-[10px] text-purple-400 uppercase font-black mb-4">Detalles del Plan</p>
                    <div class="flex justify-between text-sm mb-2 dark:text-gray-300">
                        <span class="font-medium text-slate-600 dark:text-slate-400">Kilos por mes</span>
                        <span class="font-bold text-purple-600" x-text="sub.kilos + ' kg'"></span>
                    </div>
                    <div class="border-t border-purple-100 dark:border-purple-800 mt-4 pt-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase">Precio</span>
                        <span class="text-xl font-black text-purple-600 dark:text-purple-400" x-text="'$' + Number(sub.precio).toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-purple-50/50 dark:bg-purple-900/20">
                <button @click="openSub = false" class="w-full bg-purple-600 hover:scale-[1.02] text-white py-4 rounded-2xl font-bold transition-all shadow-xl active:scale-95">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const CALENDAR_URL = "{{ route('calendar.index') }}";

    window.CalendarData = {
        orders:         @json($orders),
        subscriptions:  @json($subscriptions),
    };

    console.log('CalendarData:', window.CalendarData);
</script>
@push('scripts')
    <script src="{{ asset('js/calendar-logic.js') }}?v={{ time() }}"></script>
@endpush

<style>
    [x-cloak] { display: none !important; }
    /* Scroll suave para la lista */
    #upcoming-events-list { scrollbar-width: none; }
    #upcoming-events-list::-webkit-scrollbar { display: none; }
</style>
@endsection