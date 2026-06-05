<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\BrickPagoController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CajaController;
use Illuminate\Support\Facades\Http;
// Imports para ruta principal /
use App\Models\Service;
use App\Models\Supply;
use App\Models\Subscription;
// Import para Controlador del catalogo
use App\Http\Controllers\CatalogoController;
// Import para Controlador del historial de ventas
use App\Http\Controllers\SalesController;
// Import para Controlador de orders
use App\Http\Controllers\OrderController;
// Import para modelo de cliente
use App\Models\Client;
// Import para controlador de clientes
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================
// 1. SECCIÓN PRINCIPAL
// =========================================================

Route::get('/', function () {

    $services = Service::query()
        ->where('is_active', true)
        ->where('is_for_orders', false)
        ->get();
    $supplies = Supply::query()->where('is_active', true)->get();
    $subscriptions = Subscription::query()->where('is_active', true)->get();
    $clients = Client::query()->latest()->get();

    return view('pages.pos', [
        'title'         => 'Punto de Venta',
        'services'      => $services,
        'supplies'      => $supplies,
        'subscriptions' => $subscriptions,
        'clients'       => $clients
    ]);
})->name('pos');

// Ruta para cambiar el estado de un elemento del catalogo
Route::patch('/catalogo/toggle-estado', [CatalogoController::class, 'toggleEstado'])->name('catalogo.toggle');

// Ruta para guardar cosas del catalogo
Route::post('/catalogo/guardar', [CatalogoController::class, 'store'])->name('catalogo.store');
// Ruta para editar cosas del catalogo
Route::put('/catalogo/actualizar', [CatalogoController::class, 'update'])->name('catalogo.update');
// Ruta para eliminar registros del catalogo
Route::delete('/catalogo/eliminar', [CatalogoController::class, 'destroy'])->name('catalogo.destroy');

// Ruta para registrar en el historial de compras
Route::post('/ventas/checkout', [SalesController::class, 'store'])->name('ventas.checkout');
// Ruta para obtener el historial de compras
Route::get('/ventas/api-historial', [SalesController::class, 'apiHistorial']);
// Ruta para borrar múltiples ventas a la vez
Route::delete('/ventas/bulk', [SalesController::class, 'destroyBulk'])->name('ventas.bulkDestroy');
// Ruta para borrar una sola venta
Route::delete('/ventas/{id}', [SalesController::class, 'destroy'])->name('ventas.destroy');

// Rutas para clientes
Route::get('/api/clientes/init', function () {
    $clients = Client::with(['currentSubscription.plan', 'latestSubscription.plan'])->latest()->get();
    $subscriptions = Subscription::query()->where('is_active', true)->get();

    return response()->json([
        'clients' => $clients,
        'subscriptions' => $subscriptions
    ]);
});
Route::post('/api/clientes', [ClientController::class, 'store']);
Route::put('/api/clientes/{client}', [ClientController::class, 'update']);
Route::delete('/api/clientes/{client}', [ClientController::class, 'destroy']);

// Rutas para ordenes/pedidos
Route::prefix('api/orders')->group(function () {
    // Carga inicial de datos para la vista (GET)
    Route::get('/init', [OrderController::class, 'apiInit']);

    // Guardar un nuevo encargo (POST)
    Route::post('/', [OrderController::class, 'store']);

    // Actualizar un encargo existente (PUT)
    // El parámetro {order} usa el Model Binding de Laravel
    Route::put('/{order}', [OrderController::class, 'update']);

    // Eliminar un encargo (DELETE)
    Route::delete('/{order}', [OrderController::class, 'destroy']);
});

Route::get('/historial', function () {
    return view('pages.historial', ['title' => 'Historial de Ventas']);
})->name('historial');

Route::get('/maquinas', function () {
    return view('pages.maquinas', ['title' => 'Máquinas IoT']);
})->name('maquinas');


// =========================================================
// 2. SECCIÓN CATÁLOGOS
// =========================================================

Route::get('/servicios', function () {
    $services = Service::query()->latest()->get();
    return view('pages.servicios', ['title' => 'Servicios', 'services' => $services]);
})->name('servicios');

