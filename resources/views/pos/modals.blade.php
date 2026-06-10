{{-- Modal de Edición/Creación/Vista de Productos del POS --}}
<div x-cloak x-show="itemModal.open" 
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl border-2 border-slate-100 w-full max-w-md overflow-hidden animate-fade-in" @click.stop>

        {{-- Encabezado dinámico --}}
        <div class="p-6 border-b border-slate-100 bg-[#F4F8FC]">
            <h3 class="text-2xl font-black text-[#1E55AA]"
                x-text="itemModal.mode === 'add' ? 'Nuevo Elemento' : (itemModal.mode === 'edit' ? 'Editar Elemento' : (itemModal.mode === 'view' ? 'Detalles del Elemento' : 'Eliminar Elemento'))">
            </h3>
        </div>

        <div class="p-6 space-y-4">
            {{-- Formulario para Modos: Crear, Editar y Ver --}}
            <template x-if="itemModal.mode !== 'delete'">
                <form @submit.prevent="saveItem" class="space-y-4">

                    {{-- Campo: Estado (Activo/Inactivo) --}}
                    <div class="flex items-center gap-3 p-4 rounded-2xl border-2 transition-colors"
                         :class="itemModal.mode === 'view' ? 'bg-slate-50 border-slate-100' : 'bg-emerald-50/50 border-emerald-100/50 hover:border-emerald-200'">
                        <input type="checkbox" id="is_active" x-model="itemModal.is_active" :disabled="itemModal.mode === 'view'"
                            class="w-5 h-5 rounded-md border-slate-300 focus:ring-emerald-500 cursor-pointer"
                            :class="itemModal.mode === 'view' ? 'text-slate-400' : 'text-emerald-500'">
                        <label for="is_active" class="font-black cursor-pointer select-none"
                               :class="itemModal.mode === 'view' ? 'text-slate-600' : 'text-emerald-900'">
                            Elemento Activo (Visible en el catálogo)
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-[#1E55AA] mb-1">Nombre</label>
                        <input type="text" x-model="itemModal.name" required :disabled="itemModal.mode === 'view'"
                            class="w-full rounded-xl py-3 px-4 font-bold outline-none transition-all"
                            :class="itemModal.mode === 'view' ? 'bg-transparent text-slate-800 border border-slate-200 shadow-inner' : 'border-2 border-slate-100 bg-white text-[#1E55AA] focus:border-[#1E55AA] focus:ring-2 focus:ring-[#1E55AA]/10'">
                    </div>

                    {{-- Campo Clave SAT --}}
                    <div>
                        <label class="block text-sm font-black text-[#1E55AA] mb-1">Clave SAT</label>
                        <input type="text" x-model="itemModal.clave_prodserv" placeholder="80101500" maxlength="8" :disabled="itemModal.mode === 'view'"
                            class="w-full rounded-xl py-3 px-4 font-bold outline-none transition-all"
                            :class="itemModal.mode === 'view' ? 'bg-transparent text-slate-800 border border-slate-200 shadow-inner' : 'border-2 border-slate-100 bg-white text-[#1E55AA] focus:border-[#1E55AA]'">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-[#1E55AA] mb-1">Precio ($)</label>
                        <input type="number" step="0.5" x-model="itemModal.price" required :disabled="itemModal.mode === 'view'"
                            class="w-full rounded-xl py-3 px-4 font-bold outline-none transition-all"
                            :class="itemModal.mode === 'view' ? 'bg-transparent text-slate-800 border border-slate-200 shadow-inner' : 'border-2 border-slate-100 bg-white text-[#1E55AA] focus:border-[#1E55AA] focus:ring-2 focus:ring-[#1E55AA]/10'">
                    </div>

                    {{-- Campo: Descripción --}}
                    <div x-show="itemModal.category === 'services' || itemModal.category === 'subscriptions'" x-transition>
                        <label class="block text-sm font-extrabold text-[#1E55AA]/70 mb-2 ml-1">Descripción</label>
                        <textarea x-model="itemModal.description" rows="2" placeholder="Detalles adicionales..." :disabled="itemModal.mode === 'view'"
                            class="w-full px-5 py-3 rounded-xl font-bold focus:outline-none transition-colors"
                            :class="itemModal.mode === 'view' ? 'bg-transparent text-slate-800 border border-slate-200 shadow-inner' : 'bg-slate-50 border-2 border-slate-200 text-[#1E55AA] focus:border-[#1E55AA] focus:bg-white'"></textarea>
                    </div>

                    {{-- Campo: Checkbox Es Orden --}}
                    <div x-show="itemModal.category === 'services'"
                        class="flex items-center gap-3 p-4 rounded-2xl border-2 transition-colors"
                        :class="itemModal.mode === 'view' ? 'bg-slate-50 border-slate-100' : 'bg-slate-50 border-slate-100 hover:border-[#1E55AA]/30'">
                        <input type="checkbox" id="is_for_orders" x-model="itemModal.is_for_orders" :disabled="itemModal.mode === 'view' || itemModal.mode === 'delete'"
                            class="w-5 h-5 rounded-md border-slate-300 cursor-pointer"
                            :class="itemModal.mode === 'view' ? 'text-slate-400 focus:ring-slate-400' : 'text-[#1E55AA] focus:ring-[#1E55AA]'">
                        <label for="is_for_orders" class="font-black cursor-pointer select-none"
                               :class="itemModal.mode === 'view' ? 'text-slate-600' : 'text-[#1E55AA]'">
                            Este servicio es por encargo
                        </label>
                    </div>

                    {{-- Campos: Stock y Unidad --}}
                    <div x-show="itemModal.category === 'supplies'" class="grid grid-cols-2 gap-4" x-transition>
                        <div>
                            <label class="block text-sm font-extrabold text-[#1E55AA]/70 mb-2 ml-1">Stock</label>
                            <input type="number" x-model="itemModal.stock" placeholder="0" :disabled="itemModal.mode === 'view'"
                                 class="w-full px-5 py-3 rounded-xl font-bold focus:outline-none transition-colors"
                                 :class="itemModal.mode === 'view' ? 'bg-transparent text-slate-800 border border-slate-200 shadow-inner' : 'bg-slate-50 border-2 border-slate-200 text-[#1E55AA] focus:border-[#1E55AA] focus:bg-white'">
                        </div>
                        <div>
                            <label class="block text-sm font-extrabold text-[#1E55AA]/70 mb-2 ml-1">Unidad de Medida</label>
                            <select x-model="itemModal.unit" :disabled="itemModal.mode === 'view'"
                                class="w-full px-5 py-3 rounded-xl font-bold focus:outline-none transition-colors appearance-none cursor-pointer"
                                :class="itemModal.mode === 'view' ? 'bg-transparent text-slate-800 border border-slate-200 shadow-inner opacity-100' : 'bg-slate-50 border-2 border-slate-200 text-[#1E55AA] focus:border-[#1E55AA] focus:bg-white'">
                                <option value="H87" selected>H87 - Pieza</option>
                                <option value="E48">E48 - Unidad de servicio</option>
                                <option value="ACT">ACT - Actividad</option>
                                <option value="C62">C62 - Uno (Sin unidad específica)</option>
                                <option value="KGM">KGM - Kilogramo</option>
                                <option value="GRM">GRM - Gramo</option>
                                <option value="LTR">LTR - Litro</option>
                                <option value="MLT">MLT - Mililitro</option>
                                <option value="SET">SET - Conjunto / Juego</option>
                                <option value="XKI">XKI - Kit (Conjunto de Piezas)</option>
                                <option value="DPC">DPC - Docena de Piezas</option>
                                <option value="XBX">XBX - Caja</option>
                                <option value="TNE">TNE - Tonelada métrica</option>
                            </select>
                        </div>
                    </div>

                    {{-- Campo: Duración --}}
                    <div x-show="itemModal.category === 'subscriptions'" x-transition>
                        <label class="block text-sm font-extrabold text-[#1E55AA]/70 mb-2 ml-1">Duración (Meses)</label>
                        <input type="number" x-model="itemModal.duration_months" placeholder="1" :disabled="itemModal.mode === 'view'"
                            class="w-full px-5 py-3 rounded-xl font-bold focus:outline-none transition-colors"
                            :class="itemModal.mode === 'view' ? 'bg-transparent text-slate-800 border border-slate-200 shadow-inner' : 'bg-slate-50 border-2 border-slate-200 text-[#1E55AA] focus:border-[#1E55AA] focus:bg-white'">
                    </div>

                    {{-- Campo: Kilos por mes (Solo Suscripciones) --}}
                    <div x-show="itemModal.category === 'subscriptions'" x-collapse>
                         <label class="block text-sm font-extrabold text-[#1E55AA]/70 mb-2 ml-1">Kilos Gratis al Mes</label>
                            <input type="number" step="0.5" x-model="itemModal.kilos_per_month" placeholder="Ej. 20" :disabled="itemModal.mode === 'view'"
                                class="w-full px-5 py-3 rounded-xl font-bold focus:outline-none transition-colors"
                                :class="itemModal.mode === 'view' ? 'bg-transparent text-slate-800 border border-slate-200 shadow-inner' : 'bg-slate-50 border-2 border-slate-200 text-[#1E55AA] focus:border-[#1E55AA] focus:bg-white'">
                    </div>

                    {{-- Botones Inferiores dinámicos --}}
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="closeModal()" class="flex-1 py-3 rounded-xl font-black text-[#1E55AA]/60 bg-slate-100 hover:bg-slate-200 transition-all">
                            <span x-text="itemModal.mode === 'view' ? 'Cerrar' : 'Cancelar'"></span>
                        </button>
                        <button x-show="itemModal.mode !== 'view'" type="submit" class="flex-1 py-3 rounded-xl font-black text-white bg-emerald-500 shadow-lg shadow-emerald-500/20 hover:bg-emerald-600 transition-all"
                            x-text="itemModal.mode === 'add' ? 'Agregar' : 'Guardar'">
                        </button>
                    </div>
                </form>
            </template>

            {{-- 👇 AQUÍ ESTÁ LA SOLUCIÓN: El bloque de eliminación que faltaba 👇 --}}
            <template x-if="itemModal.mode === 'delete'">
                <div class="text-center p-4 space-y-4 font-nunito">
                    <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto shadow-inner border border-rose-100 animate-pulse">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800">¿Confirmar Eliminación?</h3>
                    <p class="text-slate-500 font-bold leading-relaxed">
                        ¿Estás completamente segura de que deseas borrar <span class="text-[#1E55AA] font-black" x-text="itemModal.name"></span>? Esta acción eliminará el registro permanentemente del catálogo.
                    </p>
                    
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="closeModal()" class="flex-1 py-3 rounded-xl font-black text-slate-500 bg-slate-100 hover:bg-slate-200 transition-all">
                            Cancelar
                        </button>
                        <button type="button" @click="deleteItem()" class="flex-1 py-3 rounded-xl font-black text-white bg-rose-500 hover:bg-rose-600 shadow-lg shadow-rose-500/20 transition-all">
                            Sí, Eliminar
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- Modal Pre-Confirmación --}}
<div x-show="showPreConfirmacion" 
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl border-2 border-slate-100 w-full max-w-lg overflow-hidden animate-fade-in" @click.stop>

        <div class="p-6 border-b border-slate-100 bg-[#F4F8FC]">
            <h3 class="text-2xl font-black text-[#1E55AA]">Completar Venta</h3>
            <p class="text-[#1E55AA]/60 font-bold mt-1">Registra al cliente y selecciona el método de pago</p>
            <p x-show="cart.some(item => item.category === 'subscriptions')" class="text-[#1E55AA]/60 font-bold mt-1">Registra al cliente y su vigencia (Opcional)</p>
        </div>

        <div class="p-6 space-y-4">

            {{-- BLOQUE DE SUSCRIPCIÓN Y CLIENTE --}}
            <div x-show="cart.some(item => item.category === 'subscriptions')" x-collapse class="space-y-4">

                {{-- Selector de Tipo de Registro --}}
                <div class="bg-slate-50 p-4 rounded-2xl border-2 border-slate-100">
                    <label class="block text-sm font-black text-[#1E55AA] mb-2">¿Para quién es la suscripción?</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" x-model="clienteForm.tipoRegistro" value="nuevo" class="w-5 h-5 text-[#1E55AA] focus:ring-[#1E55AA]">
                            <span class="font-bold text-slate-700">Crear Nuevo Cliente</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" x-model="clienteForm.tipoRegistro" value="existente" class="w-5 h-5 text-[#1E55AA] focus:ring-[#1E55AA]">
                            <span class="font-bold text-slate-700">Cliente Existente</span>
                        </label>
                    </div>
                </div>

                {{-- Campos para Cliente Existente (Con Datalist) --}}
                <div x-show="clienteForm.tipoRegistro === 'existente'" x-collapse>
                    <label class="block text-sm font-black text-[#1E55AA] mb-1">Buscar Cliente</label>

                    {{-- Input Visible --}}
                    <input type="text"
                        list="clientes-datalist"
                        x-model="clienteForm.cliente_texto"
                        @input="vincularClienteId()"
                        placeholder="Escribe el nombre o teléfono..."
                        class="w-full rounded-xl border-2 border-slate-100 bg-white py-2.5 px-3 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] transition-all">

                    {{-- Lista de Datos Nativa --}}
                    <datalist id="clientes-datalist">
                        <template x-for="client in clients" :key="client.id">
                            <option :value="client.name + ' (' + (client.phone || 'Sin tel') + ')'"></option>
                        </template>
                    </datalist>

                    {{-- Feedback visual para el cajero --}}
                    <div class="mt-2 text-[11px] font-black uppercase tracking-wider transition-colors"
                         x-show="clienteForm.cliente_texto !== ''"
                         :class="clienteForm.cliente_id ? 'text-emerald-500' : 'text-rose-400'">
                         <span x-text="clienteForm.cliente_id ? '✓ Cliente vinculado (ID: ' + clienteForm.cliente_id + ')' : '⚠️ Debes seleccionar un cliente válido de la lista'"></span>
                    </div>
                </div>

                {{-- Campos para Nuevo Cliente --}}
                <div x-show="clienteForm.tipoRegistro === 'nuevo'" x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-black text-[#1E55AA] mb-1">Nombre Completo <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="clienteForm.nombre" placeholder="Ej. Juan Pérez" class="w-full rounded-xl border-2 border-slate-100 bg-white py-2.5 px-3 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] focus:ring-2 focus:ring-[#1E55AA]/10 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-black text-[#1E55AA] mb-1">Teléfono</label>
                        <input type="text" x-model="clienteForm.telefono" placeholder="Opcional" class="w-full rounded-xl border-2 border-slate-100 bg-white py-2.5 px-3 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] focus:ring-2 focus:ring-[#1E55AA]/10 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-black text-[#1E55AA] mb-1">Correo Electrónico</label>
                        <input type="email" x-model="clienteForm.email" placeholder="Opcional" class="w-full rounded-xl border-2 border-slate-100 bg-white py-2.5 px-3 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] focus:ring-2 focus:ring-[#1E55AA]/10 transition-all">
                    </div>
                </div>

                {{-- Fecha de Inicio (Aplica para ambos casos) --}}
                <div class="bg-[#F4F8FC] p-4 rounded-xl border border-[#1E55AA]/10 mt-2">
                    <label class="block text-sm font-black text-[#1E55AA] mb-1">Inicio de Plan <span class="text-rose-500">*</span></label>
                    <input type="date" x-model="clienteForm.inicio" class="w-full rounded-xl border-2 border-slate-100 bg-white py-2 px-3 font-bold text-[#1E55AA] outline-none focus:border-[#1E55AA] focus:ring-2 focus:ring-[#1E55AA]/10 transition-all">
                    <p class="text-[11px] font-bold text-slate-400 mt-1">El fin del plan se calculará automáticamente.</p>
                </div>
            </div>

            <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-100">
                <div class="text-[#1E55AA]/60 font-bold">Total a cobrar:</div>
                <div class="text-3xl font-black text-emerald-500" x-text="formatMoney(total)"></div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-100 flex flex-col gap-3">
            <div class="flex gap-3">
                <button @click="confirmarCheckout('Efectivo')" class="flex-1 py-3.5 rounded-xl font-black text-[#1E55AA] bg-[#FFE63C] shadow-lg shadow-[#FFE63C]/20 hover:bg-[#f5dd38] hover:-translate-y-0.5 transition-all">
                    Efectivo
                </button>
                <button @click="confirmarCheckout('Terminal')" class="flex-1 py-3.5 rounded-xl font-black text-white bg-[#1E55AA] shadow-lg shadow-[#1E55AA]/20 hover:bg-[#153e7d] hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Terminal MP
                </button>
            </div>
            <button @click="cancelarCheckout()" class="w-full py-2.5 rounded-xl font-bold text-slate-400 hover:text-slate-600 transition-colors">
                Cancelar Venta
            </button>
        </div>
    </div>
</div>

{{-- Modal de Carga (Esperando Terminal) --}}
<div x-show="esperandoTerminal" 
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-8 relative" @click.stop>
        
        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-blue-50 mb-6 relative">
            <div class="absolute inset-0 rounded-full border-4 border-blue-200 animate-ping opacity-50"></div>
            <svg class="h-12 w-12 text-blue-600 animate-pulse relative z-10" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
            </svg>
        </div>

        <h3 class="text-2xl font-black text-[#1E55AA] mb-3">Procesando Pago...</h3>
        <p class="text-gray-500 font-bold leading-relaxed mb-8">Por favor, siga las instrucciones en la terminal Mercado Pago.</p>
        
        <div class="flex justify-center items-center space-x-3 mb-4">
            <div class="h-3.5 w-3.5 rounded-full bg-yellow-400 animate-bounce"></div>
            <div class="h-3.5 w-3.5 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 0.15s"></div>
            <div class="h-3.5 w-3.5 rounded-full bg-green-500 animate-bounce" style="animation-delay: 0.3s"></div>
        </div>
    </div>
</div>

{{-- Modal de Error de Pago --}}
<div x-show="showErrorModal" 
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl border-2 border-rose-100 w-full max-w-sm overflow-hidden text-center p-8 animate-fade-in" @click.stop>
        
        <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-rose-100">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
        
        <h3 class="text-2xl font-black text-[#1E55AA] mb-3">Pago No Realizado</h3>
        <p class="text-slate-500 font-bold mb-8 leading-relaxed" x-text="errorPago"></p>
        
        <button @click="cerrarErrorModal()" class="w-full py-4 rounded-xl font-black text-white bg-rose-500 hover:bg-rose-600 shadow-lg shadow-rose-500/20 transition-all active:scale-95">
            Entendido, regresar
        </button>
    </div>
</div>

{{-- Modal Éxito --}}
<div x-show="showConfirmacion" 
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl border-2 border-slate-100 w-full max-w-sm overflow-hidden text-center p-8 animate-fade-in" @click.stop>
        <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h3 class="text-2xl font-black text-[#1E55AA] mb-2">¡Venta Exitosa!</h3>
        <p class="text-slate-500 font-bold mb-6">El pago se registró correctamente en la caja.</p>
        <button @click="cerrarConfirmacion()" class="w-full py-3.5 rounded-xl font-black text-[#1E55AA] bg-[#FFE63C] hover:bg-[#f5dd38] transition-all">
            Nueva Venta
        </button>
    </div> 
</div>