# Módulo: Punto de Venta (POS) y Ecosistema de Pagos

El **Módulo POS** es el componente de mayor densidad estructural en la
plataforma. Orquesta en tiempo real el ciclo de compras de lavandería,
integrando suscripciones mensuales automatizadas, control inventarial de insumos
y múltiples canales de captación de capital mediante **Mercado Pago**
(Terminales Físicas y Pasarelas Digitales).

## 1. Arquitectura Topológica de Venta

A diferencia de catálogos servidos por SSR (Server Side Rendering) estándar, el
POS está diseñado para mitigar latencias inyectando todo el ecosistema de base
de datos a un árbol de dependencias Reactivo (AlpineJS) desde la primera carga.

### Inyección de Memoria (RAM)

La vista base (`pos.blade.php`) no ejecuta consultas en vivo contra la base de
datos por cada pulsación de botón de usuario. Utiliza la directiva nativa
`@Js::from($x)` de Laravel Blade para traspasar serializaciones JSON gigantescas
sobre categorías discretas al constructor del cliente:

- `servicesDb` (Lavado, Secado)
- `suppliesDb` (Insumos, Suavizantes, Plásticos)
- `subscriptionsDb` (Membresías por Kilos)
- `clientsDb` (Toda la cartera de CRM, para filtrado y cruce sin delays HTTP)

---

## 2. Motor de Frontend en Cliente (`pos.js`)

Centralizado en el componente funcional
`posSystem(servicesDb, suppliesDb, subscriptionsDb, clientsDb)`, el ecosistema
rige bajo las siguientes arquitecturas:

### a. Control del Árbol de Estado Reactivo (`activeMode`)

La UI oscila sobre una máquina de estados para reciclar ventanas modales
(`sale`, `edit`, `delete`, `add`). Esto restringe el accionar del usuario (e.g.
Si entra a modo `edit`, tocar una prenda abre en cascada `/catalogo/actualizar`
a la BD local, en lugar de agregar la prenda a la canasta financiera `cart`).

### b. Motor del Carrito (`cart` y matemáticas volátiles)

Al inyectar a `addToCart(item, category)`, el Array de cobro absorbe deep-copies
en memoria de los catálogos.

- Utiliza la función computada getter `get total()` evaluando la sumatoria
  volumétrica `(sum + item.price * item.quantity)` al vuelo, protegiéndola de
  recargas parciales.

### c. Creador Incrustado de CRM (`procesarClientePOS`)

Cuando en el POS se procesa a un cliente "Nuevo" vinculando la compra a una
"Suscripción", el Frontend suspende momentáneamente el CheckOut general. Dispara
una petición XHR a `/api/clientes` para registrar al usuario permanentemente
contra la API, obtener su UUID generado e insertarlo retroactivamente en el
Payload del Cobro final de la venta, garantizando una relación Foreign Key
impecable de un solo tramo para el Cajero (Empleado).

### d. Lógica Parcial Offline (`registrarClienteLocal`)

Como medida de seguridad, además del respaldo por servidor, la caja dispara logs
secundarios hacia `localStorage` (`lavanderia_clientes_final_v2`) construyendo
una matriz pura en JavaScript para prevenir catástrofes totales ante
desconexiones HTTP del negocio.

---

## 3. Topología Financiera y Terminal Físico: MercadoPago Point

**Archivos involucrados:** `TerminalController.php`, `pos.js`

El punto más frágil de cualquier caja asíncrona moderna es la sincronización y
confirmación de pagos en hardware físico externo (terminales). Para evitar
bloqueos (deadlocks) en la interfaz del cajero o pagos truncados, este
ecosistema maneja el flujo de Point de la siguiente manera:

### 3.1. Payment Intent (Intención de Pago)

El ciclo inicia cuando Javascript emite una solicitud a `/terminal/cobrar`. El
controlador `TerminalController` construye la intención monetaria multiplicando
por 100 el monto (para ajustarlo a centavos, requerimiento de la API) y
enviándolo mediante la librería `PointClient`.

- **`device_id`**: Es el identificador físico inyectado por variables de entorno
  (`MERCADOPAGO_POINT_DEVICE_ID`).
- La API de Mercado Pago "despierta" el hardware físico en el establecimiento y
  devuelve un `payment_intent_id`, el cual es capturado por el Frontend.

### 3.2. Aggressive Polling en JavaScript (Sondeo de Estado)

Dado que Mercado Pago Point no usa WebSockets bidireccionales automáticos en
este esquema, `pos.js` implementa un ciclo de interrogación constante
(Aggressive Polling):

- Ejecuta un ciclo `while` limitado con un candado a 60 iteraciones
  (`MAX_INTENTOS = 60`), es decir, 60 segundos de vida.
