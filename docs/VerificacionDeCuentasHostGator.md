# Configuración de Correos y Flujo de Aprobación de Usuarios

Este documento detalla la configuración del servidor de correos, la estructura de las plantillas de notificación y la arquitectura lógica implementada en el Punto de Venta para el registro, control de acceso y validación de nuevas cuentas de personal.

---

## 1. Configuración de Servidor de Correo (SMTP)

El sistema utiliza la infraestructura de **Titan Email** para procesar y enviar de forma segura las alertas de seguridad y moderación al administrador.

### Variables de Entorno

Diríjase al archivo `.env` ubicado en la raíz del proyecto web e ingrese las credenciales del servidor SMTP institucional. La estructura de las variables globales de correo debe definirse de la siguiente manera:

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
MAIL_FROM_ADDRESS="xxxxxxxxxxxxs@tu-xxxxx.com.mx"
MAIL_FROM_NAME="xxxxx"
```

> **Nota:** Este archivo por razones de seguridad no está al descargar el proyecto.

---

## 2. Estructura de las Plantillas de Vista (Blade)

La capa visual de las notificaciones utiliza el motor de plantillas Blade de Laravel para estructurar y presentar la información de los usuarios antes del envío.

### Diseño de Correo de Notificación (`aprobacion-cuenta.blade.php`)

* **Ubicación:** `resources/views/emails/aprobacion-cuenta.blade.php`
* **Estructura de Datos:** Despliega una tarjeta HTML estructurada que muestra los datos capturados en el formulario de registro:
  * **Nombre:** `{{ $user->name }}`
  * **Email:** `{{ $user->email }}`
  * **Rol asignado:** `{{ $user->role }}`

**Manejo de Acciones (CTA):**
El diseño integra dos botones de acción directa en el cuerpo del mensaje. Ambos adjuntan el valor del `$token` único de control y apuntan a sus respectivas URLs de resolución:
* **Permitir (Activar):** Enlace hacia la ruta `cuenta.aprobar` para dar de alta al empleado.
* **Cancelar (Eliminar):** Enlace hacia la ruta `cuenta.rechazar` para descartar el registro.

---

## 3. Seguridad en Rutas y Middleware de Acceso

Las URLs encargadas de alterar los estados de los usuarios en el backend están protegidas mediante dos filtros de seguridad en el archivo `routes/web.php` para mitigar accesos no autorizados.

### 3.1. Protección de Sesión Activa (`auth`)
Todas las rutas de administración están encapsuladas dentro del grupo afectado por el middleware `auth`. Si el cliente no posee una sesión válida e iniciada, la petición es rechazada en la capa de enrutamiento.

### 3.2. Filtro de Rol Operativo (`AdminMiddleware`)
* **Ubicación:** `app/Http/Middleware/AdminMiddleware.php`

Antes de ceder el control al controlador, el código evalúa si el usuario en sesión cuenta con privilegios de administración:

```php
if (!auth()->check() || !auth()->user()->isAdmin()) {
    abort(403, 'Acceso denegado. Solo los administradores pueden realizar esta acción.');
}
```
Si la aserción falla, el kernel interrumpe la petición web y retorna un código de estado HTTP `403 Forbidden`.

### 3.3. Endpoints del Módulo de Personal
Rutas declaradas para la gestión de operadores y la interceptación de respuestas del correo electrónico:

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

---

## 4. Arquitectura de Código y Reglas de Negocio

La lógica de persistencia y el control de acceso se centralizan en la clase `EmpleadoController.php`.

### 4.1. Registro y Envío de Solicitud (`store`)
Para preservar la integridad de la base de datos ante posibles interrupciones del servicio SMTP, el registro se encapsula en una transacción (`DB::beginTransaction()`).

```php
public function store(Request $request) {
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
**Flujo de ejecución:**
1. Validación estricta de las entradas del formulario.
2. Generación criptográfica de un token de 60 caracteres (`Str::random(60)`).
3. Persistencia del modelo `User` asginando el estado inicial `'pendiente'`.
4. Disparo del servicio `Mail` hacia la cuenta administrativa.
5. Si el envío es exitoso, se ejecuta `DB::commit()`. Ante cualquier excepción, `DB::rollBack()` purga la transacción para evitar cuentas huérfanas.

### 4.2. Eliminación de Cuentas por ID (`eliminarPorId`)
Permite remover registros activos mediante su identificador primario.

```php
public function eliminarPorId($id) {
    $usuario = User::findOrFail($id);
    $nombre = $usuario->name;
    $usuario->delete();
    
    return redirect()->back()->with('success', 'Has eliminado la cuenta de '.$nombre.' exitosamente.');
}
```
**Flujo de ejecución:** 
Ejecuta `User::findOrFail($id)`. Si el registro es inexistente, arroja un error 404. Si es localizado, extrae el nombre para la retroalimentación visual y destruye la entidad en la base de datos mediante el método `delete()`.

### 4.3. Validación y Activación de Accesos (`aprobar`)
Invocado al consumir el token de validación. Remueve las restricciones del modelo para habilitar la autenticación.

```php
public function aprobar($token) {
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
**Flujo de ejecución:**
1. Saneamiento del string de entrada con `trim()`.
2. Búsqueda del registro condicionado al `confirmation_token`.
3. Si el token es caduco o nulo, se interrumpe la ejecución con una redirección de error.
4. Si es válido, se muta el `status` a `'activo'`, se invalida el token (`null`) y se estampa la marca de tiempo en `email_verified_at`.

### 4.4. Rechazo y Purga de Solicitudes (`rechazar`)
Ejecutado al declinar una solicitud desde la notificación por correo.

```php
public function rechazar($token) {
    $tokenLimpio = trim($token);
    
    $usuario = User::where('confirmation_token', $tokenLimpio)->first();

    if (!$usuario) {
        return redirect('/personal')->with('error', 'El enlace no es válido o esta solicitud ya fue procesada.');
    }

    $usuario->delete();
    
    return redirect('/personal')->with('success', 'Has rechazado la solicitud.');
}
```
**Flujo de ejecución:**
Filtra el parámetro y localiza la petición pendiente. En caso de acierto, invoca `$usuario->delete()`, eliminando físicamente el registro para evitar el almacenamiento de cuentas denegadas en el sistema.
