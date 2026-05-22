document.addEventListener("alpine:init", () => {
    Alpine.data("clientManager", () => ({
        searchQuery: "",
        isModalOpen: false,
        modalMode: "add",
        clients: [],
        planesPOS: [],

        currentClient: {
            id: null,
            name: "",
            phone: "",
            email: "", // <-- NUEVO
            subscription_id: "",
            start_subscription: new Date().toISOString().split("T").shift(),
            wantsBilling: false,
            rfc: "",
            razon_social: "",
            regimen_fiscal: "",
            same_billing_address: false,
            codigo_postal: "",
            calle: "",
            numero_exterior: "",
            numero_interior: "",
            colonia: "",
            ciudad: "",
            estado: "",
        },

        async init() {
            await this.cargarDatosDesdeBD();
        },

        async cargarDatosDesdeBD() {
            try {
                const response = await fetch("/api/clientes/init");
                if (!response.ok)
                    throw new Error("Error al cargar datos iniciales");

                const data = await response.json();

                // Mapeamos los clientes para adaptar la vista
                this.clients = data.clients.map((c) => this.mapearCliente(c));
                this.planesPOS = data.subscriptions;
            } catch (error) {
                console.error("Error cargando el dashboard:", error);
            }
        },

        // Helper para adaptar el modelo de Laravel a lo que espera tu HTML
        mapearCliente(c) {
            return {
                ...c,
                subscription_name: c.subscription
                    ? c.subscription.name
                    : "Ninguna",
                wantsBilling: c.rfc ? true : false,
            };
        },

        get filteredClients() {
            if (this.searchQuery === "") return this.clients;
            return this.clients.filter(
                (c) =>
                    c.name
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase()) ||
                    (c.phone &&
                        c.phone
                            .toLowerCase()
                            .includes(this.searchQuery.toLowerCase())),
            );
        },

        // Modificado para usar end_subscription
        getSubscriptionStatus(client) {
            if (!client.end_subscription)
                return { text: "Sin fecha", class: "hidden" };

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const endDate = new Date(client.end_subscription + "T12:00:00");
            endDate.setHours(0, 0, 0, 0);

            const diffTime = endDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays < 0) {
                return {
                    text: "CADUCADA",
                    class: "bg-rose-50 text-rose-600 border-rose-200",
                };
            } else if (diffDays === 0) {
                return {
                    text: "ACTIVA (Vence hoy)",
                    class: "bg-[#FFE63C]/30 text-[#1E55AA]",
                };
            } else {
                return {
                    text: `ACTIVA (Vence en ${diffDays} días)`,
                    class: "bg-emerald-50 text-emerald-600 border-emerald-200",
                };
            }
        },

        // Lógica para auto-calcular fecha según el modelo Subscription
        actualizarFechaVencimiento() {
            if (!this.currentClient.subscription_id) {
                this.currentClient.end_subscription = "";
                return;
            }

            if (!this.currentClient.end_subscription) {
                const plan = this.planesPOS.find(
                    (p) => p.id == this.currentClient.subscription_id,
                );
                if (plan && plan.duration_months) {
                    let d = new Date();
                    d.setMonth(d.getMonth() + plan.duration_months);
                    this.currentClient.end_subscription = d
                        .toISOString()
                        .split("T");
                }
            }
        },

        openModal(mode, client = null) {
            this.modalMode = mode;

            // Obtenemos la fecha de hoy en formato YYYY-MM-DD
            const hoy = new Date().toISOString().split("T").shift();

            if (client) {
                this.currentClient = {
                    ...client,
                    start_subscription: hoy, // Por si edita el plan, que inicie hoy
                };
            } else {
                this.currentClient = {
                    id: null,
                    name: "",
                    phone: "",
                    email: "",
                    subscription_id: "",
                    start_subscription: hoy,
                    wantsBilling: false,
                    rfc: "",
                    razon_social: "",
                    regimen_fiscal: "",
                    same_billing_address: false,
                    codigo_postal: "",
                    calle: "",
                    numero_exterior: "",
                    numero_interior: "",
                    colonia: "",
                    ciudad: "",
                    estado: "",
                };
            }
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
        },

        async saveClient() {
            try {
                // Obtener CSRF Token de la etiqueta meta
                const token = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");

                const url =
                    this.modalMode === "add"
                        ? "/api/clientes"
                        : `/api/clientes/${this.currentClient.id}`;
                const method = this.modalMode === "add" ? "POST" : "PUT";

                // --- LIMPIEZA DE DATOS ANTES DE ENVIAR ---
                let payload = JSON.parse(JSON.stringify(this.currentClient));

                // Si el ID de suscripción viene vacío o es "0", lo hacemos null
                if (
                    !payload.subscription_id ||
                    payload.subscription_id === ""
                ) {
                    payload.subscription_id = null;
                    // Y SI NO HAY SUSCRIPCIÓN, LA FECHA DE INICIO DEBE SER NULL
                    payload.start_subscription = null;
                }
                // Si hay suscripción, pero la fecha está vacía, también a null
                else if (payload.start_subscription === "") {
                    payload.start_subscription = null;
                }

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": token,
                    },
                    // Enviamos nuestro payload limpio en lugar de this.currentClient directamente
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    const err = await response.json();
                    console.error("Error validación:", err);
                    alert("Error al guardar: Revisa los campos requeridos.");
                    return;
                }

                const result = await response.json();

                // Actualizamos la tabla localmente sin tener que recargar toda la BD
                const clienteMapeado = this.mapearCliente(result.client);

                if (this.modalMode === "add") {
                    this.clients.unshift(clienteMapeado);
                } else {
                    const index = this.clients.findIndex(
                        (c) => c.id === this.currentClient.id,
                    );
                    if (index !== -1)
                        this.clients.splice(index, 1, clienteMapeado);
                }

                this.closeModal();
            } catch (error) {
                console.error("Error network:", error);
                alert("Hubo un problema de conexión al guardar el cliente.");
            }
        },

        async deleteClient(id) {
            if (
                confirm("¿Estás seguro de que deseas eliminar este registro?")
            ) {
                try {
                    const token = document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content");
                    const response = await fetch(`/api/clientes/${id}`, {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-CSRF-TOKEN": token,
                        },
                    });

                    if (response.ok) {
                        this.clients = this.clients.filter((c) => c.id !== id);
                    } else {
                        alert("Error al intentar eliminar el registro.");
                    }
                } catch (error) {
                    console.error(error);
                }
            }
        },

        formatDate(dateString) {
            if (!dateString) return "";

            // Forzamos la zona horaria para evitar desfases de un día
            const date = new Date(dateString);
            // Agregamos la zona horaria a la fecha para evitar que se reste un día por el UTC
            const userTimezoneOffset = date.getTimezoneOffset() * 60000;
            const correctedDate = new Date(date.getTime() + userTimezoneOffset);

            return correctedDate.toLocaleDateString("es-MX", {
                year: "numeric",
                month: "long",
                day: "numeric",
            });
        },
    }));
});
