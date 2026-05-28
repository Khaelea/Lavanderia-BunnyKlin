@extends('layouts.app')

@section('content')
<div x-data="inventorySystem()" class="p-6 bg-[#F4F8FC] min-h-screen font-nunito">
    
    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-8 max-w-[1600px] mx-auto">
        <h1 class="text-2xl font-black text-[#1E55AA]">Inventario de Insumos</h1>
        <button class="bg-[#1E55AA] hover:bg-[#153e7d] text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-[#1E55AA]/20 transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Registrar Compra
        </button>
    </div>

    <div class="max-w-[1600px] mx-auto space-y-6">
        {{-- Tarjetas de Métricas --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Tarjeta 1: Total de Productos --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="w-12 h-12 bg-blue-50 text-[#1E55AA] rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="text-3xl font-black text-slate-800" x-text="items.length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Total de Productos</p>
            </div>

            {{-- Tarjeta 2: Stock Bajo --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-yellow-100 relative overflow-hidden">
                <div class="absolute top-4 right-4 bg-yellow-100 text-yellow-700 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Por Agotar
                </div>
                <div class="w-12 h-12 bg-yellow-100 text-yellow-500 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <h3 class="text-3xl font-black text-yellow-500" x-text="items.filter(i => i.status === 'Stock Bajo').length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Stock Bajo</p>
            </div>

            {{-- Tarjeta 3: Sin Stock --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border-2 border-rose-100 relative overflow-hidden">
                <div class="absolute top-4 right-4 bg-rose-100 text-rose-700 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Agotado
                </div>
                <div class="w-12 h-12 bg-rose-100 text-rose-500 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-3xl font-black text-rose-500" x-text="items.filter(i => i.status === 'Sin Stock').length"></h3>
                <p class="text-sm font-bold text-slate-400 mt-1">Sin Stock</p>
            </div>
        </div>

        {{-- Tabla de Inventario --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-6">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-sm font-black">
                            <th class="p-5 pl-6">Producto</th>
                            <th class="p-5">Categoría</th>
                            <th class="p-5">Stock Actual</th>
                            <th class="p-5">Stock Mínimo</th>
                            <th class="p-5">Precio Unitario</th>
                            <th class="p-5 pr-6">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="item in items" :key="item.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-5 pl-6 text-sm font-bold text-slate-700" x-text="item.name"></td>
                                <td class="p-5 text-sm font-semibold text-slate-400" x-text="item.category"></td>
                                
                                {{-- Stock Actual con color condicional --}}
                                <td class="p-5 text-sm font-black" 
                                    :class="{
                                        'text-slate-700': item.status === 'Normal',
                                        'text-yellow-500': item.status === 'Stock Bajo',
                                        'text-rose-500': item.status === 'Sin Stock'
                                    }" 
                                    x-text="item.stock"></td>
                                
                                <td class="p-5 text-sm font-semibold text-slate-500" x-text="item.min_stock"></td>
                                <td class="p-5 text-sm font-black text-[#1E55AA]" x-text="formatMoney(item.price)"></td>
                                
                                {{-- Badge de Estado condicional --}}
                                <td class="p-5 pr-6">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider"
                                          :class="{
                                              'bg-emerald-100 text-emerald-600': item.status === 'Normal',
                                              'bg-yellow-100 text-yellow-600': item.status === 'Stock Bajo',
                                              'bg-rose-100 text-rose-600': item.status === 'Sin Stock'
                                          }">
                                        <svg x-show="item.status === 'Normal'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        <svg x-show="item.status === 'Stock Bajo'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        <svg x-show="item.status === 'Sin Stock'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span x-text="item.status"></span>
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="items.length === 0" class="p-8 text-center text-slate-400 font-bold">
                    No hay insumos registrados. ¡Agrega uno desde el POS!
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('inventorySystem', () => ({
        
        items: @json($insumosDb).map(item => {
            // El mínimo es 10 (lo dejamos fijo porque aún no lo tienes en DB)
            let min_stock = 10; 
            // Si el item viene nulo, es 0
            let current_stock = item.stock || 0;
            
            // LA NUEVA LÓGICA QUE PEDISTE:
            let status = 'Normal';
            
            if (current_stock <= 0) {
                // Si es 0 o negativo, ya no hay nada
                status = 'Sin Stock';
            } else if (current_stock < min_stock) {
                // Si tienes entre 1 y 9, es alerta de que se va a acabar
                status = 'Stock Bajo';
            }
            // Si tienes 10 o más, se queda como 'Normal'
            
            return { 
                id: item.id, 
                name: item.name, 
                category: 'Insumos', 
                stock: current_stock, 
                min_stock: min_stock, 
                price: parseFloat(item.price),
                status: status 
            };
        }),

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