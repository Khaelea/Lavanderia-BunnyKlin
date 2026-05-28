@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pos.css') }}">
<script src="{{ asset('js/pos.js?v=' . time()) }}"></script>
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="posSystem({{ Js::from($services ?? []) }}, {{ Js::from($supplies ?? []) }}, {{ Js::from($subscriptions ?? []) }})" class="font-nunito relative min-h-screen pb-12 bg-[#F4F8FC] text-[#1E55AA] selection:bg-[#FFE63C] selection:text-[#1E55AA]">

    {{-- Fondo Decorativo --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-5%] w-[40vw] h-[40vw] rounded-full bg-[#1E55AA]/5 blur-[100px] animate-float"></div>
        <div class="absolute bottom-[-15%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-[#1E55AA]/10 blur-[120px] animate-float-delayed"></div>
        <div class="absolute top-[20%] right-[15%] w-[25vw] h-[25vw] rounded-full bg-[#FFE63C]/10 blur-[80px] animate-float" style="animation-duration: 7s;"></div>
    </div>

    {{-- MAIN LAYOUT --}}
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- IZQUIERDA: CATÁLOGO --}}
            <div class="lg:col-span-8 space-y-8">
                @include('pos.catalog')
            </div>

            {{-- DERECHA: CANASTA --}}
            <div class="lg:col-span-4">
                @include('pos.cart')
            </div>
            
        </div>
    </div>
    
    {{-- MODALES NORMALES --}}
    @include('pos.modals')

    {{-- 🔥 NUEVO: MODAL DE ESPERANDO TERMINAL (RELOJ Y CANCELAR) 🔥 --}}
    <div x-show="esperandoTerminal" 
         class="fixed inset-0 z-[999] flex items-center justify-center bg-black bg-opacity-50" 
         style="display: none;" 
         x-transition x-cloak>
        <div class="bg-white p-8 rounded-lg shadow-2xl text-center max-w-sm w-full mx-4">
            
            <div class="mb-4">
                <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <h3 class="text-2xl font-bold text-gray-800 mb-2">Procesando Pago</h3>
            <p class="text-gray-600 mb-6">Por favor, usa la terminal física para completar la transacción.</p>

            <div class="text-4xl font-mono font-black text-red-500 mb-8 bg-red-50 py-3 rounded-xl border border-red-100" 
                 x-text="tiempoFormateado">
                05:00
            </div>

            <button type="button"
                    @click="cancelarCobroTerminal('Pago cancelado por el usuario')" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md">
                Cancelar Operación
            </button>
        </div>
    </div>
</div>
@endsection