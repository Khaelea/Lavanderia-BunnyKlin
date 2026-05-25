document.addEventListener("alpine:init", () => {
    Alpine.data("historialSystem", () => ({
        ventas: [],
        ventasFiltradas: [],
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
            this.$watch("tipoFiltro", (value) => {
                if (value === "todas") {
                    this.valorFiltro = "";
                } else if (
                    value === "mes" &&
                    this.mesesDisponibles.length > 0
                ) {
                    this.valorFiltro = this.mesesDisponibles[0].valor;
                } else if (value === "dia" && this.diasDisponibles.length > 0) {
                    this.valorFiltro = this.diasDisponibles[0];
                }
                this.filtrarVentas();
            });
            this.$watch("valorFiltro", () => this.filtrarVentas());
        },

        // Adaptado para leer la relación 'items' de Laravel o tus respaldos locales
        getDetalles(venta) {
            console.log(
                venta?.items || venta?.detalles || venta?.productos || [],
            );
            return venta?.items || venta?.detalles || venta?.productos || [];
        },

        async cargarDatos() {
            try {
                // Hacemos la petición al controlador de Laravel
                const response = await fetch("/ventas/api-historial");

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const dataCruda = await response.json();

                // Mapeamos los datos reales de la base de datos a la estructura que espera Alpine
                this.ventas = dataCruda.data.map((venta) => {
                    // Formateamos la fecha a DD/MM/YYYY, HH:MM
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
                    const fechaFormateada = `${dia}/${mes}/${anio}, ${hora}:${minutos}`;

                    return {
                        id: venta.id,
                        folio: venta.reference,
                        fecha: fechaFormateada,
                        total: parseFloat(venta.total),
                        // Nos aseguramos de que items sea siempre un arreglo
                        items: venta.items || [],
                    };
                });

                // Si todo sale bien, eliminamos la basura vieja del localStorage para evitar confusiones futuras
                localStorage.removeItem("historial_ventas");
            } catch (error) {
                console.error(
                    "🚨 Error cargando el historial desde la base de datos:",
                    error,
                );
                // Si la base de datos falla, vaciamos la tabla en vez de mostrar datos falsos.
                this.ventas = [];
                alert(
                    "No se pudo conectar con la base de datos para cargar el historial.",
                );
            }

            // Estas funciones reconstruyen los filtros y la vista con los datos nuevos
            this.extraerFechas();
            this.filtrarVentas();
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
