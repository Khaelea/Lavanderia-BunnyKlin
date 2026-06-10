@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 p-6 font-sans">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-2xl font-bold text-blue-900">Máquinas IoT</h1>
            <button
                onclick="toggleForm()"
                class="flex items-center gap-1.5 bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                </svg>
                Agregar equipo
            </button>
        </div>

        <div class="flex flex-wrap gap-4 text-sm font-medium text-gray-700 bg-white p-3 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span>Disponible (<span id="cnt-avail">0</span>)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span>En Uso (<span id="cnt-use">0</span>)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                <span>Fuera de Servicio (<span id="cnt-oos">0</span>)</span>
            </div>
        </div>
    </div>

    {{-- FORMULARIO AGREGAR --}}
    <div id="form-panel" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Nuevo equipo</h2>

        {{-- Fila 1: ID, Tipo, Estado --}}
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex flex-col gap-1 flex-1 min-w-[130px]">
                <label for="f-id" class="text-xs font-medium text-gray-500">ID del equipo</label>
                <input
                    id="f-id" type="text" maxlength="8" placeholder="Ej. L-07, S-05"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"/>
            </div>
            <div class="flex flex-col gap-1 flex-1 min-w-[130px]">
                <label for="f-type" class="text-xs font-medium text-gray-500">Tipo</label>
                <select id="f-type" class="border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition bg-white">
                    <option value="Lavadora">Lavadora</option>
                    <option value="Secadora">Secadora</option>
                </select>
            </div>
            <div class="flex flex-col gap-1 flex-1 min-w-[130px]">
                <label for="f-status" class="text-xs font-medium text-gray-500">Estado inicial</label>
                <select id="f-status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition bg-white">
                    <option value="available">Disponible</option>
                    <option value="oos">Fuera de Servicio</option>
                </select>
            </div>
        </div>

        {{-- Fila 2: Modelo, Tipo de lavado --}}
        <div class="flex flex-wrap gap-4 items-end mt-4">
            <div class="flex flex-col gap-1 flex-1 min-w-[180px]">
                <label for="f-model" class="text-xs font-medium text-gray-500">Modelo</label>
                <input
                    id="f-model" type="text" maxlength="40" placeholder="Ej. Samsung WW90T, LG F4WV510S"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"/>
            </div>
            <div class="flex flex-col gap-1 flex-1 min-w-[160px]">
                <label for="f-wash" class="text-xs font-medium text-gray-500">Tipo de lavado</label>
                <select id="f-wash" class="border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition bg-white">
                    <option value="">— Sin asignar —</option>
                    <option value="Normal">Normal</option>
                    <option value="Delicado">Delicado</option>
                    <option value="Rápido">Rápido</option>
                    <option value="Pesado">Pesado</option>
                    <option value="Centrifugado">Centrifugado</option>
                </select>
            </div>
        </div>

        <p id="form-err" class="hidden text-xs text-rose-500 font-medium mt-2"></p>

        <div class="flex gap-2 mt-4">
            <button onclick="addMachine()" class="flex items-center gap-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar
            </button>
            <button onclick="toggleForm()" class="text-sm font-medium text-gray-500 hover:text-gray-700 border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-xl transition-colors">
                Cancelar
            </button>
        </div>
    </div>

    {{-- Lavadoras --}}
    <div class="mb-8">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-4">Lavadoras</h2>
        <div id="grid-lavadoras" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5"></div>
    </div>

    {{-- Secadoras --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-widest mb-4">Secadoras</h2>
        <div id="grid-secadoras" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5"></div>
    </div>

    {{-- MODAL CONFIRMAR ELIMINAR --}}
    <div id="modal-overlay" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-sm mx-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-rose-50 text-rose-500 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Eliminar equipo</h3>
            </div>
            <p class="text-sm text-gray-500 mb-5">
                ¿Estás seguro que deseas eliminar la máquina <span id="modal-machine-id" class="font-semibold text-gray-800"></span>? Esta acción no se puede deshacer.
            </p>
            <div class="flex gap-2 justify-end">
                <button onclick="closeModal()"
                    class="text-sm font-medium text-gray-500 hover:text-gray-700 border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-xl transition-colors">
                    Cancelar
                </button>
                <button id="modal-confirm-btn" onclick="confirmDelete()"
                    class="flex items-center gap-1.5 bg-rose-500 hover:bg-rose-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>

</div>

<script>
const machines = [
    { id: 'L-01', type: 'Lavadora', status: 'inuse',     remaining: 15, total: 60, pct: 75, model: 'Samsung WW90T',   wash: 'Normal'       },
    { id: 'L-02', type: 'Lavadora', status: 'available',                                     model: 'LG F4WV510S',    wash: 'Delicado'     },
    { id: 'L-03', type: 'Lavadora', status: 'oos',                                           model: 'Whirlpool FWF',  wash: ''             },
    { id: 'L-04', type: 'Lavadora', status: 'inuse',     remaining: 5,  total: 50, pct: 90, model: 'Bosch WAN28281', wash: 'Rápido'       },
    { id: 'L-05', type: 'Lavadora', status: 'available',                                     model: 'Samsung WW90T',  wash: 'Pesado'       },
    { id: 'L-06', type: 'Lavadora', status: 'available',                                     model: '',               wash: ''             },
    { id: 'S-01', type: 'Secadora', status: 'available',                                     model: 'Bosch WTH85222', wash: ''             },
    { id: 'S-02', type: 'Secadora', status: 'inuse',     remaining: 30, total: 55, pct: 45, model: 'LG RC9055AP2F',  wash: 'Centrifugado' },
    { id: 'S-03', type: 'Secadora', status: 'available',                                     model: '',               wash: ''             },
    { id: 'S-04', type: 'Secadora', status: 'inuse',     remaining: 20, total: 50, pct: 60, model: 'Miele TWI180WP', wash: 'Normal'       },
];

let newFlags = new Set();

const laundryIcon = `<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M3 18h18M3 6h18"/></svg>`;
const dryerIcon   = `<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M8 5v14M16 5v14"/></svg>`;
const clockIcon   = `<svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
const warnIcon    = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
const trashIcon   = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`;

// Colores de badge por tipo de lavado
const washBadge = {
    'Normal':       'bg-gray-100 text-gray-600',
    'Delicado':     'bg-sky-50 text-sky-600',
    'Rápido':       'bg-amber-50 text-amber-600',
    'Pesado':       'bg-violet-50 text-violet-600',
    'Centrifugado': 'bg-teal-50 text-teal-600',
};

const cfg = {
    available: { border: 'border-emerald-100', iconBg: 'bg-emerald-50 text-emerald-500', badge: 'text-emerald-600 bg-emerald-50', label: 'Disponible'        },
    inuse:     { border: 'border-blue-100',    iconBg: 'bg-blue-50 text-blue-500',       badge: 'text-blue-600 bg-blue-50',     label: 'En Uso'             },
    oos:       { border: 'border-rose-100',    iconBg: 'bg-rose-50 text-rose-500',       badge: 'text-rose-600 bg-rose-50',     label: 'Fuera de Servicio'  },
};

function toggleForm() {
    const panel = document.getElementById('form-panel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) document.getElementById('f-id').focus();
    document.getElementById('form-err').classList.add('hidden');
}

function addMachine() {
    const rawId  = document.getElementById('f-id').value.trim().toUpperCase();
    const type   = document.getElementById('f-type').value;
    const status = document.getElementById('f-status').value;
    const model  = document.getElementById('f-model').value.trim();
    const wash   = document.getElementById('f-wash').value;
    const errEl  = document.getElementById('form-err');

    if (!rawId) {
        errEl.textContent = 'El ID es obligatorio.';
        errEl.classList.remove('hidden');
        return;
    }
    if (machines.find(m => m.id === rawId)) {
        errEl.textContent = 'Ya existe un equipo con ese ID.';
        errEl.classList.remove('hidden');
        return;
    }

    errEl.classList.add('hidden');
    machines.push({ id: rawId, type, status, model, wash });
    newFlags.add(rawId);
    document.getElementById('f-id').value    = '';
    document.getElementById('f-model').value = '';
    document.getElementById('f-wash').value  = '';
    document.getElementById('form-panel').classList.add('hidden');
    render();
    setTimeout(() => newFlags.delete(rawId), 600);
}

function startCycle(i) {
    machines[i].status    = 'inuse';
    machines[i].remaining = 30;
    machines[i].total     = 30;
    machines[i].pct       = 0;
    render();
}

function releaseCycle(i) {
    machines[i].status = 'available';
    delete machines[i].remaining;
    delete machines[i].total;
    delete machines[i].pct;
    render();
}

let pendingDeleteIndex = null;

function deleteMachine(i) {
    pendingDeleteIndex = i;
    document.getElementById('modal-machine-id').textContent = machines[i].id;
    document.getElementById('modal-overlay').classList.remove('hidden');
}

function closeModal() {
    pendingDeleteIndex = null;
    document.getElementById('modal-overlay').classList.add('hidden');
}

function confirmDelete() {
    if (pendingDeleteIndex === null) return;
    machines.splice(pendingDeleteIndex, 1);
    pendingDeleteIndex = null;
    document.getElementById('modal-overlay').classList.add('hidden');
    render();
}

function render() {
    const gridLav = document.getElementById('grid-lavadoras');
    const gridSec = document.getElementById('grid-secadoras');
    gridLav.innerHTML = '';
    gridSec.innerHTML = '';
    let avail = 0, inuse = 0, oos = 0;

    machines.forEach((m, i) => {
        if      (m.status === 'available') avail++;
        else if (m.status === 'inuse')     inuse++;
        else                               oos++;

        const c    = cfg[m.status];
        const icon = m.type === 'Lavadora' ? laundryIcon : dryerIcon;

        // Badge de tipo de lavado
        const washHtml = m.wash
            ? `<span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full ${washBadge[m.wash] || 'bg-gray-100 text-gray-500'}">${m.wash}</span>`
            : '';

        // Modelo
        const modelHtml = m.model
            ? `<p class="text-xs text-gray-400 font-medium mt-0.5 truncate">${m.model}</p>`
            : '';

        // Bloque inferior
        let bottom = '';

        if (m.status === 'available') {
            bottom = `
                <div class="mt-4 flex flex-col gap-2">
                    <button onclick="startCycle(${i})"
                        class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-medium text-sm py-2 px-4 rounded-xl transition-colors">
                        Iniciar Ciclo
                    </button>
                    <button onclick="deleteMachine(${i})"
                        class="w-full flex items-center justify-center gap-1.5 text-xs font-semibold text-rose-400 hover:text-rose-600 border border-rose-100 hover:border-rose-300 hover:bg-rose-50 py-1.5 rounded-xl transition-colors">
                        ${trashIcon} Eliminar
                    </button>
                </div>`;

        } else if (m.status === 'inuse') {
            bottom = `
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between text-xs font-semibold text-gray-600">
                        <span class="flex items-center gap-1">
                            ${clockIcon}
                            Resta: <span id="rem-${i}">${m.remaining}</span> min
                        </span>
                        <span class="text-blue-600" id="pct-${i}">${m.pct}%</span>
                    </div>
                    <div class="w-full bg-blue-100 h-2 rounded-full overflow-hidden">
                        <div id="bar-${i}" class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width:${m.pct}%"></div>
                    </div>
                    <button onclick="releaseCycle(${i})"
                        class="w-full text-xs font-semibold text-blue-500 hover:text-blue-700 border border-blue-100 hover:border-blue-300 hover:bg-blue-50 py-1.5 rounded-xl transition-colors">
                        Liberar máquina
                    </button>
                </div>`;

        } else {
            bottom = `
                <div class="mt-4 flex flex-col gap-2">
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-rose-500">
                        ${warnIcon}
                        <span>Requiere mantenimiento</span>
                    </div>
                    <button onclick="deleteMachine(${i})"
                        class="w-full flex items-center justify-center gap-1.5 text-xs font-semibold text-rose-400 hover:text-rose-600 border border-rose-100 hover:border-rose-300 hover:bg-rose-50 py-1.5 rounded-xl transition-colors">
                        ${trashIcon} Eliminar
                    </button>
                </div>`;
        }

        // ---- Tarjeta completa ----
        const cardHtml = `
            <div class="bg-white rounded-2xl p-5 shadow-sm border-2 ${c.border} flex flex-col justify-between min-h-[220px]">
                <div class="flex justify-between items-start">
                    <div class="p-2.5 ${c.iconBg} rounded-xl">${icon}</div>
                    <span class="px-2.5 py-1 text-xs font-semibold ${c.badge} rounded-full">${c.label}</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-xl font-bold text-gray-900">${m.id}</h3>
                    <p class="text-xs text-gray-500 font-medium">${m.type}</p>
                    ${modelHtml}
                    ${washHtml}
                </div>
                ${bottom}
            </div>`;

        // ---- Decide a qué grid va (DENTRO del forEach) ----
        if (m.type === 'Lavadora') gridLav.innerHTML += cardHtml;
        else                       gridSec.innerHTML += cardHtml;
    });

    document.getElementById('cnt-avail').textContent = avail;
    document.getElementById('cnt-use').textContent   = inuse;
    document.getElementById('cnt-oos').textContent   = oos;
}

render();

setInterval(() => {
    let changed = false;
    machines.forEach((m, i) => {
        if (m.status === 'inuse' && m.remaining > 0) {
            m.remaining = Math.max(0, m.remaining - 1);
            m.pct       = Math.min(100, Math.round(((m.total - m.remaining) / m.total) * 100));
            const r = document.getElementById(`rem-${i}`);
            const p = document.getElementById(`pct-${i}`);
            const b = document.getElementById(`bar-${i}`);
            if (r) r.textContent = m.remaining;
            if (p) p.textContent = m.pct + '%';
            if (b) b.style.width = m.pct + '%';
            if (m.remaining === 0) {
                m.status = 'available';
                delete m.remaining; delete m.total; delete m.pct;
                changed = true;
            }
        }
    });
    if (changed) render();
}, 60000);
</script>
@endsection