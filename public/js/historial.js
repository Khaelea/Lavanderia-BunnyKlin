document.addEventListener("alpine:init", () => {
    Alpine.data("historialSystem", () => ({
        ventas: [],
        ventasFiltradas: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            links: [],
        },
        tipoFiltro: "todas",
        valorFiltro: "",
        mesesDisponibles: [],
        diasDisponibles: [],
        totalFiltro: 0,
        confirmModal: { open: false, title: "", message: "", onConfirm: null },
        ticketActivo: null,
        ticketAImprimir: null,

        init() {
            this.cargarDatos();

            // Si el usuario cambia el menú (ej. de "dia" a "todas"), reseteamos la fecha
            this.$watch("tipoFiltro", (value) => {
                this.valorFiltro = "";
                if (value === "todas") {
                    this.cargarDatos(); // Recargamos la tabla sin filtros
                }
            });

            // Si el usuario elige una fecha en el calendario, pedimos los datos a Laravel
            this.$watch("valorFiltro", (value) => {
                if (value !== "") {
                    this.cargarDatos();
                }
            });

            this.$watch("tipoFiltro", (value) => {
                this.valorFiltro = "";
                this.cargarDatos();
            });

            this.$watch("valorFiltro", (value) => {
                if (this.tipoFiltro === "folio") {
                    this.cargarDatos();
                }
            });

            // Inicializar el selector de Mes
            flatpickr(this.$refs.filtroMes, {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "Y-m", // Formato que tu controlador necesita
                    }),
                ],
                onChange: (selectedDates, dateStr) => {
                    this.valorFiltro = dateStr; // Actualiza Alpine
                },
            });

            // Inicializar el selector de Día
            flatpickr(this.$refs.filtroDia, {
                dateFormat: "Y-m-d", // Formato que tu controlador necesita
                onChange: (selectedDates, dateStr) => {
                    this.valorFiltro = dateStr; // Actualiza Alpine
                },
            });
        },

        // Adaptado para leer la relación 'items' de Laravel o tus respaldos locales
        getDetalles(venta) {
            console.log(
                venta?.items || venta?.detalles || venta?.productos || [],
            );
            return venta?.items || venta?.detalles || venta?.productos || [];
        },

        async cargarDatos(url = "/ventas/api-historial") {
            try {
                // Preparamos la URL
                const urlObj = new URL(url, window.location.origin);

                // Si es una nueva búsqueda (no viene de darle clic a "Página 2"), inyectamos los filtros actuales
                if (
                    !urlObj.searchParams.has("page") &&
                    this.tipoFiltro !== "todas" &&
                    this.valorFiltro
                ) {
                    urlObj.searchParams.append("tipo", this.tipoFiltro);
                    urlObj.searchParams.append("fecha", this.valorFiltro);
                }

                const response = await fetch(urlObj);
                if (!response.ok) throw new Error("Error HTTP de Laravel");

                const dataApi = await response.json();

                // Extraemos la paginación y el total real de la nueva estructura
                const paginator = dataApi.paginacion;
                this.totalFiltro = dataApi.total_filtro; // <--- AQUÍ ESTÁ LA SUMA REAL

                // Actualizamos los controles de paginación
                this.pagination.current_page = paginator.current_page;
                this.pagination.last_page = paginator.last_page;
                this.pagination.links = paginator.links;

                // Las ventas ya nos llegan filtradas y listas desde Laravel
                this.ventasFiltradas = paginator.data.map((venta) => {
                    const fechaObj = new Date(venta.created_at);
                    const dia = String(fechaObj.getDate()).padStart(2, "0");
                    const mes = String(fechaObj.getMonth() + 1).padStart(
                        2,
                        "0",
                    );
                    const anio = fechaObj.getFullYear();
                    const hora = String(fechaObj.getHours()).padStart(2, "0");
                    const minutos = String(fechaObj.getMinutes()).padStart(
                        2,
                        "0",
                    );

                    return {
                        id: venta.id,
                        folio: venta.reference,
                        fecha: `${dia}/${mes}/${anio}, ${hora}:${minutos}`,
                        total: parseFloat(venta.total),
                        items: venta.items || [],

                        // 🔥 AQUÍ ESTÁ LA LÍNEA MÁGICA: Mapeamos el nombre del vendedor
                        nombre_vendedor: venta.nombre_vendedor || "Desconocido",
                    };
                });
            } catch (error) {
                console.error("🚨 Error cargando el historial:", error);
                this.ventasFiltradas = [];
            }
        },

        verTicket(venta) {
            this.ticketActivo = venta;
        },

        imprimirDirecto(venta) {
            this.ticketAImprimir = venta;
            setTimeout(() => window.print(), 150);
        },

        abrirConfirmacion(titulo, mensaje, accion) {
            Object.assign(this.confirmModal, {
                open: true,
                title: titulo,
                message: mensaje,
                onConfirm: accion,
            });
        },

        cerrarConfirmacion() {
            this.confirmModal.open = false;
            setTimeout(() => {
                this.confirmModal.onConfirm = null;
            }, 200);
        },

        ejecutarConfirmacion() {
            if (typeof this.confirmModal.onConfirm === "function")
                this.confirmModal.onConfirm();
            this.cerrarConfirmacion();
        },

        async borrarVenta(id) {
            this.abrirConfirmacion(
                "¿Eliminar registro?",
                "El ticket desaparecerá de tu historial y de la base de datos.",
                async () => {
                    try {
                        const response = await fetch(`/ventas/${id}`, {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                Accept: "application/json",
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                            },
                        });

                        if (!response.ok)
                            throw new Error("Error al eliminar la venta");

                        // Actualizamos la vista localmente
                        this.ventas = this.ventas.filter((v) => v.id !== id);
                        if (this.ticketActivo?.id === id)
                            this.ticketActivo = null;

                        // Recargamos los datos desde el servidor para sincronizar
                        this.cargarDatos();
                    } catch (error) {
                        console.error(error);
                        alert(
                            "Hubo un problema al intentar eliminar el registro.",
                        );
                    }
                },
            );
        },

        borrarHistorialFiltrado() {
            const mensajes = {
                dia: `Se eliminarán los tickets del día ${this.valorFiltro}.`,
                mes: `Se eliminarán los tickets del mes de ${this.valorFiltro}.`,
                todas: "Se vaciará por completo el historial de ventas.",
            };

            this.abrirConfirmacion(
                "¿Limpiar historial?",
                mensajes[this.tipoFiltro],
                async () => {
                    // Extraemos solo los IDs de las ventas que actualmente vemos en pantalla
                    const idsABorrar = this.ventasFiltradas.map((v) => v.id);

                    if (idsABorrar.length === 0) {
                        this.cerrarConfirmacion();
                        return;
                    }

                    try {
                        const response = await fetch(`/ventas/bulk`, {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                Accept: "application/json",
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content"),
                            },
                            body: JSON.stringify({ ids: idsABorrar }),
                        });

                        if (!response.ok)
                            throw new Error(
                                "Error al eliminar las ventas de forma masiva",
                            );

                        // Limpiamos la vista
                        this.ventas = this.ventas.filter(
                            (v) => !idsABorrar.includes(v.id),
                        );
                        this.ticketActivo = null;
                        this.tipoFiltro = "todas";

                        // Recargamos los datos para confirmar sincronización
                        this.cargarDatos();
                    } catch (error) {
                        console.error(error);
                        alert(
                            "Hubo un problema al intentar vaciar el historial.",
                        );
                    }
                },
            );
        },

        extraerFechas() {
            const diasSet = new Set();
            const mesesMap = new Map();

            this.ventas.forEach((v) => {
                if (!v.fecha) return;
                const fechaParte = v.fecha.split(",")[0].trim();
                diasSet.add(fechaParte);

                const partes = fechaParte.split("/");
                if (partes.length === 3) {
                    const mesAnio = `${partes[1]}/${partes[2]}`;
                    const fechaObj = new Date(
                        partes[2],
                        parseInt(partes[1]) - 1,
                        1,
                    );
                    let nombreMes = fechaObj.toLocaleString("es-MX", {
                        month: "long",
                        year: "numeric",
                    });
                    mesesMap.set(
                        mesAnio,
                        nombreMes.charAt(0).toUpperCase() + nombreMes.slice(1),
                    );
                }
            });

            this.diasDisponibles = Array.from(diasSet);
            this.mesesDisponibles = Array.from(mesesMap).map(
                ([valor, nombre]) => ({ valor, nombre }),
            );
        },

        filtrarVentas() {
            const filtros = {
                todas: () => this.ventas,
                dia: () =>
                    this.ventas.filter(
                        (v) =>
                            v.fecha &&
                            v.fecha.split(",")[0].trim() === this.valorFiltro,
                    ),
                mes: () =>
                    this.ventas.filter((v) => {
                        if (!v.fecha) return false;
                        const partes = v.fecha.split(",")[0].trim().split("/");
                        return `${partes[1]}/${partes[2]}` === this.valorFiltro;
                    }),
            };
            this.ventasFiltradas = (
                filtros[this.tipoFiltro] || filtros.todas
            )();
            this.totalFiltro = this.ventasFiltradas.reduce(
                (suma, venta) => suma + parseFloat(venta.total),
                0,
            );
        },

        formatMoney(amount) {
            return (
                "$" +
                Number(amount).toLocaleString("es-MX", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                })
            );
        },
    }));
});
