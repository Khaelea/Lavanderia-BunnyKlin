INTEGRACIÓN DE TERMINAL
MERCADO PAGO

Obtener credenciales<img width="903" height="208" alt="image" src="https://github.com/user-attachments/assets/3843562b-c714-4083-9a7e-48e6376f748c" />


Las credenciales de Mercado Pago son creadas a partir de una aplicación de Mercado Pago. Es decir, están directamente vinculadas a la aplicación que se crea a través del apartado “Tus integraciones”.

A continuación, se conocerá cómo obtener las credenciales.
1.	En la esquina superior derecha de Mercado Pago Developers, haga clic en Ingresar y se tendrá que completar los datos requeridos con la información correspondiente a su cuenta de Mercado Pago. Luego, haga clic en el botón integraciones  ubicado en la esquina superior derecha.

Link: https://www.mercadopago.com.mx/developers/es

![Uploading image.png…]()


 

2.	Acceda a su aplicación o cree una si aún no lo ha hecho.
 


2.1. Se coloca el titulo que desee que tenga la aplicación y después al botón de continuar.

 

2.2.  En esta opción se deberá elegir las opciones de “Pagos presenciales”, y “Con un desarrollo propio”.

 

 

2.3. Una vez verificado que los datos estén en orden, se le da al botón de Confirmar.

 
 

3.	Una vez creada la aplicación, aparecerá esta pantalla:

 
Para obtener las credenciales de producción que necesitamos, se dará clic en el apartado de Productivas.

3.1. Damos clic al botón de Activar credenciales y continuamos. Posteriormente, aparecerá la pestaña de Credenciales de producción en donde se tendrá que completar la información requerida, una vez llenado los campos solicitados, se dará clic al botón de Activar credenciales de producción.
 


4.	Se encontrarán las credenciales bajo el título: Access Token y Client Secret, es importante que se guarden para utilizarlas en un momento más.
 

5.	Ahora descargaremos Postman para obtener el ID de la terminal de Mercado Pago:

5.1.	 

6.	 Identificador de la terminal (Device ID): NEWLAND_N950__N950NCCB05478475

7.	Asegúrese de que la terminal física esté encendida, conectada a internet y vinculada a su cuenta de Mercado Pago a través de la aplicación móvil.

8.	 A continuación, diríjase al archivo .env en la raíz del proyecto de la página.

 


8.1.	 En la línea 59 del archivo .env se encuentran los apartados para ingresar las credenciales anteriores que obtuvimos: Colocamos las credenciales en donde corresponden de acuerdo a los títulos.

 

CODIGO

1. Interfaz de Usuario y Control de Interrupciones (pos.js)
Esta sección maneja la preparación de la interfaz gráfica y los mecanismos de seguridad visual para el cajero.

Ubicación del archivo: \public\js\pos.js (Línea 427)

•	Gestión de UI: Estas funciones centralizan el control de errores en pantalla. mostrarError incluye una regla de sanitización (startsWith("<")) que intercepta fallos críticos del servidor (como respuestas HTML 500) y los traduce a mensajes legibles para el operador, evitando que el sistema colapse visualmente.
•	Cancelación Manual (Kill Switch): La función cancelarTerminal actúa como un interruptor de emergencia. Al cambiar el estado de this.pollingActive a false, el sistema aborta de inmediato cualquier escucha asíncrona hacia la terminal, liberando el punto de venta en caso de que el cliente abandone la compra.
 

2. Inicio de Transacción y Petición de Cobro (pos.js) (Línea: 451)

Reglas de Negocio Frontend: Antes de emitir solicitudes a la red, el sistema valida que el monto cumpla con las políticas de Mercado Pago (mínimo $5.00 MXN), ahorrando consumo de API.
Preparación de Estado: Se activan las banderas esperandoTerminal (bloqueo de pantalla) y pollingActive (autorización de escucha).
Petición Segura: Se extrae el token CSRF dinámico del DOM para firmar la petición asíncrona (fetch) enviada al endpoint /terminal/cobrar, garantizando protección contra vulnerabilidades de falsificación de peticiones web.
 



