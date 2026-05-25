{{-- Botones de Modo --}}
<div class="flex justify-end gap-3 mb-2">
    <button @click="toggleMode('edit')" :class="activeMode === 'edit' ? 'bg-[#FFE63C] text-[#1E55AA] border-transparent shadow-md' : 'bg-white text-[#1E55AA] border-[#1E55AA]/20 hover:border-[#1E55AA] hover:bg-[#F4F8FC]'" class="px-6 py-3 rounded-xl border-2 text-sm font-extrabold transition-all flex items-center gap-2 shadow-sm active:scale-95">
        <svg x-show="activeMode !== 'edit'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        <span x-text="activeMode === 'edit' ? 'Terminar Edición' : 'Editar'"></span>
    </button>
    <button @click="toggleMode('delete')" :class="activeMode === 'delete' ? 'bg-rose-500 text-white border-transparent shadow-md' : 'bg-white text-rose-500 border-rose-200 hover:border-rose-500 hover:bg-rose-50'" class="px-6 py-3 rounded-xl border-2 text-sm font-extrabold transition-all flex items-center gap-2 shadow-sm active:scale-95">
        <svg x-show="activeMode !== 'delete'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        <span x-text="activeMode === 'delete' ? 'Terminar Eliminación' : 'Eliminar'"></span>
    </button>
</div>

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
        <button x-show="activeMode === 'edit'" @click="openAddModal('services')" class="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-dashed border-[#1E55AA]/30 text-[#1E55AA] bg-white hover:bg-[#F4F8FC] transition-colors active:scale-95">
            <div class="p-2 bg-[#1E55AA]/10 rounded-full mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg></div>
            <span class="font-bold text-sm">Añadir Servicio</span>
        </button>
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

        <button x-show="activeMode === 'edit'" @click="openAddModal('supplies')" class="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-dashed border-[#FFE63C] text-[#1E55AA] bg-white hover:bg-[#FFE63C]/10 transition-colors active:scale-95">
            <div class="p-2 bg-[#FFE63C]/20 rounded-full mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg></div>
            <span class="font-bold text-sm">Añadir Insumo</span>
        </button>
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
        <button x-show="activeMode === 'edit'" @click="openAddModal('subscriptions')" class="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-dashed border-emerald-300 text-emerald-600 bg-white hover:bg-emerald-50 transition-colors active:scale-95">
            <div class="p-2 bg-emerald-100 rounded-full mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg></div>
            <span class="font-bold text-sm">Añadir Plan</span>
        </button>
    </div>
</section>
