# Integración de Terminal Física Mercado Pago

Este documento detalla el flujo de integración, configuración de credenciales y la arquitectura lógica implementada en el sistema para conectar los puntos de venta con las terminales físicas de Mercado Pago.

---

## 1. Obtención de Credenciales

Las credenciales de Mercado Pago están directamente vinculadas a la aplicación que se genera a través del apartado corporativo de integraciones.

### Pasos para obtener las credenciales:

1. Ingrese a [Mercado Pago Developers](https://www.mercadopago.com.mx/developers/es). En la esquina superior derecha, haga clic en **Ingresar** e inicie sesión con su cuenta. Posteriormente, acceda al apartado de **Tus integraciones**.

<img width="500" alt="image" src="https://github.com/user-attachments/assets/30e6d650-d52a-4771-9aa0-1bd288f87188" />

2. Seleccione una aplicación existente o cree una nueva interfaz si aún no lo ha hecho.

<img width="500" alt="image" src="https://github.com/user-attachments/assets/a266389d-b9f9-47d0-8e06-08cd66b2bb6d" />

* **2.1.** Asigne el título correspondiente a la aplicación y haga clic en el botón de **Continuar**.

<img width="450" alt="image" src="https://github.com/user-attachments/assets/6384ff7d-6426-44ab-ab2d-4e186c8cc76b" />

* **2.2.** En el tipo de solución, elija estrictamente las opciones de **"Pagos presenciales"** y **"Con un desarrollo propio"**.

<img width="450" alt="image" src="https://github.com/user-attachments/assets/868715f1-6f70-4206-8161-4d302ebe3ae0" />
<img width="500" alt="image" src="https://github.com/user-attachments/assets/e61f6633-f8ec-4f75-9568-348226c1f5d1" />

* **2.3.** Verifique la declaración de términos de uso y haga clic en el botón de **Confirmar**.

<img width="500" alt="image" src="https://github.com/user-attachments/assets/9915d285-8b76-47ef-9e8c-870fcf926eae" />

3. Una vez creada la aplicación de forma exitosa, el sistema desplegará el panel principal de control. Para obtener los tokens de producción, haga clic en el apartado de **Credenciales productivas**.

<img width="400" alt="image" src="https://github.com/user-attachments/assets/22454184-1834-439a-83d5-ddfce89b1b41" />

* **3.1.** Haga clic en **Activar credenciales**. Complete el formulario de datos comerciales e información requerida por la plataforma. Al finalizar, confirme mediante el botón **Activar credenciales de producción**.

<img width="500" alt="image" src="https://github.com/user-attachments/assets/b6d110a4-dbf1-4db5-b705-1fc47fc4803e" />

4. Copie y resguarde los valores generados bajo los campos **Access Token** y **Client Secret**. Serán indispensables en los archivos de configuración del entorno del servidor.

<img width="400" alt="image" src="https://github.com/user-attachments/assets/892c7d18-ae42-401f-a992-6d204f773b1e" />

---

## 2. Configuración del Entorno y Hardware

Para entrelazar el software con el dispositivo Point físico, es necesario recuperar los identificadores de red y mapearlos en el backend.

1. **Obtención del Device ID:** Utilice un cliente HTTP como Postman para consultar los dispositivos vinculados y recuperar el identificador único del hardware.
   * **Identificador de terminal activo:** `NEWLAND_N950__N950NCCB05478475`
2. **Estado del Hardware:** Asegúrese de que la terminal física permanezca encendida, con conexión estable a internet (Wi-Fi/4G) y correctamente emparejada a la cuenta comercial mediante la aplicación móvil de Mercado Pago.
3. **Variables de Entorno:** Diríjase al archivo `.env` ubicado en la raíz del proyecto.

<img width="200" alt="image" src="https://github.com/user-attachments/assets/cd16d27a-1ef4-458b-b28a-1467bf9e0b25" />

4. Ubique la sección de configuración de pasarelas de pago e ingrese las credenciales productivas obtenidas anteriormente en sus respectivas variables.

---

## 3. Arquitectura del Código y Reglas de Negocio

A continuación, se detalla la lógica técnica distribuida entre el Frontend (JavaScript) y el Backend (PHP/Laravel) que gobierna el comportamiento del punto de venta.

### 3.1. Interfaz de Usuario y Control de Interrupciones
* **Archivo:** `\public\js\pos.js`
* **Gestión de UI:** Centraliza el manejo de excepciones visuales. La función `mostrarError` integra un mecanismo de sanitización (`startsWith("<")`) que intercepta fallas del servidor (ej. errores HTTP 500) y los procesa en mensajes legibles, impidiendo que código crudo afecte el renderizado del DOM.
* **Kill Switch:** La función `cancelarTerminal` interrumpe de forma controlada la ejecución. Al mutar el estado de `this.pollingActive` a `false`, se aborta el bucle de escucha asíncrona, liberando el hilo principal si la operación es cancelada por el operador.

<img width="350" alt="image" src="https://github.com/user-attachments/assets/b0b53ae1-1b62-4df4-a302-2bbae75a7f77" />

### 3.2. Inicio de Transacción y Petición de Cobro
* **Archivo:** `\public\js\pos.js`
* **Validación en Frontend:** El script valida que el importe total cumpla las políticas comerciales de la pasarela (monto mínimo de $5.00 MXN) de manera local, optimizando el consumo de la API.
* **Control de Estados:** Se activan de manera síncrona los indicadores booleanos `esperandoTerminal` (bloqueo de UI) y `pollingActive` (apertura de socket/escucha).
* **Seguridad (CSRF):** El proceso extrae dinámicamente el token CSRF del DOM para firmar la petición asíncrona (`fetch`) enviada al endpoint `/terminal/cobrar`, asegurando la integridad de la solicitud.

<img width="450" alt="image" src="https://github.com/user-attachments/assets/e3ab3275-f24f-410f-abfe-6ad4ab268992" />

### 3.3. Ciclo de Escucha Activa ("Polling")
* **Archivo:** `\public\js\pos.js`
* **Identificador de Transacción:** El sistema almacena de forma persistente el `intentId` devuelto por la API para garantizar la trazabilidad del proceso en curso.
* **Estrategia de Consulta:** Se ejecuta un ciclo `while` limitado a 60 iteraciones (1 minuto de *timeout*). Mediante una promesa combinada con `setTimeout`, se establece un *delay* de 1000ms entre consultas para mitigar la sobrecarga del servidor.
* **Tolerancia a Fallos:** Si la petición al endpoint de estado falla por inestabilidad de red, el bloque `catch` captura la excepción e instruye al ciclo a continuar en la siguiente iteración, garantizando la resiliencia del módulo.

<img width="400" alt="image" src="https://github.com/user-attachments/assets/878adde5-ce18-4619-a25d-7c541eb2f5fe" />

### 3.4. Árbol de Decisión y Resolución de Estados
* **Archivo:** `\public\js\pos.js`
* **Normalización:** Se transforman los estados financieros a mayúsculas estrictas para mitigar errores de *Case Sensitivity*.
* **Flujo Condicional:**
  * **Transitorio:** Ante estados como `OPEN` o `PROCESSING`, la rutina continúa el *polling*.
  * **Aprobado:** El sistema liquida la venta mediante `finalizarVentaLocal` estrictamente tras una validación dual: estado del dispositivo en `FINISHED` y estado financiero en `APPROVED`.
  * **Rechazado:** Cualquier código de terminación anómala (`CANCELED`, `REJECTED`, `ERROR`) interrumpe el flujo y emite una excepción controlada a la capa de presentación.

<img width="350" alt="image" src="https://github.com/user-attachments/assets/14234229-b32d-479a-a776-2d5ed2a94c53" />

### 3.5. Enrutamiento Protegido
* **Archivo:** `routes/web.php`
* **Middleware:** Las rutas que interactúan con el hardware están protegidas bajo el middleware `auth`. Esto restringe el acceso al controlador de pagos exclusivamente a sesiones autenticadas válidas, previniendo inyección de solicitudes mediante endpoints públicos.

<img width="350" alt="image" src="https://github.com/user-attachments/assets/e6d4cb6c-191c-4c34-a695-62a23bb85c4e" />
<img width="450" alt="image" src="https://github.com/user-attachments/assets/2d39fa0b-7fbc-4784-81c0-061e5720ca35" />

### 3.6. Autenticación de Hardware y Creación de Intención
* **Archivo:** `TerminalController.php`
* **Inyección Segura:** El controlador recupera las credenciales criptográficas a través de variables de entorno, evitando la exposición de claves en el repositorio de código.
* **Parseo de Payload:** La API requiere el manejo de importes fraccionados. El método formatea el monto multiplicándolo por 100 y realizando un *cast* a entero (`$request->total * 100`).
* **Trazabilidad:** Se genera una referencia externa concatenando el prefijo `BK-` con un *Timestamp Unix*, estableciendo un índice de conciliación directo entre la base de datos local y el dashboard de Mercado Pago.

<img width="450" alt="image" src="https://github.com/user-attachments/assets/fc7350df-1e7d-46ed-91e4-501dc0586870" />

### 3.7. Regla de Reconciliación de Estados
* **Archivo:** `TerminalController.php`
* **Validación de Integridad:** Si el webhook o consulta de la API confirma el abono (`$estadoPago === 'approved'`), pero la terminal no emite el estado de finalización (ej. error mecánico por falta de papel), el backend fuerza la mutación de la variable a `FINISHED`. Esto garantiza que la lógica de negocio priorice la confirmación transaccional bancaria sobre el estado mecánico del periférico.

<img width="450" alt="image" src="https://github.com/user-attachments/assets/57dead9a-46b2-4233-af9c-9dccd9de26c8" />

### 3.8. Persistencia y Trazabilidad de Venta
* **Archivo:** `SalesController.php`
* **Consolidación:** Al validarse el pago, se invoca el método `store` para registrar la operación en la base de datos.
* **Auditoría:** El registro de la venta vincula el `Auth::id()` del operador en sesión, manteniendo la trazabilidad del usuario que ejecutó la transacción.
* **Mutación de Respuesta:** El controlador anexa la propiedad `nombre_vendedor` en la respuesta JSON. Esto provee al frontend los datos estructurados necesarios para la generación e impresión del recibo sin requerir peticiones HTTP adicionales.

<img width="450" alt="image" src="https://github.com/user-attachments/assets/dbad32c8-9825-485d-b646-514cf9025e21" />
