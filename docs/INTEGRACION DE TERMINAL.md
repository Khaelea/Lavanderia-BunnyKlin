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

4. **Declaración de Variables:** Ubique la sección de pasarelas de pago dentro de su archivo `.env` (aproximadamente en la línea 59) e ingrese las credenciales productivas obtenidas en sus respectivas llaves. La estructura de las variables globales debe definirse de la siguiente manera:

```env

MERCADOPAGO_ACCESS_TOKEN=APP_USR-XXXXXXXXXXXXXXXXXXXXXX
MERCADOPAGO_POINT_DEVICE_ID=NEWLAND_NXXXXXXXXXXXXXXXXXXXXXX
MERCADOPAGO_PUBLIC_KEY=APP_USR-XXXXXXXXXXXXXXXXXXXXXXXXXXXX

