# Configuración de Correos y Flujo de Aprobación de Usuarios

Este documento detalla la configuración del servidor de correos, la estructura de las plantillas de notificación y la arquitectura lógica implementada en el Punto de Venta para el registro, control de acceso y validación de nuevas cuentas de personal.

<br>

## 1. Configuración de Servidor de Correo (SMTP)

El sistema utiliza la infraestructura de **Titan Email** para procesar y enviar de forma segura las alertas de seguridad y moderación al administrador.

### Variables de Entorno:

1. Diríjase al archivo `.env` ubicado en la raíz del proyecto web.
2. Ingrese las credenciales del servidor SMTP institucional. La estructura de las variables globales de correo debe definirse de la siguiente manera:

```env
# ==========================================
# CONFIGURACIÓN DE NOTIFICACIONES MAIL (SMTP)
# ==========================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.titan.email
MAIL_PORT=587
MAIL_USERNAME="tu-correo@tu-dominio.com.mx"
MAIL_PASSWORD="contraseña_segura_de_aplicacion"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="notificaciones@tu-dominio.com.mx"
MAIL_FROM_NAME="BunnyKlin"
```


<br>

## 2. Estructura de las Plantillas de Vista (Blade)

La capa visual de las notificaciones utiliza el motor de plantillas Blade de Laravel para estructurar y presentar la información de los usuarios antes de mandarla por correo.

### Diseño de Correo de Notificación (`aprobacion-cuenta.blade.php`)

* **Ubicación del archivo:** `resources/views/emails/aprobacion-cuenta.blade.php`
* **Contenido:** Despliega una tarjeta HTML limpia y profesional que muestra de forma ordenada los datos que el usuario ingresó en el formulario de registro:
  * **Nombre:** `{{ $user->name }}`
  * **Email:** `{{ $user->email }}`
  * **Rol asignado:** `{{ $user->role }}`
* **Botones de acción:** El diseño incluye dos botones de acción directa incrustados en el cuerpo del mensaje. Estos botones adjuntan el valor del `$token` único de control y apuntan a sus respectivas URLs de validación en el servidor:
  * **Permitir (Activar):** Enlace en color verde hacia la ruta `cuenta.aprobar` para dar de alta al empleado.
  * **Cancelar (Eliminar):** Enlace en color rojo hacia la ruta `cuenta.rechazar` para descartar el registro.

<br>

## 3. Seguridad en Rutas y Middleware de Acceso

Las URLs encargadas de alterar los estados de los usuarios en el backend están protegidas mediante dos filtros de seguridad en el archivo `routes/web.php` para impedir inyecciones o accesos no autorizados.

### 3.1. Protección de Sesión Activa (`auth`)
Todas las rutas de administración están encapsuladas dentro del grupo afectado por el middleware `auth`. Si quien invoca la URL no posee una sesión válida e iniciada en el Punto de Venta, la petición es rechazada de inmediato.

### 3.2. Filtro de Rol Operativo (`AdminMiddleware`)
* **Ubicación del archivo:** `app/Http/Middleware/AdminMiddleware.php`
* **Lógica del Filtro:** Antes de ceder el control, el código evalúa si el usuario en sesión cuenta con el rol de administración:

```php
if (!auth()->check() || !auth()->user()->isAdmin()) {
    abort(403, 'Acceso denegado. Solo los administradores pueden realizar esta acción.');
}
```

Si la condición falla, el sistema interrumpe la petición web y retorna un error `403 Forbidden` (Acceso denegado).

### 3.3. Declaración de Endpoints del Módulo de Personal
Estas son las rutas declaradas en `web.php` para el control de los operadores y la respuesta de los botones de los correos electrónicos:

```php
Route::get('/personal', function () {
    $empleados = User::query()->latest()->get();
    return view('pages.personal', ['title' => 'Gestión de Personal', 'empleados' => $empleados]);
})->name('personal');

Route::post('/personal/guardar', [EmpleadoController::class, 'store'])->name('personal.store');
Route::delete('/personal/eliminar/{id}', [EmpleadoController::class, 'eliminarPorId'])->name('personal.eliminar_id');
Route::get('/aprobar-cuenta/{token}', [EmpleadoController::class, 'aprobar'])->name('cuenta.aprobar');
Route::get('/rechazar-cuenta/{token}', [EmpleadoController::class, 'rechazar'])->name('cuenta.rechazar');
```

<br>

