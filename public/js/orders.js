document.addEventListener("alpine:init", () => {
    Alpine.data("ordersManager", () => ({
        isSaving: false,
        searchQuery: "",
        isModalOpen: false,
        modalMode: "add",
        orders: [],
        isExistingClient: false,
        showClientDropdown: false,
        availableClients: [],
        currentOrder: {
            id: null,
            client_id: null,
            reference: "",
            name: "",
            phone: "",
            service_id: "",
            quantity: 0,
            details: "",
            total: 0,
            advance: 0,
            status: "Recibido",
            arrivalDate: "",
            deliveryDate: "",
        },
        hasSubscription: false,
        selectedClientKilos: 0,

        async init() {
            await this.cargarDatosDesdeBD();
            await this.cargarListaClientes();
        },

        async cargarDatosDesdeBD() {
            try {
                const response = await fetch("/api/orders/init");
                if (!response.ok) throw new Error("Error al cargar órdenes");

                const data = await response.json();
                this.orders = data.orders.map((o) => this.mapearOrden(o));
                this.availableServices = data.services;
            } catch (error) {
                console.error("Error cargando órdenes:", error);
            }
        },

        // Adaptamos los datos de Laravel a lo que espera tu HTML
        mapearOrden(o) {
            return {
                id: o.id,
                reference: o.reference,
                name: o.client ? o.client.name : "Cliente Mostrador",
                phone: o.client ? o.client.phone : "",
                service_id: o.service_id,
                quantity: o.quantity ? parseFloat(o.quantity) : 1,
                details: o.details,
                total: parseFloat(o.total_price),
                advance: parseFloat(o.advance_payment),
                status: o.status,
                // Formateamos las fechas para los inputs date
                arrivalDate: o.arrival_date
                    ? new Date(o.arrival_date).toISOString().split("T").shift()
                    : "",
                deliveryDate: o.delivery_date
                    ? new Date(o.delivery_date).toISOString().split("T").shift()
                    : "",
            };
        },

        get filteredOrders() {
            if (this.searchQuery === "") return this.orders;
            const q = this.searchQuery.toLowerCase();
            return this.orders.filter(
                (o) =>
                    (o.name && o.name.toLowerCase().includes(q)) ||
                    (o.phone && o.phone.toLowerCase().includes(q)) ||
                    (o.reference && o.reference.toLowerCase().includes(q)),
            );
        },

        getStatusClass(status) {
            switch (status) {
                case "Recibido":
                    return "bg-slate-100 text-slate-600 border-slate-200";
                case "En Proceso":
                    return "bg-[#FFE63C]/30 text-[#1E55AA] border-[#FFE63C]/50";
                case "Listo":
                    return "bg-emerald-50 text-emerald-600 border-emerald-200 shadow-sm";
                case "Entregado":
                    return "bg-blue-50 text-blue-600 border-blue-200";
                default:
                    return "bg-slate-100 text-slate-600 border-slate-200";
            }
        },

        calcularTotalAutomatico() {
            if (!this.currentOrder.service_id || !this.currentOrder.quantity) {
                this.currentOrder.total = 0;
                return;
            }

            const servicioSeleccionado = this.availableServices.find(
                (s) => s.id == this.currentOrder.service_id,
            );

            if (servicioSeleccionado) {
                let qty = parseFloat(this.currentOrder.quantity);
                let precioBase = parseFloat(servicioSeleccionado.price);

                // Si el cliente tiene una suscripción y le quedan kilos
                if (this.hasSubscription && this.selectedClientKilos > 0) {
                    if (qty <= this.selectedClientKilos) {
                        // El encargo completo entra gratis en su suscripción
                        this.currentOrder.total = 0;
                    } else {
                        // El encargo es MAYOR a los kilos que le quedan.
                        // Ejemplo: Trae 10kg pero solo le quedan 4kg gratis.
                        // Solo le cobramos los 6kg excedentes.
                        let kilosExcedentes = qty - this.selectedClientKilos;
                        this.currentOrder.total = kilosExcedentes * precioBase;
                    }
                } else {
                    // Cobro normal (Sin suscripción)
                    this.currentOrder.total = precioBase * qty;
                }
            }
        },

        formatMoney(amount) {
            return new Intl.NumberFormat("es-MX", {
                style: "currency",
                currency: "MXN",
            }).format(amount);
        },

        //generateTicket() {
        //    return "ORD-" + Math.floor(1000 + Math.random() * 9000);
        //},

        openModal(mode, order = null) {
            this.modalMode = mode;
            this.isExistingClient = false; // Resetear checkbox
            this.showClientDropdown = false; // Resetear dropdown

            if (order) {
                console.log({ order });
                this.currentOrder = { ...order };
            } else {
                let formattedToday = new Date()
                    .toISOString()
                    .split("T")
                    .shift();

                this.currentOrder = {
                    id: null,
                    client_id: null, // Asegurar que inicie limpio
                    reference: "",
                    name: "",
                    phone: "",
                    service_id: "",
                    quantity: 0,
                    details: "",
                    total: 0,
                    advance: 0,
                    status: "Recibido",
                    arrivalDate: formattedToday,
                    deliveryDate: "",
                };
            }
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
        },

        async saveOrder() {
            if (this.isSaving) return;

            this.isSaving = true;

            try {
                const token = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");
                const url =
                    this.modalMode === "add"
                        ? "/api/orders"
                        : `/api/orders/${this.currentOrder.id}`;
                const method = this.modalMode === "add" ? "POST" : "PUT";

                // Limpieza básica
                let payload = { ...this.currentOrder };
                if (payload.deliveryDate === "") payload.deliveryDate = null;

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": token,
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    const err = await response.json();
                    console.error("Error validación:", err);
                    alert("Error al guardar: Revisa los campos.");
                    return;
                }

                const result = await response.json();
                const ordenMapeada = this.mapearOrden(result.order);

                if (result.order && result.order.client) {
                    const clientIndex = this.availableClients.findIndex(
                        (c) => c.id === result.order.client.id,
                    );

                    if (clientIndex !== -1) {
                        this.availableClients.splice(
                            clientIndex,
                            1,
                            result.order.client,
                        );
                    } else {
                        this.availableClients.unshift(result.order.client);
                    }
                }

                if (this.modalMode === "add") {
                    this.orders.unshift(ordenMapeada);
                } else {
                    const index = this.orders.findIndex(
                        (o) => o.id === this.currentOrder.id,
                    );
                    if (index !== -1)
                        this.orders.splice(index, 1, ordenMapeada);
                }

                this.closeModal();
            } catch (error) {
                console.error("Error network:", error);
                alert("Hubo un problema de conexión.");
            } finally {
                this.isSaving = false;
            }
        },

        async deleteOrder(id) {
            if (
                confirm(
                    "¿Estás seguro de que deseas eliminar este encargo? Esto también eliminará su registro de venta.",
                )
            ) {
                try {
                    const token = document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content");
                    const response = await fetch(`/api/orders/${id}`, {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-CSRF-TOKEN": token,
                        },
                    });

                    if (response.ok) {
                        this.orders = this.orders.filter((o) => o.id !== id);
                    } else {
                        alert("Error al intentar eliminar el encargo.");
                    }
                } catch (error) {
                    console.error(error);
                }
            }
        },

        async cargarListaClientes() {
            try {
                // Usamos la ruta que creamos cuando hicimos el módulo de clientes
                const response = await fetch("/api/clientes/init");
                if (response.ok) {
                    const data = await response.json();
                    this.availableClients = data.clients;
                }
            } catch (error) {
                console.error("Error cargando el catálogo de clientes:", error);
            }
        },

        get filteredClientsList() {
            if (!this.currentOrder.name) return this.availableClients;
            const q = this.currentOrder.name.toLowerCase();
            return this.availableClients.filter(
                (c) =>
                    c.name.toLowerCase().includes(q) ||
                    (c.phone && c.phone.includes(q)),
            );
        },

        selectClient(c) {
            this.currentOrder.client_id = c.id;
            this.currentOrder.name = c.name;
            this.currentOrder.phone = c.phone || "";
            this.showClientDropdown = false;

            // Lógica para saber si tiene kilos disponibles
            // (Asumimos que el backend mandará 'remaining_kilos' en el objeto del cliente)
            let kilosDisponibles = parseFloat(c.remaining_kilos) || 0;

            if (c.has_active_subscription && kilosDisponibles > 0) {
                this.hasSubscription = true;
                this.selectedClientKilos = kilosDisponibles;
            } else {
                this.hasSubscription = false;
                this.selectedClientKilos = 0;
            }

            // Recalculamos el total por si ya había puesto un peso antes de elegir al cliente
            this.calcularTotalAutomatico();
            console.log({ c });
        },

        clearClientSelection() {
            this.currentOrder.client_id = null;
            this.currentOrder.name = "";
            this.currentOrder.phone = "";
            this.hasSubscription = false;
            this.selectedClientKilos = 0;
            this.calcularTotalAutomatico(); // Recalculamos sin el descuento
        },
    }));
});