Route::get('/insumos', function () {
    $supplies = Supply::query()->latest()->get();
    return view('pages.insumos', ['title' => 'Inventario de Insumos', 'supplies' => $supplies]);
})->name('insumos');

Route::get('/suscripciones', function () {
    $subscriptions = Subscription::query()->latest()->get();
    $totalSubscribedClients = Client::query()->has('currentSubscription')->count();
    return view('pages.suscripciones', [
        'title' => 'Suscripciones',
        'subscriptions' => $subscriptions,
        'totalSubscribedClients' => $totalSubscribedClients
    ]);
})->name('suscripciones');

// =========================================================
// 3. SECCIÓN OPERACIÓN
// =========================================================

// 👇 AQUÍ ESTÁ LA RUTA DE PEDIDOS Y ENCARGOS QUE FALTABA 👇
Route::get('/pedidos', function () {
    return view('pages.pedidos', ['title' => 'Pedidos y Encargos']);
})->name('pedidos');

// 👇 AQUÍ ESTÁ LA RUTA DE CLIENTES Y SUSCRIPCIONES 👇
Route::get('/clientes', function () {
    return view('pages.clientes', ['title' => 'Clientes y Suscripciones']);
})->name('clientes');


/*Route::get('/caja', function () {
    return view('pages.caja', ['title' => 'Corte de Caja']);
})->name('caja');*/

//Rutas para corte de caja
Route::get('/caja', [CajaController::class, 'corte'])->name('caja');
Route::post('/caja/movimiento', [CajaController::class, 'movimiento'])->name('caja.movimiento');
Route::post('/caja/generar-corte', [CajaController::class, 'generarCorte'])->name('caja.generarCorte');
Route::post('/caja/factura-global', [CajaController::class, 'facturaGlobal'])->name('caja.facturaGlobal');

Route::get('/newcalendar', [CalendarController::class, 'index'])->name('calendar.index');

//RUTAS DE FACTURACIÓN (ACOMODAR DESPUÉS DE PROBAR)
// Mostrar el formulario
Route::get('/factura/crear', [FacturaController::class, 'create'])->name('factura.crear');

// Procesar el formulario
Route::post('/factura/crear', [FacturaController::class, 'facturar'])->name('venta.facturar');

// Descargar archivos de la factura
Route::get('/factura/archivo/{id}/{tipo?}', [FacturaController::class, 'descargarArchivo'])->name('factura.archivo');

// =========================================================
// RUTAS DE LA PLANTILLA Y MERCADO PAGO
// =========================================================
Route::get('/calendar', function () { return view('pages.calender', ['title' => 'Calendar']); })->name('calendar');
Route::get('/profile', function () { return view('pages.profile', ['title' => 'Profile']); })->name('profile');
Route::get('/signin', function () { return view('pages.auth.signin', ['title' => 'Sign In']); })->name('signin');
Route::get('/signup', function () { return view('pages.auth.signup', ['title' => 'Sign Up']); })->name('signup');
Route::post('/pagar', [PagoController::class, 'iniciarPago'])->name('pago.iniciar');

Route::get('/pago/exito', [PagoController::class, 'pagoExitoso'])->name('pago.exito');
Route::get('/pago/fallo', [PagoController::class, 'pagoFallido'])->name('pago.fallo');
Route::get('/pago/pendiente', [PagoController::class, 'pagoPendiente'])->name('pago.pendiente');

// Checkout Bricks
Route::get('/pagar-con-tarjeta', [BrickPagoController::class, 'mostrarFormulario'])->name('brick.form');
Route::post('/procesar-pago', [BrickPagoController::class, 'procesarPago'])->name('brick.procesar');

// Consultar terminales físicas de Mercado Pago Point
Route::get('/mis-terminales', function () {
    $response = Http::withHeaders([
        'Authorization' => 'Bearer TU_ACCESS_TOKEN_DE_PRODUCCION'
    ])->get('https://api.mercadopago.com/point/integration-api/devices');

    return $response->json();
});