3. Ciclo de Escucha Activa o "Polling" (pos.js)
Identificador de Intención (intentId): El sistema almacena el ticket devuelto por el servidor para rastrear unívocamente la transacción actual.
Estrategia de Polling: Se despliega un ciclo while limitado a 60 iteraciones (1 minuto). Mediante una promesa con setTimeout, se fuerza un retardo de 1000ms (1 segundo) entre peticiones, optimizando la carga en el servidor.
Tolerancia a Micro-cortes: Si el fetch hacia el endpoint de consulta de estado falla por inestabilidad temporal de la red, el bloque catch local absorbe el error e instruye al ciclo a continuar (continue) en el siguiente segundo, asegurando resiliencia.
 


4.	Árbol de Decisión y Resolución de Estados (pos.js) (Línea 551)
      Evaluación de la respuesta de los servidores financieros.

•	Normalización de Datos: Convierte los estados devueltos a mayúsculas para evitar fallos por sensibilidad a caracteres (Case Sensitivity).
•	Semáforo de Lógica:
•	Espera: Si la terminal responde con un estado transitorio (OPEN, PROCESSING), el ciclo avanza a la siguiente consulta.
•	Aprobación: Solo cuando ambos indicadores, el del dispositivo (FINISHED) y el del banco (APPROVED), son afirmativos, se invoca la función de guardado en base de datos (finalizarVentaLocal).
•	Rechazo: Se mapean los códigos técnicos de error (CANCELED, REJECTED, ERROR) a cadenas de texto amigables para notificar al cajero mediante el lanzamiento estructurado de una excepción.
 


5.	Enrutamiento Protegido (web.php)

Ubicación:

Seguridad de Acceso: Los endpoints que activan el hardware se declaran dentro del grupo middleware(['auth']). Esto garantiza que únicamente el personal con sesión activa en el sistema pueda inicializar cobros o consultar estados, previniendo accesos no autorizados mediante peticiones directas a la URL.
 
 

6. Autenticación de hardware y creación de intención (TerminalController.php)
Lógica de servidor para interactuar con la API de Mercado Pago.

•	Inyección Segura: El constructor recupera las credenciales criptográficas y el identificador de la máquina directamente de las variables de entorno del servidor (.env), eliminando el riesgo de fuga de datos en el código fuente.




•	Formateo de Payload: La API de Point requiere que el dinero se envíe fraccionado. El monto se multiplica y se moldea a entero ($request->total * 100).

•	Trazabilidad: Se inyecta el prefijo BK- concatenado a un Timestamp Unix en la referencia externa para permitir la conciliación de ventas con el panel de Mercado Pago.

 






7. Regla de Reconciliación de Estados (TerminalController.php)

Regla de Negocio de Reconciliación: 
Implementa un parche de seguridad lógica. Si la respuesta de la API indica que la transferencia bancaria fue aprobada ($estadoPago === 'approved'), pero la terminal reporta un estado distinto (por ejemplo, por atasco de papel), el controlador impone el valor FINISHED en el $estadoIntent. Esto asegura que el frontend asuma la venta como exitosa y liquide la transacción, priorizando la respuesta financiera sobre las fallas periféricas.
 

8. Persistencia y Trazabilidad de Venta (SalesController.php)
•	Consolidación de la Transacción: Una vez que la terminal y Mercado Pago aprueban el cobro, se ejecuta el método store.
•	Auditoría Interna: Se inyecta el Auth::id() del usuario en sesión a la carga útil ($datosVenta), garantizando que exista un responsable rastreable por cada venta procesada.
•	Preparación de Ticket: Al retornar el JSON al frontend, se muta la respuesta inyectando explícitamente el nombre_vendedor, lo cual provee a la vista de los datos exactos requeridos para generar el ticket físico o digital sin necesidad de consultas asíncronas adicionales a la base de usuarios.
 
