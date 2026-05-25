<x-app-layout>
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-black dark:text-white">
                    Catálogo de Servicios
                </h2>
                <p class="text-sm text-gray-500">Administra los precios y tiempos de tus lavadoras.</p>
            </div>

            <button class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 transition-all">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Servicio
            </button>
        </div>

        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="min-w-[200px] py-4 px-4 font-medium text-black dark:text-white">Nombre del Servicio</th>
                            <th class="min-w-[120px] py-4 px-4 font-medium text-black dark:text-white">Precio Público</th>
                            <th class="min-w-[120px] py-4 px-4 font-medium text-black dark:text-white">Duración</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white">Estado</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-[#eee] dark:border-strokedark">
                            <td class="py-5 px-4">
                                <p class="text-black dark:text-white font-medium">Lavado Express (10kg)</p>
                                <p class="text-xs text-gray-500">Agua Fría, Ciclo Rápido</p>
                            </td>
                            <td class="py-5 px-4">
                                <p class="text-success font-bold text-lg">$60.00</p>
                            </td>
                            <td class="py-5 px-4">
                                <p class="text-black dark:text-white">35 min</p>
                            </td>
                            <td class="py-5 px-4">
                                <span class="inline-flex rounded-full bg-success bg-opacity-10 py-1 px-3 text-xs font-medium text-success">
                                    Activo
                                </span>
                            </td>
                            <td class="py-5 px-4 text-right">
                                <button class="hover:text-primary mr-3 text-lg transition-colors"><i class="far fa-edit"></i></button>
                                <button class="hover:text-danger text-lg transition-colors"><i class="far fa-trash-alt"></i></button>
                            </td>
                        </tr>

                        <tr class="border-b border-[#eee] dark:border-strokedark">
                            <td class="py-5 px-4">
                                <p class="text-black dark:text-white font-medium">Secado Estándar</p>
                                <p class="text-xs text-gray-500">Temperatura Media</p>
                            </td>
                            <td class="py-5 px-4">
                                <p class="text-success font-bold text-lg">$45.00</p>
                            </td>
                            <td class="py-5 px-4">
                                <p class="text-black dark:text-white">45 min</p>
                            </td>
                            <td class="py-5 px-4">
                                <span class="inline-flex rounded-full bg-success bg-opacity-10 py-1 px-3 text-xs font-medium text-success">
                                    Activo
                                </span>
                            </td>
                            <td class="py-5 px-4 text-right">
                                <button class="hover:text-primary mr-3 text-lg transition-colors"><i class="far fa-edit"></i></button>
                                <button class="hover:text-danger text-lg transition-colors"><i class="far fa-trash-alt"></i></button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
