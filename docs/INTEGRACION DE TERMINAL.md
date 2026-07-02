# Integración de Terminal Física Mercado Pago

Este documento detalla el flujo de integración, configuración de credenciales y la arquitectura lógica implementada en el sistema para conectar los puntos de venta con las terminales físicas de Mercado Pago.

<br>

## 1. Obtención de Credenciales

Las credenciales de Mercado Pago están directamente vinculadas a la aplicación que se genera a través del apartado corporativo de integraciones.

### Pasos para obtener las credenciales:

1. Ingrese a [Mercado Pago Developers](https://www.mercadopago.com.mx/developers/es). En la esquina superior derecha, haga clic en **Ingresar** e inicie sesión con su cuenta. Posteriormente, acceda al apartado de **Tus integraciones**.

<p align="center">
  <img width="500" alt="image" src="https://github.com/user-attachments/assets/30e6d650-d52a-4771-9aa0-1bd288f87188" />
</p>

2. Seleccione una aplicación existente o cree una nueva interfaz si aún no lo ha hecho.

<p align="center">
  <img width="500" alt="image" src="https://github.com/user-attachments/assets/a266389d-b9f9-47d0-8e06-08cd66b2bb6d" />
</p>

3. Asigne el título correspondiente a la aplicación y haga clic en el botón de **Continuar**.

<p align="center">
  <img width="450" alt="image" src="https://github.com/user-attachments/assets/6384ff7d-6426-44ab-ab2d-4e186c8cc76b" />
</p>

4. En el tipo de solución, elija estrictamente las opciones de **"Pagos presenciales"** y **"Con un desarrollo propio"**.

<p align="center">
  <img width="450" alt="image" src="https://github.com/user-attachments/assets/868715f1-6f70-4206-8161-4d302ebe3ae0" />
</p>

<p align="center">
  <img width="500" alt="image" src="https://github.com/user-attachments/assets/e61f6633-f8ec-4f75-9568-348226c1f5d1" />
</p>

5. Verifique la declaración de términos de uso y haga clic en el botón de **Confirmar**.

<p align="center">
  <img width="500" alt="image" src="https://github.com/user-attachments/assets/9915d285-8b76-47ef-9e8c-870fcf926eae" />
</p>

6. Una vez creada la aplicación de forma exitosa, el sistema desplegará el panel principal de control. Para obtener los tokens de producción, haga clic en el apartado de **Credenciales productivas**.

<p align="center">
  <img width="400" alt="image" src="https://github.com/user-attachments/assets/22454184-1834-439a-83d5-ddfce89b1b41" />
</p>

7. Haga clic en **Activar credenciales**. Complete el formulario de datos comerciales e información requerida por la plataforma. Al finalizar, confirme mediante el botón **Activar credenciales de producción**.

<p align="center">
  <img width="500" alt="image" src="https://github.com/user-attachments/assets/b6d110a4-dbf1-4db5-b705-1fc47fc4803e" />
</p>

8. Copie y resguarde los valores generados bajo los campos **Access Token** y **Client Secret**. Serán indispensables en los archivos de configuración del entorno del servidor.

<p align="center">
  <img width="400" alt="image" src="https://github.com/user-attachments/assets/892c7d18-ae42-401f-a992-6d204f773b1e" />
</p>

<br>

## 2. Configuración del Entorno y Hardware

Para entrelazar el software con el dispositivo Point físico, es necesario recuperar los identificadores de red y mapearlos en el backend.

1. **Obtención del Device ID:** Utilice un cliente HTTP como Postman para consultar los dispositivos vinculados y recuperar el identificador único del hardware. El identificador de terminal activo para este entorno es `NEWLAND_N950__N950NCCB05478475`.

2. **Estado del Hardware:** Asegúrese de que la terminal física permanezca encendida, con conexión estable a internet (Wi-Fi/4G) y correctamente emparejada a la cuenta comercial mediante la aplicación móvil de Mercado Pago.

3. **Variables de Entorno:** Diríjase al archivo `.env` ubicado en la raíz del proyecto web.

<p align="center">
  <img width="200" alt="image" src="https://github.com/user-attachments/assets/cd16d27a-1ef4-458b-b28a-1467bf9e0b25" />
</p>

4. Ubique la sección de configuración de pasarelas de pago e ingrese las credenciales productivas obtenidas anteriormente en sus respectivas variables globales.

<br>

## 3. Arquitectura del Código y Reglas de Negocio

A continuación se detalla la lógica técnica distribuida entre el Frontend (JavaScript) y el Backend (PHP/Laravel) que gobierna el comportamiento del punto de venta.

### Interfaz de Usuario y Control de Interrupciones
Archivo origen: `\public\js\pos.js`

El sistema centraliza el manejo de excepciones visuales. La función `mostrarError` integra un mecanismo de sanitización (`startsWith("<")`) que intercepta fallas críticas del servidor (como errores HTTP 500) y los procesa en mensajes legibles, impidiendo que código HTML crudo afecte el renderizado del DOM o colapse la interfaz.

Para la cancelación manual, la función `cancelarTerminal` interrumpe de forma controlada la ejecución asíncrona. Al mutar el estado de `this.pollingActive` a `false`, se rompe el bucle de escucha hacia la terminal, liberando el hilo principal del punto de venta si la operación es cancelada por el operador.

<p align="center">
  <img width="350" alt="image" src="https://github.com/user-attachments/assets/b0b53ae1-1b62-4df4-a302-2bbae75a7f77" />
</p>

### Inicio de Transacción y Petición de Cobro
Archivo origen: `\public\js\pos.js`

Antes de consumir recursos de red, el script frontend valida localmente que el importe total cumpla las políticas comerciales de la pasarela (monto mínimo de $5.00 MXN), optimizando el rendimiento de la API.

Al inicializar la transacción, se activan de manera síncrona los indicadores booleanos `esperandoTerminal` para bloquear el panel del cajero y `pollingActive` para autorizar la escucha activa. El proceso extrae dinámicamente el token CSRF del DOM para firmar la petición asíncrona (`fetch`) enviada al endpoint `/terminal/cobrar`, asegurando la integridad de la solicitud ante vulnerabilidades cruzadas.

<p align="center">
  <img width="450" alt="image" src="https://github.com/user-attachments/assets/e3ab3275-f24f-410f-abfe-6ad4ab268992" />
</p>

### Ciclo de Escucha Activa ("Polling")
Archivo origen: `\public\js\pos.js`

El sistema captura y almacena de forma persistente el ticket único (`intentId`) devuelto por la API para mantener la trazabilidad de la operación actual en segundo plano.

Se despliega un ciclo `while` limitado a un máximo de 60 iteraciones (equivalente a un minuto de tolerancia). Mediante una promesa combinada con `setTimeout`, se fuerza un retraso síncrono de 1000ms entre peticiones, regulando la carga sobre el servidor. Si la consulta al endpoint falla debido a inestabilidad temporal de la red, el bloque `catch` absorbe el error e instruye al ciclo a continuar (`continue`) en la siguiente iteración, garantizando la resiliencia del proceso de cobro.

<p align="center">
  <img width="400" alt="image" src="https://github.com/user-attachments/assets/878adde5-ce18-4619-a25d-7c541eb2f5fe" />
</p>

### Árbol de Decisión y Resolución de Estados
Archivo origen: `\public\js\pos.js`

El sistema transforma los estados financieros recibidos a letras mayúsculas estrictas para mitigar errores de ejecución causados por la sensibilidad a mayúsculas y minúsculas (*Case Sensitivity*).

Ante estados transitorios como `OPEN` o `PROCESSING`, la rutina mantiene el bloqueo visual y avanza a la siguiente consulta. El sistema procesa el guardado en base de datos (`finalizarVentaLocal`) únicamente al confirmar la doble validación positiva: estado del dispositivo en `FINISHED` y estado transaccional bancario en `APPROVED`. Cualquier código de terminación anómala (`CANCELED`, `REJECTED`, `ERROR`) detiene el flujo, lanza una excepción controlada y despliega la alerta correspondiente en la caja.

<p align="center">
  <img width="350" alt="image" src="https://github.com/user-attachments/assets/14234229-b32d-479a-a776-2d5ed2a94c53" />
</p>

### Enrutamiento Protegido
Archivo origen: `routes/web.php`

Las rutas encargadas de interactuar de forma directa con el hardware se declaran dentro de un grupo restringido por el middleware `auth`. Esto garantiza que únicamente el personal con una sesión activa y válida en el sistema pueda inicializar cobros o consultar estados del hardware, previniendo accesos no autorizados mediante inyección externa a las URLs.

<p align="center">
  <img width="350" alt="image" src="https://github.com/user-attachments/assets/e6d4cb6c-191c-4c34-a695-62a23bb85c4e" />
</p>

<p align="center">
  <img width="450" alt="image" src="https://github.com/user-attachments/assets/2d39fa0b-7fbc-4784-81c0-061e5720ca35" />
</p>

### Autenticación de Hardware y Creación de Intención
Archivo origen: `TerminalController.php`

El constructor de la clase recupera las credenciales criptográficas y el identificador de la máquina directamente de las variables de entorno del servidor (`.env`), eliminando malas prácticas de exposición de llaves maestras dentro del código fuente.

La API de Point procesa importes fraccionados, por lo que el método formatea el monto multiplicándolo por 100 y realizando un *cast* a entero (`$request->total * 100`) para asegurar la equivalencia en centavos. Para la conciliación, se genera una referencia externa única concatenando el prefijo institucional `BK-` con un *Timestamp Unix*, facilitando la auditoría financiera directa en el panel general de Mercado Pago.

<p align="center">
  <img width="450" alt="image" src="https://github.com/user-attachments/assets/fc7350df-1e7d-46ed-91e4-501dc0586870" />
</p>

### Regla de Reconciliación de Estados
Archivo origen: `TerminalController.php`

Mecanismo de mitigación ante fallos de hardware en producción. Si la consulta confirma el abono monetario exitoso (`$estadoPago === 'approved'`), pero la terminal no emite el estado de finalización debido a un error mecánico (como atasco o falta de papel), el backend fuerza la mutación del estado a `FINISHED`. Esto implementa una regla de negocio que prioriza la confirmación transaccional financiera sobre el estado periférico del dispositivo físico.

<p align="center">
  <img width="450" alt="image" src="https://github.com/user-attachments/assets/57dead9a-46b2-4233-af9c-9dccd9de26c8" />
</p>

### Persistencia y Trazabilidad de Venta
Archivo origen: `SalesController.php`

Tras la aprobación mutua del cobro entre la API y el dispositivo, se ejecuta el método `store` para la persistencia de datos en el sistema. El registro de la venta vincula el `Auth::id()` del operador en sesión activa, manteniendo la trazabilidad estricta del usuario que ejecutó la transacción para auditorías internas.

Al retornar la respuesta JSON hacia el frontend, se realiza una mutación que anexa la propiedad `nombre_vendedor`. Esto provee de manera inmediata a la vista los datos exactos requeridos para la impresión del ticket físico de compra sin necesidad de generar llamadas asíncronas secundarias a las tablas de usuarios.

<p align="center">
  <img width="450" alt="image" src="https://github.com/user-attachments/assets/dbad32c8-9825-485d-b646-514cf9025e21" />
</p>
