function posSystem(servicesDb, suppliesDb, subscriptionsDb, extrasDb) {
    const adaptarCatalogo = (data, category) => {
        if (!data || !Array.isArray(data)) return [];
        return data.map(item => ({
            id: item.id,
            clave_prodserv: item.clave_prodserv,
            name: item.name,
            price: parseFloat(item.price),
            category: category,
            description: item.description || null,
            stock: item.stock || null,
            unit: item.unit || null,
            duration_months: item.duration_months || null,
<<<<<<< HEAD
            clave_prodserv: item.clave_prodserv || null,
            is_active: item.is_active ? true : false,
            is_for_orders: item.is_for_orders ? true : false,
=======
            clave_prodserv: item.clave_prodserv || null, // <-- Lo que agregó tu compañero para que cargue la clave SAT
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
        }));
    };

    return {
        activeMode: "sale",
        showPreConfirmacion: false,
        showConfirmacion: false,
        ultimaVenta: null,
        itemModal: {
<<<<<<< HEAD
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
            is_active: true,
            is_for_orders: false,
=======
            open: false, mode: "add", category: "",
            id: null, name: "", clave_prodserv: "", price: "",
            description: "", stock: 0, unit: "", duration_months: 1,
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
        },
        esperandoTerminal: false,
        showErrorModal: false,
        errorPago: "",
        debugStatus: "",
        // Parche para error Alpine
        tiempoFormateado: "",
        clienteForm: { nombre: "", telefono: "", inicio: "", fin: "" },
        
        services: adaptarCatalogo(servicesDb, "services"),
        supplies: adaptarCatalogo(suppliesDb, "supplies"),
        subscriptions: adaptarCatalogo(subscriptionsDb, "subscriptions"),
        extras: adaptarCatalogo(extrasDb, "extras"),
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
<<<<<<< HEAD
                open: true,
                mode: "add",
                category: category,
                id: null,
                name: "",
                price: "",
                description: "",
                stock: 0,
                unit: "",
                duration_months: 1,
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
                is_for_orders: item.is_for_orders ? true : false,
                is_active: item.is_active ? true : false,
=======
                open: true, mode: "add", category: category,
                id: null, name: "", clave_prodserv: "", price: "",
                description: "", stock: 0, unit: "", duration_months: 1
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
            };
        },
        openEditModal(item, category) {
            this.itemModal = {
<<<<<<< HEAD
                open: true,
                mode: "edit",
                category: category,
                id: item.id,
                clave_prodserv: item.clave_prodserv || null,
                name: item.name,
                price: item.price,
                description: item.description || null,
                stock: item.stock || null,
                unit: item.unit || null,
                duration_months: item.duration_months || null,
                is_active: item.is_active ? true : false,
                is_for_orders: item.is_for_orders ? true : false,
=======
                open: true, mode: "edit", category: category,
                id: item.id, name: item.name, clave_prodserv: item.clave_prodserv || "",
                price: item.price, description: item.description || null,
                stock: item.stock || null, unit: item.unit || null,
                duration_months: item.duration_months || null
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
            };
        },
        openDeleteModal(item, category) {
            this.itemModal = {
<<<<<<< HEAD
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
                is_active: item.is_active ? true : false,
                is_for_orders: item.is_for_orders ? true : false,
=======
                open: true, mode: "delete", category: category,
                id: item.id, name: item.name, price: item.price,
                description: item.description || null,
                stock: item.stock || null, unit: item.unit || null,
                duration_months: item.duration_months || null
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
            };
        },
        closeModal() { this.itemModal.open = false; },

        async saveItem() {
            if (!this.itemModal.name.trim() || this.itemModal.price === "") return;
            let priceVal = parseFloat(this.itemModal.price) || 0;
            const payload = {
                id: this.itemModal.id,
                category: this.itemModal.category,
<<<<<<< HEAD
                clave_prodserv: this.itemModal.clave_prodserv,
=======
                clave_prodserv: this.itemModal.clave_prodserv || "",
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
                name: this.itemModal.name,
                price: priceVal,
                description: this.itemModal.description,
                stock: this.itemModal.stock,
                unit: this.itemModal.unit,
                duration_months: this.itemModal.duration_months,
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
<<<<<<< HEAD
                    const response = await fetch("/catalogo/guardar", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        const errorCrudo = await response.text();
                        console.error(
                            "🚨 DETALLE DEL ERROR DE LARAVEL:",
                            errorCrudo,
                        );
                        try {
                            const errorJson = JSON.parse(errorCrudo);
                            alert(
                                "Error del servidor: " +
                                    (errorJson.message || errorJson.error),
                            );
                        } catch (e) {
                            alert(
                                "Error crítico del servidor. Revisa la consola.",
                            );
                        }
                        return;
                    }

                    const data = await response.json();

                    targetList.push({
                        ...data.item,
                        price: parseFloat(data.item.price),
                        category: this.itemModal.category,
                    });
=======
                    const res = await fetch("/catalogo/guardar", { method: "POST", headers, body: JSON.stringify(payload) });
                    if (!res.ok) throw new Error((await res.json().catch(()=>{})).message || "Error al guardar");
                    const data = await res.json();
                    targetList.push({ id: data.item.id, name: data.item.name, price: parseFloat(data.item.price), category: this.itemModal.category, clave_prodserv: data.item.clave_prodserv });
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
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
<<<<<<< HEAD
                        targetList[idx].duration_months =
                            data.item.duration_months || null;
                        targetList[idx].is_active = data.item.is_active
                            ? true
                            : false;
                        targetList[idx].is_for_orders = data.item.is_for_orders
                            ? true
                            : false;

                        // Actualizamos el item en el carrito
                        let cartIdx = this.cart.findIndex(
                            (c) =>
                                c.id === this.itemModal.id &&
                                c.category === this.itemModal.category,
                        );
=======
                        targetList[idx].duration_months = data.item.duration_months || null;
                        targetList[idx].clave_prodserv = data.item.clave_prodserv || null;
                        let cartIdx = this.cart.findIndex(c => c.id === this.itemModal.id && c.category === this.itemModal.category);
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
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

<<<<<<< HEAD
        async toggleServiceStatus(service) {
            // 1. Invertimos el estado localmente para una respuesta visual instantánea (UX)
            const nuevoEstado = !service.is_active;
            service.is_active = nuevoEstado;

            // 2. Preparamos el payload mínimo necesario para el backend
            const payload = {
                id: service.id,
                category: service.category || "services", // Aseguramos que sepa qué tabla actualizar
                is_active: nuevoEstado,
            };

            try {
                // 3. Enviamos la petición al servidor a una nueva ruta específica
                const response = await fetch("/catalogo/toggle-estado", {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    throw new Error("Error en el servidor");
                }

                // (Opcional) Si quieres actualizar el contador de inactivos, Alpine lo hará solo
                // porque el arreglo 'services' ya mutó en el paso 1.
            } catch (error) {
                console.error("Error al cambiar estado:", error);

                // 4. Revertimos el estado visual si la petición falló
                service.is_active = !nuevoEstado;
                alert("Hubo un problema de conexión. El estado no se cambió.");
            }
        },

        addToCart(item, category) {
            let found = this.cart.find(
                (i) => i.id === item.id && i.category === category,
            );
            if (found) {
                found.quantity++;
            } else {
                this.cart.push({
                    ...item,
                    category: category,
                    quantity: 1,
                    cart_id: category + "-" + item.id,
                });
            }
        },

        updateQty(index, amount) {
            this.cart[index].quantity =
                (parseInt(this.cart[index].quantity) || 0) + amount;
            if (this.cart[index].quantity <= 0) this.removeItem(index);
        },

        removeItem(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            this.cart = [];
        },

        get total() {
            return this.cart.reduce(
                (sum, item) =>
                    sum + item.price * (parseFloat(item.quantity) || 0),
                0,
            );
        },

        formatMoney(amount) {
            return new Intl.NumberFormat("es-MX", {
                style: "currency",
                currency: "MXN",
            }).format(amount);
        },

        checkout() {
=======
        // ========== FLUJO DE COBRO ==========
        checkout() { 
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
            if (this.cart.length) {
                const today = new Date();
                const nextMonth = new Date();
                nextMonth.setDate(today.getDate() + 30);

                this.clienteForm = {
                    nombre: "",
                    telefono: "",
                    inicio: today.toISOString().split("T")[0],
                    fin: nextMonth.toISOString().split("T")[0],
                };
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

        cancelarTerminal() {
            this.pollingActive = false;
            this.esperandoTerminal = false;
            this.debugStatus = "";
            this.mostrarError("Operación cancelada por el usuario.");
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
                                console.warn(this.debugStatus);
                                continue;
                            }
                            const ct = statusRes.headers.get('content-type');
                            if (!ct || !ct.includes('application/json')) {
                                this.debugStatus = `Respuesta no JSON (intento ${intentos})`;
                                console.warn(this.debugStatus);
                                continue;
                            }
                            statusData = await statusRes.json();
                            console.log(`Intento ${intentos}:`, statusData);
                        } catch (e) {
                            this.debugStatus = `Error de red (intento ${intentos})`;
                            console.warn(this.debugStatus, e);
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
                        this.debugStatus = `Estado: ${status || '?'} / Pago: ${paymentStatus || '?'} (intento ${intentos})`;

                        // Estados de espera
                        if (['OPEN', 'ON_TERMINAL', 'PROCESSING', 'READY'].includes(status)) {
                            continue;
                        }

                        // Cualquier otro estado es final
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

            // Efectivo
            this.finalizarVentaLocal("Efectivo");
        },

        async finalizarVentaLocal(metodo) {
            try {
                const payload = {
                    total: parseFloat(this.total).toFixed(2),
                    metodo_pago: metodo,
                    detalles: this.cart,
                    cliente: this.clienteForm.nombre.trim() || "Público en General"
                };
                const response = await fetch("/ventas/checkout", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                    body: JSON.stringify(payload),
                });
                if (!response.ok) {
                    const err = await response.json().catch(() => ({}));
                    throw new Error(err.message || "Error al registrar la venta");
                }
                const data = await response.json();
                if (this.clienteForm.nombre.trim() !== "") this.registrarClienteLocal();

                this.ultimaVenta = {
                    folio: data.venta?.folio || data.venta?.reference || "BK-" + Date.now().toString().slice(-4),
                    fecha: new Date().toLocaleString("es-MX"),
                    total: this.total,
                };
                this.showPreConfirmacion = false;
                this.showConfirmacion = true;
                this.clearCart();
            } catch (error) {
                console.error("Error al guardar en BD:", error);
                this.esperandoTerminal = false;
                this.mostrarError("Error: " + error.message);
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
        }
    };
}