## 4. Arquitectura de Código y Reglas de Negocio (`EmpleadoController`)

La lógica y el control de los accesos están gobernados por la clase `EmpleadoController.php`.

### 4.1. Registro y Envío de Solicitud (`store`)
Para evitar que queden cuentas rotas si ocurre un fallo de red durante el envío del correo, el registro se ejecuta dentro de una transacción de base de datos (`DB::beginTransaction()`).

```php
public function store(Request $request){
    $request->validate([
        'nombre' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'rol' => 'required',
        'password' => 'required|min:6'
    ]);

    try {
        DB::beginTransaction();

        $token = Str::random(60);

        $empleado = User::create([
            'name' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->rol,
            'status' => 'pendiente', 
            'confirmation_token' => $token
        ]);

        Mail::to('[CORREO_ADMINISTRADOR]')->send(new AprobacionCuentaMailable($empleado, $token));

        DB::commit();

        return redirect()->back()->with('success', '¡Solicitud enviada! La cuenta está pendiente de aprobación.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'No se pudo procesar la solicitud. Revisa la conexión al correo.')->withInput();
    }
}
```
* **Funcionamiento:** Se validan los datos mínimos del formulario. El sistema genera una cadena aleatoria de 60 caracteres mediante `Str::random(60)` para usarla como token de verificación. El registro se guarda con el campo `status` en `'pendiente'` de forma predeterminada y se intenta enviar el correo. Si el correo se envía correctamente, se confirman los cambios con `DB::commit()`. De lo contrario, se dispara un `DB::rollBack()` para limpiar el registro fallido de la base de datos de inmediato.

### 4.2. Eliminación de Cuentas por ID (`eliminarPorId`)
Permite remover la fila de un usuario activo de manera directa utilizando su número identificador.

```php
public function eliminarPorId($id){
    $usuario = User::findOrFail($id);
    $nombre = $usuario->name;
    $usuario->delete();
    
    return redirect()->back()->with('success', 'Has eliminado la cuenta de '.$nombre.' exitosamente.');
}
```
* **Funcionamiento:** El método busca al usuario mediante `User::findOrFail($id)`. Si el identificador no existe en la base de datos, Laravel detiene el proceso mostrando una pantalla de error 404. Si existe, extrae el nombre del registro para el mensaje de confirmación y lo purga de la tabla usando el comando `delete()`.

### 4.3. Validación y Activación de Accesos (`aprobar`)
Este método se ejecuta al presionar el botón de activación en el correo del administrador. Quita las restricciones del usuario para permitirle iniciar sesión.

```php
public function aprobar($token){
    $tokenLimpio = trim($token);
    
    $usuario = User::where('confirmation_token', $tokenLimpio)->first();

    if (!$usuario) {
        return redirect('/personal')->with('error', 'El enlace no es válido o esta solicitud ya fue procesada.');
    }

    $usuario->update([
        'status' => 'activo',
        'confirmation_token' => null, 
        'email_verified_at' => now()
    ]);

    return redirect('/personal')->with('success', '¡Has aprobado la cuenta de '.$usuario->name.' con éxito!');
}
```
* **Funcionamiento:** Limpia los espacios vacíos del token con `trim()` y busca la coincidencia en la tabla de usuarios. Si el token no existe (porque está mal o ya se usó antes), redirige al administrador mostrando un error. Si lo encuentra, actualiza el estado del cajero a `'activo'`, borra la llave de `confirmation_token` escribiéndole `null` para invalidar el link, y registra la fecha de validación en `email_verified_at`.

### 4.4. Rechazo y Purga de Solicitudes (`rechazar`)
Se dispara al presionar el botón rojo de cancelación desde el cuerpo del correo.

```php
public function rechazar($token){
    $tokenLimpio = trim($token);
    
    $usuario = User::where('confirmation_token', $tokenLimpio)->first();

    if (!$usuario) {
        return redirect('/personal')->with('error', 'El enlace no es válido o esta solicitud ya fue procesada.');
    }

    $usuario->delete();
    
    return redirect('/personal')->with('success', 'Has rechazado la solicitud.');
}
```
* **Funcionamiento:** Filtra el token recibido y busca la solicitud pendiente. Si el registro no se encuentra, aborta y regresa a la pantalla de gestión. Si localiza la petición, ejecuta un comando `$usuario->delete()`, eliminando físicamente la fila para no acumular registros no autorizados en la base de datos.
