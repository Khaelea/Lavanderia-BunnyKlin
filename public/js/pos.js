function posSystem(servicesDb, suppliesDb, subscriptionsDb, extrasDb) {
    const adaptarCatalogo = (data, category) => {
        return (data || []).map((item) => ({
            id: item.id,
            clave_prodserv: item.clave_prodserv,
            name: item.name,
            price: parseFloat(item.price),
            category: category,
            description: item.description || null,
            stock: item.stock || null,
            unit: item.unit || null,
            duration_months: item.duration_months || null,
            clave_prodserv: item.clave_prodserv || null,
            is_active: item.is_active ? true : false,
            is_for_orders: item.is_for_orders ? true : false,
        }));
    };

    return {
        activeMode: "sale",
        showPreConfirmacion: false,
        showConfirmacion: false,
        lastSale: null,
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
            is_active: true,
            is_for_orders: false,
        },

        clienteForm: { nombre: "", telefono: "", inicio: "", fin: "" },

        services: adaptarCatalogo(servicesDb, "services"),
        supplies: adaptarCatalogo(suppliesDb, "supplies"),
        subscriptions: adaptarCatalogo(subscriptionsDb, "subscriptions"),
        extras: adaptarCatalogo(extrasDb, "extras"),
        cart: [],

        toggleMode(mode) {
            this.activeMode = this.activeMode === mode ? "sale" : mode;
        },

        handleItemClick(item, category) {
            if (this.activeMode === "edit") this.openEditModal(item, category);
            else if (this.activeMode === "delete")
                this.openDeleteModal(item, category);
            else this.addToCart(item, category);
        },

        openAddModal(category) {
            this.itemModal = {
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
            };
        },

        openEditModal(item, category) {
            this.itemModal = {
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
                is_active: item.is_active ? true : false,
                is_for_orders: item.is_for_orders ? true : false,
            };
        },

        closeModal() {
            this.itemModal.open = false;
        },

        async saveItem() {
            if (!this.itemModal.name.trim() || this.itemModal.price === "")
                return;

            let priceVal = parseFloat(this.itemModal.price) || 0;

            const payload = {
                id: this.itemModal.id,
                category: this.itemModal.category,
                clave_prodserv: this.itemModal.clave_prodserv,
                name: this.itemModal.name,
                price: priceVal,
                description: this.itemModal.description,
                stock: this.itemModal.stock,
                unit: this.itemModal.unit,
                duration_months: this.itemModal.duration_months,
                is_active: this.itemModal.is_active,
                is_for_orders: this.itemModal.is_for_orders ? true : false,
            };

            let targetList =
                this.itemModal.category === "services"
                    ? this.services
                    : this.itemModal.category === "supplies"
                      ? this.supplies
                      : this.itemModal.category === "subscriptions"
                        ? this.subscriptions
                        : this.extras;

            try {
                if (this.itemModal.mode === "add") {
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
                } else {
                    const response = await fetch("/catalogo/actualizar", {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) throw new Error("Error al actualizar");

                    const data = await response.json();

                    let idx = targetList.findIndex(
                        (i) => i.id === this.itemModal.id,
                    );
                    if (idx !== -1) {
                        targetList[idx].name = data.item.name;
                        targetList[idx].price = parseFloat(data.item.price);
                        targetList[idx].description =
                            data.item.description || null;
                        targetList[idx].stock = data.item.stock || null;
                        targetList[idx].unit = data.item.unit || null;
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
                        if (cartIdx !== -1) {
                            // Actualizamos sus datos, pero respetamos su .quantity actual
                            this.cart[cartIdx].name = data.item.name;
                            this.cart[cartIdx].price = parseFloat(
                                data.item.price,
                            );
                        }
                    }
                }

                this.closeModal();
            } catch (error) {
                console.error(error);
                alert("Hubo un problema al intentar guardar el elemento.");
            }
        },

        async deleteItem() {
            try {
                // 1. Armamos el paquete solo con lo necesario para borrar
                const payload = {
                    id: this.itemModal.id,
                    category: this.itemModal.category,
                };

                // 2. Disparamos la petición DELETE a Laravel
                const response = await fetch("/catalogo/eliminar", {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok)
                    throw new Error("Error al eliminar en el servidor");

                // 3. Si Laravel lo borró con éxito, actualizamos la vista
                let targetList =
                    this.itemModal.category === "services"
                        ? this.services
                        : this.itemModal.category === "supplies"
                          ? this.supplies
                          : this.subscriptions;

                let idx = targetList.findIndex(
                    (i) => i.id === this.itemModal.id,
                );

                if (idx !== -1) {
                    targetList.splice(idx, 1); // Lo quitamos del catálogo

                    // Sacamos del carrito si es que estaba agregado antes de borrarlo
                    this.cart = this.cart.filter(
                        (c) => c.id !== this.itemModal.id,
                    );
                }

                // Cerramos el modal
                this.closeModal();
            } catch (error) {
                console.error(error);
                alert("Hubo un problema al intentar eliminar el elemento.");
            }
        },

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

        cancelarCheckout() {
            this.showPreConfirmacion = false;
        },

        async confirmarCheckout() {
            try {
                // Preparamos los datos para Laravel
                const payload = {
                    total: this.total,
                    metodo_pago: "Efectivo",
                    detalles: this.cart,
                };

                // Enviamos la petición al servidor
                const response = await fetch("/ventas/checkout", {
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
                    console.error("🚨 ERROR LARAVEL:", errorCrudo);
                    throw new Error(
                        "Error al guardar la venta en la base de datos",
                    );
                }

                // Recibimos la Venta confirmada desde Laravel (con el Folio real generado)
                const data = await response.json();

                // Actualizamos la última venta para el ticket en pantalla
                this.ultimaVenta = {
                    folio: data.venta.reference, // Usamos la referencia que generó Laravel
                    fecha: new Date().toLocaleString("es-MX", {
                        timeZone: "America/Mexico_City",
                    }),
                    total: data.venta.total,
                };

                // --- LÓGICA DE CLIENTES (Se mantiene en LocalStorage por ahora) ---
                if (this.clienteForm.nombre.trim() !== "") {
                    let subscripcionComprada = this.cart.find(
                        (item) => item.category === "subscriptions",
                    );
                    let planName = subscripcionComprada
                        ? subscripcionComprada.name
                        : "Ninguna";
                    let prendas = this.cart
                        .filter((i) => i.category === "services")
                        .map((i) => i.quantity + "x " + i.name)
                        .join(", ");

                    let clientes =
                        JSON.parse(
                            localStorage.getItem(
                                "lavanderia_clientes_final_v2",
                            ),
                        ) || [];
                    clientes.unshift({
                        id: Date.now(),
                        name: this.clienteForm.nombre,
                        phone: this.clienteForm.telefono,
                        items: prendas || "Solo pago de plan",
                        status: "Pendiente",
                        subscription: planName,
                        subscriptionEndDate:
                            planName !== "Ninguna" ? this.clienteForm.fin : "",
                    });
                    localStorage.setItem(
                        "lavanderia_clientes_final_v2",
                        JSON.stringify(clientes),
                    );
                }
                // ------------------------------------------------------------------

                // Mostramos el ticket de éxito
                this.showPreConfirmacion = false;
                this.showConfirmacion = true;

                // Limpiamos el carrito
                this.clearCart();
            } catch (error) {
                console.error(error);
                alert("Hubo un problema al procesar el cobro.");
            }
        },

        cerrarConfirmacion() {
            this.showConfirmacion = false;
        },
    };
}
