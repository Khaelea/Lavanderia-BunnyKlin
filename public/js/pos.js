function posSystem(servicesDb, suppliesDb, subscriptionsDb, clientsDb) {
    const adaptarCatalogo = (data, category) => {
        if (!data || !Array.isArray(data)) return [];
        return data.map(item => ({
            id: item.id,
            clave_prodserv: item.clave_prodserv || null,
            name: item.name,
            price: parseFloat(item.price),
            category: category,
            description: item.description || null,
            stock: item.stock || null,
            unit: item.unit || null,
            duration_months: item.duration_months || null,
            kilos_per_month: item.kilos_per_month || null,
            is_active: item.is_active ? true : false,
            is_for_orders: item.is_for_orders ? true : false,
        }));
    };
    
    return {
        activeMode: "sale",
        showPreConfirmacion: false,
        showConfirmacion: false,
        ultimaVenta: null,
        itemModal: {
            open: false,
            mode: "add",
            category: "",
            id: null,
            name: "",
            clave_prodserv: "",
            price: "",
            description: "",
            stock: 0,
            unit: "",
            duration_months: 1,
            kilos_per_month: 0,
            is_active: true,
            is_for_orders: false,
        },
        esperandoTerminal: false,
        showErrorModal: false,
        errorPago: "",
        debugStatus: "",
        tiempoFormateado: "",
        
        // Formulario de clientes unificado y avanzado
        clienteForm: {
            tipoRegistro: "nuevo",
            cliente_id: "",
            cliente_texto: "",
            nombre: "",
            telefono: "",
            email: "",
            inicio: new Date().toISOString().split("T")[0],
            fin: "",
        },

        services: adaptarCatalogo(servicesDb, "services"),
        supplies: adaptarCatalogo(suppliesDb, "supplies"),
        subscriptions: adaptarCatalogo(subscriptionsDb, "subscriptions"),
        clients: clientsDb || [],
        cart: [],
        
        intentId: null,
        pollingActive: false,

        formatMoney(amount) {
            return new Intl.NumberFormat("es-MX", { style: "currency", currency: "MXN" }).format(amount);
        },

        get total() {
            return this.cart.reduce((sum, item) => sum + item.price * (parseFloat(item.quantity) || 0), 0);
        },

        toggleMode(mode) {
            this.activeMode = this.activeMode === mode ? "sale" : mode;
        },

        handleItemClick(item, category) {
            if (this.activeMode === "edit") this.openEditModal(item, category);
            else if (this.activeMode === "delete") this.openDeleteModal(item, category);
            else this.addToCart(item, category);
        },

        addToCart(item, category) {
            let found = this.cart.find(i => i.id === item.id && i.category === category);
            if (found) found.quantity++;
            else this.cart.push({ ...item, category, quantity: 1, cart_id: category + "-" + item.id });
        },

        updateQty(index, amount) {
            this.cart[index].quantity = (parseInt(this.cart[index].quantity) || 0) + amount;
            if (this.cart[index].quantity <= 0) this.removeItem(index);
        },

        removeItem(index) { this.cart.splice(index, 1); },
        clearCart() { this.cart = []; },

        // ========== MODALES DE ÍTEMS ==========
        openAddModal(category) {
            this.itemModal = {
                open: true,
                mode: "add",
                category: category,
                id: null,
                name: "",
                clave_prodserv: "",
                price: "",
                description: "",
                stock: 0,
                unit: "",
                duration_months: 1,
                kilos_per_month: 0,
                is_active: true,
                is_for_orders: false,
            };
        },

        openViewModal(item, category) {
            this.itemModal = {
                open: true,
                mode: "view",
                category: category,
                id: item.id,
                clave_prodserv: item.clave_prodserv || null,
                name: item.name,
                price: item.price,
                description: item.description || null,
                stock: item.stock || null,
                unit: item.unit || null,
                duration_months: item.duration_months || null,
                kilos_per_month: item.kilos_per_month || 0,
                is_for_orders: item.is_for_orders ? true : false,
                is_active: item.is_active ? true : false,
            };
        },

        openEditModal(item, category) {
            this.itemModal = {
                open: true,
                mode: "edit",
                category: category,
                id: item.id,
                clave_prodserv: item.clave_prodserv || "",
                name: item.name,
                price: item.price,
                description: item.description || null,
                stock: item.stock || null,
                unit: item.unit || null,
                duration_months: item.duration_months || null,
                kilos_per_month: item.kilos_per_month || 0,
                is_active: item.is_active ? true : false,
                is_for_orders: item.is_for_orders ? true : false,
            };
        },

        openDeleteModal(item, category) {
            this.itemModal = {
                open: true,
                mode: "delete",
                category: category,
                id: item.id,
                clave_prodserv: item.clave_prodserv || null,
                name: item.name,
                price: item.price,
                description: item.description || null,
                stock: item.stock || null,
                unit: item.unit || null,
                duration_months: item.duration_months || null,
                kilos_per_month: item.kilos_per_month || 0,
                is_active: item.is_active ? true : false,
                is_for_orders: item.is_for_orders ? true : false,
            };
        },
        closeModal() { this.itemModal.open = false; },

        async saveItem() {
            if (!this.itemModal.name.trim() || this.itemModal.price === "") return;
            let priceVal = parseFloat(this.itemModal.price) || 0;
            const payload = {
                id: this.itemModal.id,
                category: this.itemModal.category,
                clave_prodserv: this.itemModal.clave_prodserv || "",
                name: this.itemModal.name,
                price: priceVal,
                description: this.itemModal.description,
                stock: this.itemModal.stock,
                unit: this.itemModal.unit,
                duration_months: this.itemModal.duration_months,
                kilos_per_month: parseFloat(this.itemModal.kilos_per_month) || 0,
                is_active: this.itemModal.is_active,
                is_for_orders: this.itemModal.is_for_orders ? true : false,
            };
            
            let targetList = this.itemModal.category === "services" ? this.services :
                             this.itemModal.category === "supplies" ? this.supplies :
                             this.itemModal.category === "subscriptions" ? this.subscriptions : 
                             this.itemModal.category === "extras" ? this.extras : null;
                             
            if (!targetList) return;
            try {
                const headers = {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                };
                if (this.itemModal.mode === "add") {
                    const response = await fetch("/catalogo/guardar", {
                        method: "POST",
                        headers: headers,
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        const errorCrudo = await response.text();
                        console.error("🚨 DETALLE DEL ERROR DE LARAVEL:", errorCrudo);
                        try {
                            const errorJson = JSON.parse(errorCrudo);
                            alert("Error del servidor: " + (errorJson.message || errorJson.error));
                        } catch (e) {
                            alert("Error crítico del servidor. Revisa la consola.");
                        }
                        return;
                    }

                    const data = await response.json();

                    targetList.push({
                        ...data.item,
                        price: parseFloat(data.item.price),
                        category: this.itemModal.category,
                    });
                } else {
                    const res = await fetch("/catalogo/actualizar", { method: "PUT", headers, body: JSON.stringify(payload) });
                    if (!res.ok) throw new Error("Error al actualizar");
                    const data = await res.json();
                    let idx = targetList.findIndex(i => i.id === this.itemModal.id);
                    if (idx !== -1) {
                        targetList[idx].name = data.item.name;
                        targetList[idx].price = parseFloat(data.item.price);
                        targetList[idx].description = data.item.description || null;
                        targetList[idx].stock = data.item.stock || null;
                        targetList[idx].unit = data.item.unit || null;
                        targetList[idx].duration_months = data.item.duration_months || null;
                        targetList[idx].kilos_per_month = data.item.kilos_per_month || null;
                        targetList[idx].clave_prodserv = data.item.clave_prodserv || null;
                        targetList[idx].is_active = data.item.is_active ? true : false;
                        targetList[idx].is_for_orders = data.item.is_for_orders ? true : false;
                        
                        let cartIdx = this.cart.findIndex(c => c.id === this.itemModal.id && c.category === this.itemModal.category);
                        if (cartIdx !== -1) {
                            this.cart[cartIdx].name = data.item.name;
                            this.cart[cartIdx].price = parseFloat(data.item.price);
                        }
                    }
                }
                this.closeModal();
            } catch (e) {
                console.error(e);
                alert("Hubo un problema: " + e.message);
            }
        },

        async deleteItem() {
            const payload = { id: this.itemModal.id, category: this.itemModal.category };
            try {
                const res = await fetch("/catalogo/eliminar", {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                    body: JSON.stringify(payload),
                });
                if (!res.ok) throw new Error("Error al eliminar");
                
                let targetList = this.itemModal.category === "services" ? this.services :
                                 this.itemModal.category === "supplies" ? this.supplies :
                                 this.itemModal.category === "subscriptions" ? this.subscriptions : 
                                 this.itemModal.category === "extras" ? this.extras : null;
                                 
                if (targetList) {
                    let idx = targetList.findIndex(i => i.id === this.itemModal.id);
                    if (idx !== -1) {
                        targetList.splice(idx, 1);
                        this.cart = this.cart.filter(c => c.id !== this.itemModal.id);
                    }
                }
                this.closeModal();
            } catch (e) {
                console.error(e);
                alert("Error al eliminar: " + e.message);
            }
        },

        async toggleServiceStatus(service) {
            const nuevoEstado = !service.is_active;
            service.is_active = nuevoEstado;

            const payload = {
                id: service.id,
                category: service.category || "services", 
                is_active: nuevoEstado,
            };

            try {
                const response = await fetch("/catalogo/toggle-estado", {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    throw new Error("Error en el servidor");
                }
            } catch (error) {
                console.error("Error al cambiar estado:", error);
                service.is_active = !nuevoEstado;
                alert("Hubo un problema de conexión. El estado no se cambió.");
            }
        },

        // ========== FLUJO DE COBRO ==========
        checkout() { 
            if (this.cart.length) {
                const today = new Date();
                const nextMonth = new Date();
                nextMonth.setDate(today.getDate() + 30);

                this.clienteForm.inicio = today.toISOString().split("T")[0];
                this.clienteForm.fin = nextMonth.toISOString().split("T")[0];
                this.showPreConfirmacion = true; 
            }
        },
        cancelarCheckout() { this.showPreConfirmacion = false; },
        cerrarConfirmacion() { this.showConfirmacion = false; },

        mostrarError(mensaje) {
            if (typeof mensaje !== 'string') mensaje = String(mensaje);
            if (mensaje.startsWith('<') || mensaje.includes('Unexpected token')) {
                mensaje = 'Error inesperado. Verifique la conexión.';
            }
            this.errorPago = mensaje;
            this.showErrorModal = true;
            console.error("Error de pago:", mensaje);
        },

        cerrarErrorModal() {
            this.showErrorModal = false;
            this.showPreConfirmacion = true;
        },

        cancelarTerminal(mensaje = "Operación cancelada por el usuario.") {
            this.pollingActive = false;
            this.esperandoTerminal = false;
            this.debugStatus = "";
            this.mostrarError(mensaje);
        },

        async confirmarCheckout(metodo = "Terminal") {
            if (metodo === "Terminal") {
                if (this.total < 5) {
                    this.mostrarError("El monto mínimo es de $5.00 MXN para procesar tarjeta.");
                    return;
                }

                this.showPreConfirmacion = false;
                this.esperandoTerminal = true;
                this.pollingActive = true;
                this.debugStatus = "Conectando...";

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
                    if (!token) throw new Error("Token CSRF no encontrado");

                    const response = await fetch("/terminal/cobrar", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": token
                        },
                        body: JSON.stringify({ total: this.total })
                    });

                    if (!response.ok) {
                        const ct = response.headers.get('content-type');
                        if (ct && ct.includes('application/json')) {
                            const err = await response.json();
                            throw new Error(err.error || err.message || "Error del servidor");
                        } else {
                            throw new Error("El servidor no respondió correctamente.");
                        }
                    }

                    const data = await response.json();
                    if (!data.success) throw new Error(data.error || "No se pudo iniciar el cobro");

                    this.intentId = data.payment_intent_id;
                    let pagoFinalizado = false;
                    let intentos = 0;
                    const MAX_INTENTOS = 60;

                    while (!pagoFinalizado && this.pollingActive && intentos < MAX_INTENTOS) {
                        intentos++;
                        await new Promise(resolve => setTimeout(resolve, 1000));

                        let statusData = null;
                        try {
                            const statusRes = await fetch(`/terminal/estado/${this.intentId}`);
                            if (!statusRes.ok) {
                                this.debugStatus = `Error HTTP ${statusRes.status} (intento ${intentos})`;
                                continue;
                            }
                            const ct = statusRes.headers.get('content-type');
                            if (!ct || !ct.includes('application/json')) {
                                this.debugStatus = `Respuesta no JSON (intento ${intentos})`;
                                continue;
                            }
                            statusData = await statusRes.json();
                        } catch (e) {
                            this.debugStatus = `Error de red (intento ${intentos})`;
                            continue;
                        }

                        if (!statusData.success) {
                            if (statusData.status === 'RETRY') {
                                this.debugStatus = `Reintentando (intento ${intentos})`;
                                continue;
                            }
                            pagoFinalizado = true;
                            throw new Error(statusData.error || 'Error al consultar el estado.');
                        }

                        const status = (statusData.status || '').toUpperCase();
                        const paymentStatus = (statusData.payment_status || '').toUpperCase();
                        this.debugStatus = `Estado: ${status} / Pago: ${paymentStatus} (intento ${intentos})`;

                        if (['OPEN', 'ON_TERMINAL', 'PROCESSING', 'READY'].includes(status)) {
                            continue;
                        }

                        pagoFinalizado = true;

                        if (status === 'FINISHED' && paymentStatus === 'APPROVED') {
                            this.esperandoTerminal = false;
                            this.pollingActive = false;
                            this.debugStatus = "";
                            await this.finalizarVentaLocal("Tarjeta");
                        } else {
                            let msj = 'El pago no fue aprobado.';
                            if (status === 'CANCELED' || status === 'ABANDONED') msj = 'Pago cancelado en la terminal.';
                            else if (status === 'FINISHED' && paymentStatus === 'REJECTED') msj = 'Tarjeta rechazada. Intente otro método.';
                            else if (status === 'ERROR') msj = 'Error en la terminal. Intente nuevamente.';
                            throw new Error(msj);
                        }
                    }

                    if (!pagoFinalizado) {
                        if (!this.pollingActive) return;
                        throw new Error("Tiempo de espera agotado.");
                    }
                } catch (error) {
                    console.error("Error en flujo terminal:", error);
                    this.esperandoTerminal = false;
                    this.pollingActive = false;
                    this.debugStatus = "";
                    this.mostrarError(error.message);
                }
                return;
            }

            this.finalizarVentaLocal("Efectivo");
        },

        async finalizarVentaLocal(metodo) {
            try {
                let clientIdForSale = null;
                const hasSubscription = this.cart.some(item => item.category === "subscriptions");

                // --- LÓGICA DE CLIENTES (Base de Datos Real de tus compañeros) ---
                if (hasSubscription) {
                    if (this.clienteForm.tipoRegistro === "nuevo" && !this.clienteForm.nombre.trim()) {
                        alert("Por favor ingresa el nombre del cliente.");
                        return;
                    }
                    if (this.clienteForm.tipoRegistro === "existente" && !this.clienteForm.cliente_id) {
                        alert("Por favor selecciona un cliente válido de la lista.");
                        return;
                    }

                    // Guardamos o actualizamos al cliente en el servidor y recuperamos su ID
                    clientIdForSale = await this.procesarClientePOS();
                }

                // --- REGISTRO DE LA VENTA EN BD ---
                const payloadVenta = {
                    client_id: clientIdForSale,
                    total: parseFloat(this.total).toFixed(2),
                    metodo_pago: metodo,
                    detalles: this.cart,
                    cliente: this.clienteForm.nombre.trim() || "Público en General"
                };

                const responseVenta = await fetch("/ventas/checkout", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                    body: JSON.stringify(payloadVenta),
                });

                if (!responseVenta.ok) {
                    const errorCrudo = await responseVenta.text();
                    console.error("🚨 ERROR LARAVEL (Venta):", errorCrudo);
                    throw new Error("Error al guardar la venta en la base de datos");
                }

                const dataVenta = await responseVenta.json();

                // Tu guardado local clásico en LocalStorage (Respaldo alternativo)
                if (this.clienteForm.nombre.trim() !== "") {
                    this.registrarClienteLocal();
                }

                // --- CONTROL DE INVENTARIO (Reducción de Stock de Insumos) ---
                this.cart.forEach((itemCart) => {
                    if (itemCart.category === "supplies") {
                        const indexOriginal = this.supplies.findIndex(s => s.id === itemCart.id);
                        if (indexOriginal !== -1) {
                            this.supplies[indexOriginal].stock -= itemCart.quantity;
                            if (this.supplies[indexOriginal].stock < 0) {
                                this.supplies[indexOriginal].stock = 0;
                            }
                        }
                    }
                });

                // --- FINALIZAR PROCESO (Estructuración del ticket de éxito) ---
                this.ultimaVenta = {
                    folio: dataVenta.venta?.reference || dataVenta.venta?.folio || "BK-" + Date.now().toString().slice(-4),
                    fecha: new Date().toLocaleString("es-MX", { timeZone: "America/Mexico_City" }),
                    total: dataVenta.venta?.total || this.total,
                };

                this.showPreConfirmacion = false;
                this.showConfirmacion = true;
                this.clearCart();

                // Reiniciamos el formulario limpio para la siguiente venta
                this.clienteForm = {
                    tipoRegistro: "nuevo",
                    cliente_id: "",
                    cliente_texto: "",
                    nombre: "",
                    telefono: "",
                    email: "",
                    inicio: new Date().toISOString().split("T")[0],
                    fin: "",
                };

            } catch (error) {
                console.error("Error al guardar en BD:", error);
                this.esperandoTerminal = false;
                this.mostrarError(error.message || "Hubo un problema al procesar el cobro.");
            }
        },

        registrarClienteLocal() {
            let subscripcionComprada = this.cart.find(item => item.category === "subscriptions");
            let planName = subscripcionComprada ? subscripcionComprada.name : "Ninguna";
            
            let prendas = this.cart
                .filter(i => i.category === "services")
                .map(i => i.quantity + "x " + i.name)
                .join(", ");

            let clientes = JSON.parse(localStorage.getItem("lavanderia_clientes_final_v2")) || [];
            clientes.unshift({
                id: Date.now(),
                name: this.clienteForm.nombre,
                phone: this.clienteForm.telefono,
                items: prendas || "Solo pago de plan",
                status: "Pendiente",
                subscription: planName,
                subscriptionEndDate: planName !== "Ninguna" ? this.clienteForm.fin : "",
            });
            localStorage.setItem("lavanderia_clientes_final_v2", JSON.stringify(clientes));
        },

        async procesarClientePOS() {
            const subItem = this.cart.find(item => item.category === "subscriptions");
            let payload = {};

            if (this.clienteForm.tipoRegistro === "nuevo") {
                payload = {
                    name: this.clienteForm.nombre,
                    phone: this.clienteForm.telefono,
                    email: this.clienteForm.email,
                    subscription_id: subItem ? subItem.id : null,
                    start_subscription: this.clienteForm.inicio,
                    wantsBilling: false,
                };
            } else {
                const clienteExistente = this.clients.find(c => c.id === parseInt(this.clienteForm.cliente_id));
                payload = {
                    name: clienteExistente.name,
                    phone: clienteExistente.phone,
                    email: clienteExistente.email,
                    subscription_id: subItem ? subItem.id : null,
                    start_subscription: this.clienteForm.inicio,
                    wantsBilling: false,
                };
            }

            const url = this.clienteForm.tipoRegistro === "nuevo" ? "/api/clientes" : `/api/clientes/${this.clienteForm.cliente_id}`;
            const method = this.clienteForm.tipoRegistro === "nuevo" ? "POST" : "PUT";

            const response = await fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const err = await response.json();
                console.error("Error en validación del cliente:", err);
                throw new Error("No se pudo guardar la información del cliente. Verifica los datos.");
            }

            const data = await response.json();

            if (this.clienteForm.tipoRegistro === "nuevo") {
                this.clients.unshift(data.client);
            }

            return data.client.id;
        },

        vincularClienteId() {
            const clienteEncontrado = this.clients.find((c) => {
                const formatoTexto = c.name + " (" + (c.phone || "Sin tel") + ")";
                return formatoTexto === this.clienteForm.cliente_text; // Soporta cliente_text o cliente_texto según input
            });

            if (clienteEncontrado) {
                this.clienteForm.cliente_id = clienteEncontrado.id;
            } else {
                this.clienteForm.cliente_id = "";
            }
        },
    };
}