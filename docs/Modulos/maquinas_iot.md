# Módulo: Máquinas IoT

## Flujo General del Módulo

# 1. Descripción General

El módulo de **Máquinas IoT** permite la administración, control y monitoreo en tiempo real de los dispositivos físicos del local (lavadoras y secadoras).

Su objetivo principal es ofrecer una interfaz gráfica interactiva que muestre:

- El estado operativo de cada máquina.
- El tipo de uso asignado.
- El tiempo restante de operación mediante barras de progreso dinámicas.

> **Estado del Módulo:** **Detenido temporalmente**

El desarrollo de la integración con el backend se encuentra pausado debido a la falta de los dispositivos de hardware finales necesarios para realizar las pruebas de conectividad y verificar la funcionalidad completa de los endpoints de telemetría.

---

# 2. Modelo de Datos y Persistencia

> **Estado actual:** Pendiente de implementación.

Por el momento, el almacenamiento y la persistencia en Base de Datos permanecen sin definición formal debido al estado de pausa del hardware.

Actualmente, toda la información se maneja de forma **volátil** en memoria del lado del cliente.

---

# 3. Arquitectura del Backend (Controladores y Rutas)

> **Pendiente de implementación.**

Las rutas y controladores dentro del framework **Laravel** serán desarrollados una vez que se definan los protocolos de comunicación entre la aplicación y el hardware IoT.

---

# 4. Interfaz de Usuario (Frontend - Blade)

## Vista Principal

```text
resources/views/pages/maquinas.blade.php
```

## Descripción de la Interfaz

La vista está diseñada mediante un esquema responsivo compuesto por un conjunto de **tarjetas interactivas (Cards)**.

Cada tarjeta representa un dispositivo físico del local.

La interfaz permite:

- Visualizar dispositivos precargados.
- Añadir nuevas máquinas.
- Editar la información de una máquina.
- Eliminar dispositivos existentes.

> **Nota:** Debido a que actualmente el sistema funciona en modo de simulación, cualquier modificación realizada únicamente existe en memoria y se pierde al recargar el navegador.

---

# 5. Lógica del Cliente (JavaScript de Simulación)

## Script Asociado

Código JavaScript embebido dentro de:

```text
resources/views/pages/maquinas.blade.php
```

## Estructuras de Datos Locales

### `machines`

Arreglo constante encargado de almacenar la información de las lavadoras y secadoras preestablecidas con las que se inicializa la simulación del sistema.

## Funciones Clave

### `washBadge()`

Determina y asigna dinámicamente el color correspondiente a la etiqueta de estado de cada máquina según sus condiciones actuales de operación.

### Otras funciones

Agregar aquí las funciones existentes del script utilizando verbos en infinitivo, por ejemplo:

- `renderizarTarjetas()`
- `inicializarCronometro()`
- `actualizarProgreso()`
- `agregarMaquina()`
- `editarMaquina()`
- `eliminarMaquina()`

---

# 6. Integraciones o Dependencias Externas

## Emulación de Hardware

El comportamiento de los eventos de red y la telemetría IoT se simulan localmente mediante:

- Temporizadores (`setInterval`)
- Funciones asíncronas de JavaScript

Estas simulaciones emulan la respuesta que, en un entorno real, proporcionarían los microcontroladores, sensores y demás dispositivos físicos conectados al sistema.

---

# 7. Rutas de acceso

Para el apartado de Maquinas IoT se cuenta con una ruta de acceso para el menú lateral:

```php
Route::get(
    '/maquinas', function () { 
        return view('pages.maquinas', ['title' => 'Máquinas IoT']); 
        }
    )->name('maquinas');
```