- Emite transacciones `fetch` hacia `/terminal/estado/{id}` separadas por un
  `setTimeout` de 1000 milisegundos.
- Aísla la interfaz de usuario con modales de carga (`esperandoTerminal`)
  evitando doble facturación.

### 3.3. Intervención del Controlador frente a Hardware Deficiente (El "Salvavidas")

Uno de los descubrimientos más críticos y mitigados dentro de
`TerminalController.php` fue el tratamiento de errores periféricos en
dispositivos Point Smart.

- Mercado Pago posee dos directivas de estado en su Payload: El estado del
  "Dinero" (`['payment']['state']`) y el estatus general de la "Terminal"
  (`['state']`).
- **El Problema**: Si el hardware físico se queda sin rollo de papel para
  imprimir, o pierde momentáneamente su enlace WiFi justo después de haber
  cobrado la tarjeta del cliente, el estado de la Terminal colapsa emitiendo un
  flag `ERROR` o `ABANDONED`. Esto provocaría que el ciclo Frontend interpretara
  la venta como rechazada, cancelando el ticket de lavandería a pesar de que el
  dinero ya está en la cuenta de banco del negocio.
- **La Solución Estructural**:

```php
$estadoPago = $data['payment']['state'] ?? null;
$estadoIntent = $data['state'] ?? 'UNKNOWN';

// OVERRIDE FORZADO
if ($estadoPago === 'approved') {
    $estadoIntent = 'FINISHED'; 
}
```

Indiferentemente del diagnóstico físico reportado por el dispositivo plástico,
si el banco emisor aprueba el cobro (`approved`), el backend sobrescribe el
vector forzando a `FINISHED`. Esto colapsa victoriosamente el Polling del
navegador, permitiendo insertar la venta local de inmediato sin regalar el
servicio al cliente ni causar pérdidas cuadráticas.

---

## 4. Orquestación Paralela: Canales de Pago Automático (Plugins In-App)

Para habilitar componentes de venta sin intervención humana, la arquitectura
expone controladores abstractos centralizados a través de `MercadoPagoService`,
resolviendo variables complejas en entornos web.

### a. `PagoController` (Web Checkout - Preference Redirect)

Diseñado para minimizar la carga de PCI-Compliance en el servidor.

- Transforma la matriz de compra a `Preferences` estandarizados de Mercado Pago.
- Expone un JSON estructurado de retornos (Back URLs: `/pago/exito`,
  `/pago/fallo`).
- Ejecuta un `redirect()->away($respuesta['init_point']);` sacando al cliente
  completamente de la estructura nativa para procesar transacciones en la red
  neuronal hosteada por MercadoPago, eludiendo problemas de certificación o de
  captura de datos sensibles de la Tarjeta.

### b. `BrickPagoController` (Componentes Modulares Transparentes)

Habilita la incrustación de modales estéticos (Payment Bricks) donde el cliente
percibe que realiza el pago dentro del entorno local de ventas ("Transparent
Checkout").

- El Controlador absorbe tokens generados por I-Frames seguros de Mercado Pago
  Front.
- Recibe validaciones estrictas (`transaction_amount`, `installments`,
  identificadores `DNI/CURP`).
- Genera el cargo forzado por backend validando (Backend2Backend) evadiendo la
  necesidad de exponer claves maestras. Emite redirecciones finales basadas en
  el retorno binario `status === 'approved'` o `status === 'pending'`.

---

## 5. Convergencia y Cierre Estructural (Integridad Logística Local)

Independientemente del origen monetario validado (Terminal Física, Efectivo en
mostrador, o Checkout Brick), el embudo unifica todo sobre el co-proceso
subyacente `finalizarVentaLocal($metodo)` de AlpineJS. Al inyectarle la señal de
pago aprobad, sus ramas asíncronas detonan en cascada lo siguiente:

1. **Gestión Sensible de Inventario en Tiempo Real**: Deduce iterativamente
   insumos vendidos al vuelo
   (`this.supplies[indexOriginal].stock -= itemCart.quantity`) y fuerza
   blindajes matemáticos (`< 0 => 0`) evadiendo descuadres directos en la UI
   mostrada al cajero.
2. **Commit Masivo a la Base de Datos**: Manda el Request Array completo
   (`this.cart`) vía método POST hacia `/ventas/checkout`. Esta ruta interfiere
   con `$this->ventaService` (Transactions DB), encapsulando de foráneos todo el
   peso del polimorfismo.
3. **Reset Limpio de Estado Frontend**: Libera la memoria RAM pre-vinculada
   eliminando colecciones inyectadas del carrito (`this.cart = []`), blanquea
   variables reactivas secundarias de subscripción y CRM local, y muta
   visualmente el estado del render al Receipt (Ticket de Venta finalizando la
   UI con exito).
