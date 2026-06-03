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

</div>
@endsection