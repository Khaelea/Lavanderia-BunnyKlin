@extends('layouts.app') 

@section('content')
<div class="w-full max-w-5xl mx-auto pb-10">
    
    <!-- Encabezado -->
    <div class="flex items-center gap-3 md:gap-4 mb-6 md:mb-8">
        <div class="w-10 h-10 md:w-12 md:h-12 bg-[#1E55AA]/10 text-[#1E55AA] rounded-xl flex items-center justify-center shadow-sm shrink-0">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-[#1E55AA] tracking-wide">Gestión de Personal</h1>
    </div>

    <!-- Alertas informativas -->
    @if (session('success'))
        <div id="alert-success" class="mb-6 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-3 md:p-4 rounded-xl shadow-sm font-bold flex items-center gap-2 w-full transition-opacity duration-500 text-sm md:text-base">
            <svg class="w-5 h-5 md:w-6 md:h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div id="alert-error" class="mb-6 bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-3 md:p-4 rounded-xl shadow-sm font-bold flex items-center gap-2 w-full transition-opacity duration-500 text-sm md:text-base">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulario de Registro -->
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-8 w-full mb-6 md:mb-8">
        <h2 class="text-lg md:text-xl font-bold text-[#1E55AA] mb-5 md:mb-6">Registrar Nuevo Empleado</h2>

        <form action="{{ route('personal.store') }}" method="POST">
            @csrf 
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-5">
                <div>
                    <label class="block text-sm font-extrabold text-[#1E55AA] mb-1.5">Nombre Completo</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Juan Pérez" class="w-full bg-[#f8fafc] border border-gray-200 text-[#1E55AA] font-semibold placeholder-gray-400 rounded-xl md:rounded-2xl px-4 py-2.5 focus:outline-none focus:border-[#1E55AA] transition-all shadow-sm text-sm md:text-base">
                </div>
                <div>
                    <label class="block text-sm font-extrabold text-[#1E55AA] mb-1.5">Email de Acceso</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="correo@ejemplo.com" class="w-full bg-[#f8fafc] border border-gray-200 text-[#1E55AA] font-semibold placeholder-gray-400 rounded-xl md:rounded-2xl px-4 py-2.5 focus:outline-none focus:border-[#1E55AA] transition-all shadow-sm text-sm md:text-base">
                </div>
                <div>
                    <label class="block text-sm font-extrabold text-[#1E55AA] mb-1.5">Contraseña</label>
                    <input type="text" name="password" required placeholder="Ej. BunnyKlin2026" class="w-full bg-[#f8fafc] border border-gray-200 text-[#1E55AA] font-semibold placeholder-gray-400 rounded-xl md:rounded-2xl px-4 py-2.5 focus:outline-none focus:border-[#1E55AA] transition-all shadow-sm text-sm md:text-base">
                </div>
                <div>
                    <label class="block text-sm font-extrabold text-[#1E55AA] mb-1.5">Rol / Puesto</label>
                    <div class="relative">
                        <select name="rol" required class="w-full bg-[#f8fafc] border border-gray-200 text-[#1E55AA] font-semibold rounded-xl md:rounded-2xl px-4 py-2.5 focus:outline-none focus:border-[#1E55AA] appearance-none cursor-pointer transition-all shadow-sm text-sm md:text-base">
                            <option value="Cajero">Cajero</option>
                            <option value="Admin">Administrador</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#1E55AA]">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botón de guardar ajustado y alineado a la derecha -->
            <div class="flex justify-end">
                <button type="submit" class="bg-[#1E55AA] hover:bg-[#153e7f] text-white font-extrabold py-2.5 px-6 rounded-xl shadow-md transition-all active:scale-95 text-sm md:text-base flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Guardar Empleado
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Personal Registrado -->
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 md:p-8 w-full">
        <h2 class="text-lg md:text-xl font-bold text-[#1E55AA] mb-4 md:mb-6">Personal Registrado</h2>

        <div class="overflow-x-auto w-full pb-2">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="border-b-2 border-gray-100 text-[#1E55AA]">
                        <th class="py-3 px-3 font-extrabold uppercase text-[10px] md:text-xs whitespace-nowrap">Nombre</th>
                        <th class="py-3 px-3 font-extrabold uppercase text-[10px] md:text-xs whitespace-nowrap">Email</th>
                        <th class="py-3 px-3 font-extrabold uppercase text-[10px] md:text-xs whitespace-nowrap">Rol</th>
                        <th class="py-3 px-3 font-extrabold uppercase text-[10px] md:text-xs whitespace-nowrap">Estado</th>
                        <th class="py-3 px-3 font-extrabold uppercase text-[10px] md:text-xs text-center whitespace-nowrap">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 font-medium text-sm md:text-base">
                    @forelse($empleados as $empleado)
                        <tr class="border-b border-gray-50 hover:bg-[#f8fafc] transition-colors">
                            <td class="py-3 px-3 whitespace-nowrap">{{ $empleado->name }}</td>
                            <td class="py-3 px-3 whitespace-nowrap">{{ $empleado->email }}</td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                <span class="bg-[#1E55AA]/10 text-[#1E55AA] px-2.5 py-1 rounded-full text-[10px] md:text-xs font-extrabold uppercase">
                                    {{ $empleado->role }}
                                </span>
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                @if($empleado->status === 'activo')
                                    <span class="text-emerald-500 font-extrabold flex items-center gap-1.5 text-xs md:text-sm">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div> Activo
                                    </span>
                                @else
                                    <span class="text-amber-500 font-extrabold flex items-center gap-1.5 text-xs md:text-sm">
                                        <div class="w-2 h-2 rounded-full bg-amber-500"></div> Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <form action="{{ route('personal.eliminar_id', $empleado->id) }}" method="POST" class="form-eliminar" data-nombre="{{ $empleado->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="group flex items-center gap-1.5 bg-rose-50 border border-rose-100 text-rose-600 hover:bg-[#ff2a5f] hover:text-white font-bold text-[10px] md:text-xs px-2.5 py-1.5 rounded-lg shadow-sm transition-all active:scale-95">
                                            <svg class="w-3.5 h-3.5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-400 font-bold text-sm md:text-base">Aún no hay empleados registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- CSS Para forzar el diseño de la alerta modal de SweetAlert2 -->
<style>
    .alerta-eliminar {
        padding: 0 !important;
        border-radius: 1.5rem !important;
        border: 1px solid #f3f4f6 !important;
        overflow: hidden !important;
    }
    .alerta-eliminar .swal2-html-container {
        margin: 0 !important;
        padding: 0 !important;
    }
    .alerta-eliminar .swal2-actions {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 1rem !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 1.5rem 1.5rem 1.5rem !important;
        box-sizing: border-box !important;
    }
    @media (min-width: 640px) {
        .alerta-eliminar .swal2-actions {
            padding: 0 2rem 2rem 2rem !important;
        }
    }
    .btn-cancelar {
        width: 100% !important;
        margin: 0 !important;
        background-color: #f4f7fa !important;
        color: #7193b2 !important;
        border-radius: 1rem !important;
        font-weight: 800 !important;
        font-size: 0.875rem !important;
        padding: 0.75rem 0 !important;
        transition: all 0.2s !important;
    }
    .btn-cancelar:hover {
        background-color: #e2e8f0 !important;
    }
    .btn-eliminar {
        width: 100% !important;
        margin: 0 !important;
        background-color: #ff2a5f !important;
        color: white !important;
        border-radius: 1rem !important;
        font-weight: 800 !important;
        font-size: 0.875rem !important;
        padding: 0.75rem 0 !important;
        transition: all 0.2s !important;
    }
    .btn-eliminar:hover {
        background-color: #e62555 !important;
    }
    @media (min-width: 640px) {
        .btn-cancelar, .btn-eliminar {
            font-size: 1rem !important;
            padding: 0.875rem 0 !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        function fadeOutAlert(id) {
            let alertElement = document.getElementById(id);
            if (alertElement) {
                setTimeout(function() {
                    alertElement.style.opacity = '0'; 
                    setTimeout(function() {
                        alertElement.remove(); 
                    }, 500); 
                }, 5000); 
            }
        }
        fadeOutAlert('alert-success');
        fadeOutAlert('alert-error');

        const formsEliminar = document.querySelectorAll('.form-eliminar');
        
        formsEliminar.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); 
                
                let nombreEmpleado = this.getAttribute('data-nombre');

                Swal.fire({
                    width: window.innerWidth < 640 ? '90%' : '420px', 
                    html: `
                        <div style="background-color: #f8fafc; text-align: left; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6;">
                            <h2 style="font-size: 1.125rem; font-weight: 900; color: #1E55AA; margin: 0; line-height: 1.2;">Eliminar Elemento</h2>
                        </div>
                        <div style="padding: 1.5rem;">
                            <p style="color: #4b5563; font-weight: 700; font-size: 1rem; margin: 0; text-align: center; line-height: 1.5;">
                                ¿Seguro que deseas eliminar <span style="color: #1E55AA;">${nombreEmpleado}</span>?
                            </p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true, 
                    buttonsStyling: false, 
                    customClass: {
                        popup: 'alerta-eliminar shadow-xl',
                        cancelButton: 'btn-cancelar',
                        confirmButton: 'btn-eliminar'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); 
                    }
                })
            });
        });

    });
</script>
@endsection