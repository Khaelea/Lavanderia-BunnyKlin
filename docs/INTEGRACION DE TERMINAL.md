# Integración de Terminal Física Mercado Pago

Este documento detalla el flujo de integración, configuración de credenciales y la arquitectura lógica implementada en el sistema para conectar los puntos de venta con las terminales físicas de Mercado Pago.

---

## 1. Obtención de Credenciales

Las credenciales de Mercado Pago están directamente vinculadas a la aplicación que se genera a través del apartado corporativo de integraciones.

### Pasos para Obtener las Credenciales:

1. Ingrese a [Mercado Pago Developers](https://www.mercadopago.com.mx/developers/es). En la esquina superior derecha, haga clic en **Ingresar** e inicie sesión con su cuenta. Posteriormente, acceda al apartado de **Tus integraciones**.

<img width="921" height="96" alt="image" src="https://github.com/user-attachments/assets/30e6d650-d52a-4771-9aa0-1bd288f87188" />

2. Seleccione una aplicación existente o cree una nueva interfaz si aún no lo ha hecho.

<img width="896" height="201" alt="image" src="https://github.com/user-attachments/assets/a266389d-b9f9-47d0-8e06-08cd66b2bb6d" />

* **2.1.** Asigne el título correspondiente a la aplicación y haga clic en el botón de **Continuar**.

<img width="804" height="438" alt="image" src="https://github.com/user-attachments/assets/6384ff7d-6426-44ab-ab2d-4e186c8cc76b" />

* **2.2.** En el tipo de solución, elija estrictamente las opciones de **"Pagos presenciales"** y **"Con un desarrollo propio"**.

<img width="788" height="476" alt="image" src="https://github.com/user-attachments/assets/868715f1-6f70-4206-8161-4d302ebe3ae0" />
<img width="921" height="384" alt="image" src="https://github.com/user-attachments/assets/e61f6633-f8ec-4f75-9568-348226c1f5d1" />

* **2.3.** Verifique la declaración de términos de uso y haga clic en el botón de **Confirmar**.

<img width="921" height="620" alt="image" src="https://github.com/user-attachments/assets/9915d285-8b76-47ef-9e8c-870fcf926eae" />

3. Una vez creada la aplicación de forma exitosa, el sistema desplegará el panel principal de control. Para obtener los tokens de producción, haga clic en el apartado de **Credenciales productivas**.

<img width="735" height="376" alt="image" src="https://github.com/user-attachments/assets/22454184-1834-439a-83d5-ddfce89b1b41" />

* **3.1.** Haga clic en **Activar credenciales**. Complete el formulario de datos comerciales e información requerida por la plataforma. Al finalizar, confirme mediante el botón **Activar credenciales de producción**.

<img width="875" height="376" alt="image" src="https://github.com/user-attachments/assets/b6d110a4-dbf1-4db5-b705-1fc47fc4803e" />

4. Copie y resguarde los valores generados bajo los campos **Access Token** y **Client Secret**. Serán indispensables en los archivos de configuración del entorno del servidor.

<img width="737" height="232" alt="image" src="https://github.com/user-attachments/assets/892c7d18-ae42-401f-a992-6d204f773b1e" />

---

## 2. Configuración del Entorno y Hardware

Para entrelazar el software con el dispositivo Point físico, es necesario recuperar los identificadores de red y mapearlos en el backend.

1. **Obtención del Device ID:** Utilice un cliente HTTP como **Postman** para consultar los dispositivos vinculados y recuperar el identificador único del hardware.
   * **Identificador de terminal activo:** `NEWLAND_N950__N950NCCB05478475`
2. **Estado del Hardware:** Asegúrese de que la terminal física permanezca encendida, con conexión estable a internet (Wi-Fi/4G) y correctamente emparejada a la cuenta comercial mediante la aplicación móvil de Mercado Pago.
3. **Variables de Entorno:** Diríjase al archivo `.env` ubicado en la raíz del proyecto web.

<img width="267" height="97" alt="image" src="https://github.com/user-attachments/assets/cd16d27a-1ef4-458b-b28a-1467bf9e0b25" />

4. Ubique la sección de configuración de pasarelas de pago (Línea 59 aproximadamente) e ingrese las credenciales productivas obtenidas anteriormente en sus respectivas variables.

---

## 3. Arquitectura del Código y Reglas de Negocio

A continuación, se desglosa la lógica técnica distribuida entre el Frontend (JavaScript) y el Backend (PHP/Laravel) que gobierna el comportamiento del punto de venta.

### 📊 1. Interfaz de Usuario y Control de Interrupciones
* **Archivo de Origen:** `\public\js\pos.js` (Línea 427)
* **Gestión de UI:** Centraliza el manejo de excepciones visuales en la caja. La función `mostrarError` integra un mecanismo de sanitización (`startsWith("<")`) que intercepta fallas del servidor (como errores HTTP 500) y los procesa en mensajes legibles, impidiendo que código crudo rompa la experiencia del operador.
* **Mecanismo de Seguridad (Kill Switch):** La función `cancelarTerminal` actúa como un interruptor de emergencia. Al mutar el estado de `this.pollingActive` a `false`, se rompe de forma inmediata el bucle de escucha asíncrona, liberando el Punto de Venta si la operación es abandonada.

<img width="555" height="328" alt="image" src="https://github.com/user-attachments/assets/b0b53ae1-1b62-4df4-a302-2bbae75a7f77" />

### 🚀 2. Inicio de Transacción y Petición de Cobro
* **Archivo de Origen:** `\public\js\pos.js` (Línea 451)
* **Validación en Frontend:** Antes de saturar la red, el script valida que el importe total cumpla las reglas de negocio de Mercado Pago (monto mínimo de $5.00 MXN), evitando peticiones innecesarias a la API.
* **Control de Estados:** Al inicializar, se activan de manera síncrona las banderas `esperandoTerminal` (bloqueo del panel del cajero) y `pollingActive` (apertura del canal de escucha).
* **Seguridad Web:** El proceso extrae dinámicamente el token CSRF del DOM para firmar la petición asíncrona (`fetch`) enviada al endpoint `/terminal/cobrar`, mitigando ataques de falsificación de peticiones en sitios cruzados.

<img width="778" height="615" alt="image" src="https://github.com/user-attachments/assets/e3ab3275-f24f-410f-abfe-6ad4ab268992" />

### 🔄 3. Ciclo de Escucha Activa ("Polling")
* **Archivo de Origen:** `\public\js\pos.js`
* **Identificador de Intención (`intentId`):** El sistema captura y almacena de forma persistente el ticket único (`intentId`) devuelto por la API para mantener la trazabilidad de la operación actual.
* **Estrategia de Consulta:** Se ejecuta un ciclo controlado `while` limitado a un máximo de 60 iteraciones (equivalente a 1 minuto de tolerancia). A través de una promesa y un `setTimeout`, se fuerza un retraso síncrono de 1000ms (1 segundo) entre consultas para optimizar los recursos del servidor.
* **Resiliencia ante Micro-cortes:** Si la petición fetch al endpoint de estado falla por caídas intermitentes de red, el bloque `catch` absorbe el error e instruye al ciclo a continuar (`continue`) en la siguiente iteración, evitando el cierre inesperado del proceso de cobro.

<img width="685" height="723" alt="image" src="https://github.com/user-attachments/assets/878adde5-ce18-4619-a25d-7c541eb2f5fe" />

### 🔀 4. Árbol de Decisión y Resolución de Estados
* **Archivo de Origen:** `\public\js\pos.js` (Línea 551)
* **Normalización de Datos:** Se transforman los estados financieros recibidos a mayúsculas estrictas para evitar conflictos semánticos causados por la sensibilidad a mayúsculas y minúsculas (*Case Sensitivity*).
* **Lógica del Semáforo:**
  * **En espera:** Si la respuesta arroja estados transitorios (`OPEN`, `PROCESSING`), el ciclo mantiene la pantalla bloqueada y avanza a la siguiente consulta.
  * **Aprobado:** El sistema liquida la venta e invoca el almacenamiento local (`finalizarVentaLocal`) **únicamente** cuando se confirma la doble validación positiva: dispositivo en `FINISHED` y transacción bancaria en `APPROVED`.
  * **Rechazado:** Cualquier código de fallo terminal (`CANCELED`, `REJECTED`, `ERROR`) detiene el flujo, lanza una excepción controlada y despliega la alerta correspondiente en la caja.

<img width="611" height="625" alt="image" src="https://github.com/user-attachments/assets/14234229-b32d-479a-a776-2d5ed2a94c53" />

### 🛡️ 5. Enrutamiento Protegido
* **Archivo de Origen:** `routes/web.php`
* **Capa de Autenticación:** Las rutas encargadas de interactuar de forma directa con el hardware están protegidas bajo el grupo de middleware `['auth']`. Esto restringe de forma absoluta que usuarios no autenticados puedan inyectar cobros fantasmas o consultar estados del hardware mediante URLs directas.

<img width="596" height="126" alt="image" src="https://github.com/user-attachments/assets/e6d4cb6c-191c-4c34-a695-62a23bb85c4e" />
<img width="788" height="228" alt="image" src="https://github.com/user-attachments/assets/2d39fa0b-7fbc-4784-81c0-061e5720ca35" />

### 🖥️ 6. Autenticación de Hardware y Creación de Intención
* **Archivo de Origen:** `TerminalController.php`
* **Inyección de Dependencias Segura:** El constructor de la clase recupera de forma interna las llaves criptográficas y el identificador de la máquina directamente del archivo `.env`, eliminando por completo las malas prácticas de codificar llaves maestras en formato *hardcoded* dentro del código fuente.
* **Formateo de Payload:** La API de Mercado Pago Point procesa montos fraccionados. El backend realiza de forma automática la multiplicación y conversión a entero (`$request->total * 100`) para asegurar la correcta equivalencia matemática en centavos.
* **Trazabilidad de Referencias:** Cada transacción adjunta una cadena compuesta por el prefijo institucional `BK-` concatenado a un Timestamp Unix en el campo de referencia externa, facilitando la auditoría financiera directa en el dashboard general de Mercado Pago.

<img width="784" height="778" alt="image" src="https://github.com/user-attachments/assets/fc7350df-1e7d-46ed-91e4-501dc0586870" />

### ⚖️ 7. Regla de Reconciliación de Estados
* **Archivo de Origen:** `TerminalController.php`
* **Seguridad Lógica Financiera:** Actúa como un mecanismo de mitigación de errores físicos. Si la API de Mercado Pago reporta que la transferencia monetaria fue procesada con éxito (`$estadoPago === 'approved'`), pero la terminal Point física se queda congelada o experimenta un atasco de papel, el backend sobreescribe el estado imponiendo de forma forzada el valor `FINISHED` en el `$estadoIntent`. Esto prioriza la realidad del flujo financiero sobre los fallos mecánicos del hardware periférico.

<img width="831" height="855" alt="image" src="https://github.com/user-attachments/assets/57dead9a-46b2-4233-af9c-9dccd9de26c8" />

### 💾 8. Persistencia y Trazabilidad de Venta
* **Archivo de Origen:** `SalesController.php`
* **Consolidación de Transacciones:** Tras la doble aprobación del cobro, se dispara el método `store` para la persistencia de datos en el sistema.
* **Auditoría Interna de Operadores:** Se asocia el `Auth::id()` del usuario en sesión activa al payload de la base de datos (`$datosVenta`), asegurando un registro de auditoría estricto sobre qué cajero procesó cada transacción específica.
* **Optimización de Respuesta:** Al despachar la respuesta JSON hacia el Frontend, se realiza una mutación que inyecta explícitamente la propiedad `nombre_vendedor`. Esto provee de manera inmediata a la vista de los datos requeridos para la impresión del ticket físico de compra sin necesidad de generar llamadas asíncronas secundarias a las tablas de usuarios.

<img width="781" height="495" alt="image" src="https://github.com/user-attachments/assets/dbad32c8-9825-485d-b646-514cf9025e21" />
