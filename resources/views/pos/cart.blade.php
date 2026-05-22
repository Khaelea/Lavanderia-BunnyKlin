<div class="bg-white rounded-3xl shadow-xl border-2 border-slate-100 p-6 flex flex-col h-[calc(100vh-8rem)] sticky top-8">

    {{-- Encabezado de la Canasta --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-black text-[#1E55AA]">Canasta</h2>
        <span class="bg-[#1E55AA]/10 text-[#1E55AA] font-bold text-xs py-1 px-3 rounded-xl" x-text="cart.length + ' items'"></span>
    </div>

    {{-- Lista de Productos --}}
    <div class="flex-1 overflow-y-auto space-y-2 pr-2 custom-scrollbar">

        <template x-if="cart.length === 0">
            <div class="flex flex-col items-center justify-center h-full text-center opacity-50">
                <svg class="w-12 h-12 text-[#1E55AA] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <p class="font-bold text-[#1E55AA]">No hay productos</p>
            </div>
        </template>

        <template x-for="(item, index) in cart" :key="item.cart_id">
            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200 flex flex-col gap-1.5">
                <div class="flex justify-between items-start">
                    <div class="flex-1 pr-2">
                        <h4 class="text-sm font-black text-[#1E55AA] leading-tight" x-text="item.name"></h4>
                        <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded uppercase mt-0.5 inline-block"
                              :class="{
                                  'bg-blue-100 text-blue-600': item.category === 'services',
                                  'bg-emerald-100 text-emerald-600': item.category === 'supplies',
                                  'bg-purple-100 text-purple-600': item.category === 'subscriptions',
                                  'bg-orange-100 text-orange-600': item.category === 'extras'
                              }"
                              x-text="item.category === 'services' ? 'Servicio' : (item.category === 'supplies' ? 'Insumo' : (item.category === 'subscriptions' ? 'Suscripción' : 'Extra'))">
                        </span>
                    </div>
                    <div class="text-sm font-black text-emerald-500 shrink-0" x-text="formatMoney(item.price * item.quantity)"></div>
                </div>

                <div class="flex items-center justify-between mt-1">
                    <div class="flex items-center bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden h-7">
                        <button @click="updateQty(index, -1)" class="w-7 h-full flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-rose-500 font-bold transition-colors">-</button>
                        <span class="w-6 text-center font-black text-[#1E55AA] text-xs" x-text="item.quantity"></span>
                        <button @click="updateQty(index, 1)" class="w-7 h-full flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-emerald-500 font-bold transition-colors">+</button>
                    </div>

                    <button @click="removeItem(index)" class="p-1.5 text-rose-400 hover:bg-rose-100 rounded-md transition-colors" title="Eliminar de la canasta">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
        </template>

    </div>

    {{-- Resumen y Botón de Cobrar --}}
    <div class="pt-4 mt-2 border-t-2 border-slate-100">
        <div class="flex justify-between items-end mb-4">
            <span class="text-sm font-bold text-[#1E55AA]/60">Total Venta:</span>
            <span class="text-3xl font-black text-emerald-500 leading-none" x-text="formatMoney(total)"></span>
        </div>

        <button
            @click="checkout()"
            :disabled="cart.length === 0"
            class="w-full py-3.5 rounded-xl font-black text-white bg-[#1E55AA] shadow-lg shadow-[#1E55AA]/20 hover:bg-[#153e7d] hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:shadow-none">
            Cobrar Venta
        </button>

        <button
            x-show="cart.length > 0"
            @click="clearCart()"
            class="w-full mt-2 py-1.5 text-sm font-bold text-slate-400 hover:text-rose-500 transition-colors">
            Vaciar Canasta
        </button>
    </div>
</div>
