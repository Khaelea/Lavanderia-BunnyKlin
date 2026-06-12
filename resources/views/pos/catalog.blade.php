{{-- Botones de Modo --}}

{{-- Servicios --}}
<section>
    <div class="flex items-center gap-3 mb-6 pl-2">
        <div class="h-8 w-2 bg-[#1E55AA] rounded-full"></div>
        <h2 class="text-2xl font-black text-[#1E55AA] uppercase tracking-widest">Servicios</h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
        <template x-for="item in services" :key="item.id">
            <button @click="handleItemClick(item, 'services')" class="relative flex flex-col p-5 rounded-2xl bg-white transition-all text-left border-2 shadow-sm active:scale-95" :class="{ 'border-[#FFE63C] border-dashed bg-[#FFE63C]/10 hover:bg-[#FFE63C]/20': activeMode === 'edit', 'border-rose-400 border-dashed bg-rose-50 hover:bg-rose-100': activeMode === 'delete', 'border-[#1E55AA]/10 hover:border-[#1E55AA] hover:bg-[#1E55AA]/5': activeMode === 'sale' }">
                <span class="font-black text-[#1E55AA] text-lg z-10 leading-tight mb-1" x-text="item.name"></span>
                <span class="text-xl font-extrabold text-[#1E55AA]/70 z-10" x-text="formatMoney(item.price)"></span>
            </button>
        </template>
    </div>
</section>

{{-- Insumos --}}
<section class="mt-10">
    <div class="flex items-center gap-3 mb-6 pl-2">
        <div class="h-8 w-2 bg-[#FFE63C] rounded-full"></div>
        <h2 class="text-2xl font-black text-[#1E55AA] uppercase tracking-widest">Insumos</h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
        <template x-for="item in supplies" :key="item.id">

            {{-- Botón de Insumo --}}
            <button @click="handleItemClick(item, 'supplies')"
                :disabled="activeMode === 'sale' && item.stock <= 0"
                class="relative flex flex-col p-5 rounded-2xl transition-all text-left border-2 shadow-sm"
                class="relative flex flex-col p-5 rounded-2xl bg-white transition-all text-left border-2 shadow-sm active:scale-95" :class="{ 'border-[#FFE63C] border-dashed bg-[#FFE63C]/10 hover:bg-[#FFE63C]/20': activeMode === 'edit', 'border-rose-400 border-dashed bg-rose-50 hover:bg-rose-100': activeMode === 'delete', 'border-[#1E55AA]/10 hover:border-[#1E55AA] hover:bg-[#1E55AA]/5': activeMode === 'sale' }">

                {{-- NUEVO: Etiqueta indicadora de Stock --}}
                <div class="absolute top-3 right-3 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider"
                     :class="item.stock > 5 ? 'bg-emerald-100 text-emerald-600' : (item.stock > 0 ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600')">
                    <span x-text="item.stock > 0 ? item.stock + ' en stock' : 'Agotado'"></span>
                </div>

                {{-- Textos del botón (con un margen superior para no chocar con el badge) --}}
                <span class="font-black text-[#1E55AA] text-lg z-10 leading-tight mb-1 mt-3" x-text="item.name"></span>
                <span class="text-xl font-extrabold text-[#1E55AA]/70 z-10" x-text="formatMoney(item.price)"></span>
            </button>

        </template>

    </div>
</section>

{{-- Suscripciones --}}
<section class="mt-10">
    <div class="flex items-center gap-3 mb-6 pl-2">
        <div class="h-8 w-2 bg-emerald-500 rounded-full"></div>
        <h2 class="text-2xl font-black text-[#1E55AA] uppercase tracking-widest">Suscripciones</h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
        <template x-for="item in subscriptions" :key="item.id">
            <button @click="handleItemClick(item, 'subscriptions')" class="relative flex flex-col p-5 rounded-2xl bg-white transition-all text-left border-2 shadow-sm active:scale-95" :class="{ 'border-[#FFE63C] border-dashed bg-[#FFE63C]/10 hover:bg-[#FFE63C]/20': activeMode === 'edit', 'border-rose-400 border-dashed bg-rose-50 hover:bg-rose-100': activeMode === 'delete', 'border-emerald-200 hover:border-emerald-500 hover:bg-emerald-50': activeMode === 'sale' }">
                <span class="font-black text-[#1E55AA] text-lg z-10 leading-tight mb-1" x-text="item.name"></span>
                <span class="text-xl font-extrabold text-emerald-600 z-10" x-text="formatMoney(item.price)"></span>
            </button>
        </template>

    </div>
</section>