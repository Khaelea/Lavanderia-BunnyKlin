<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\BrickPagoController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CajaController;
use Illuminate\Support\Facades\Http;
use App\Models\Service;
use App\Models\Supply;
use App\Models\Subscription;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\SalesController;
<<<<<<< HEAD
// Import para Controlador de orders
use App\Http\Controllers\OrderController;
=======
use App\Http\Controllers\ClienteController;
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================
// 1. SECCIÓN PRINCIPAL
// =========================================================

Route::get('/', function () {
    $services = App\Models\Service::query()
        ->where('is_active', true) 
        ->where('is_for_orders', false)
        ->get();
    $supplies = App\Models\Supply::query()->where('is_active', true)->get();
    $subscriptions = App\Models\Subscription::query()->where('is_active', true)->get();

    return view('pages.pos', [
        'title'         => 'Punto de Venta',
<<<<<<< HEAD
        'services'     => $services,
        'supplies'       => $supplies,
=======
        'services'      => $services,
        'supplies'      => $supplies,
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
        'subscriptions' => $subscriptions
    ]);
})->name('pos');

<<<<<<< HEAD
// Ruta para cambiar el estado de un elemento del catalogo
Route::patch('/catalogo/toggle-estado', [App\Http\Controllers\CatalogoController::class, 'toggleEstado'])->name('catalogo.toggle');

// Ruta para guardar cosas del catalogo
=======
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
Route::post('/catalogo/guardar', [CatalogoController::class, 'store'])->name('catalogo.store');
Route::put('/catalogo/actualizar', [CatalogoController::class, 'update'])->name('catalogo.update');
Route::delete('/catalogo/eliminar', [CatalogoController::class, 'destroy'])->name('catalogo.destroy');

Route::post('/ventas/checkout', [SalesController::class, 'store'])->name('ventas.checkout');
Route::get('/ventas/api-historial', [SalesController::class, 'apiHistorial']);
Route::delete('/ventas/bulk', [App\Http\Controllers\SalesController::class, 'destroyBulk'])->name('ventas.bulkDestroy');
Route::delete('/ventas/{id}', [App\Http\Controllers\SalesController::class, 'destroy'])->name('ventas.destroy');

<<<<<<< HEAD
// Rutas para clientes
Route::get('/api/clientes/init', [App\Http\Controllers\ClientController::class, 'apiInit']);
Route::post('/api/clientes', [App\Http\Controllers\ClientController::class, 'store']);
Route::put('/api/clientes/{client}', [App\Http\Controllers\ClientController::class, 'update']);
Route::delete('/api/clientes/{client}', [App\Http\Controllers\ClientController::class, 'destroy']);

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

=======
Route::get('/historial', function () { return view('pages.historial', ['title' => 'Historial de Ventas']); })->name('historial');
Route::get('/maquinas', function () { return view('pages.blank', ['title' => 'Máquinas IoT']); })->name('maquinas');
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece

// =========================================================
// 2. SECCIÓN CATÁLOGOS
// =========================================================
<<<<<<< HEAD

Route::get('/servicios', function () {
    $services = App\Models\Service::query()->latest()->get();
    return view('pages.servicios', ['title' => 'Servicios y Productos', 'services' => $services]);
})->name('servicios');

Route::get('/insumos', function () {
    $supplies = App\Models\Supply::query()->latest()->get();
    return view('pages.insumos', ['title' => 'Inventario de Insumos', 'supplies' => $supplies]);
})->name('insumos');
=======
Route::get('/catalogo', function () { return view('pages.catalogo', ['title' => 'Servicios y Productos']); })->name('catalogo');
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece

// =========================================================
// 3. SECCIÓN OPERACIÓN
// =========================================================
Route::get('/pedidos', function () { return view('pages.pedidos', ['title' => 'Pedidos y Encargos']); })->name('pedidos');
Route::get('/clientes', function () { return view('pages.clientes', ['title' => 'Clientes y Suscripciones']); })->name('clientes');

Route::get('/api/clientes', [ClienteController::class, 'index']);
Route::post('/api/clientes', [ClienteController::class, 'store']);
Route::put('/api/clientes/{id}', [ClienteController::class, 'update']);
Route::delete('/api/clientes/{id}', [ClienteController::class, 'destroy']);

<<<<<<< HEAD

/*Route::get('/caja', function () {
    return view('pages.caja', ['title' => 'Corte de Caja']);
})->name('caja');*/

//Rutas para corte de caja
Route::get('/caja', [CajaController::class, 'corte'])->name('caja');
Route::post('/caja/movimiento', [CajaController::class, 'movimiento'])->name('caja.movimiento');
Route::post('/caja/generar-corte', [CajaController::class, 'generarCorte'])->name('caja.generarCorte');
Route::post('/caja/factura-global', [CajaController::class, 'facturaGlobal'])->name('caja.facturaGlobal');

=======
Route::get('/insumos', function () {
    $insumos = App\Models\Supply::where('is_active', true)->get();
    return view('pages.inventario', ['title' => 'Inventario de Insumos', 'insumosDb' => $insumos]);
})->name('insumos');

Route::get('/facturacion', function () { return view('pages.facturacion', ['title' => 'Facturación SAT']); })->name('facturacion');
Route::get('/caja', function () { return view('pages.blank', ['title' => 'Corte de Caja']); })->name('caja');
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
Route::get('/newcalendar', [CalendarController::class, 'index'])->name('calendar.index');

// =========================================================
// RUTAS DE FACTURACIÓN
// =========================================================
Route::get('/factura/crear', [FacturaController::class, 'create'])->name('factura.crear');
Route::post('/factura/crear', [FacturaController::class, 'facturar'])->name('venta.facturar');
// 👇 Ruta agregada por tu compañero para descargar PDF/XML
Route::get('/factura/archivo/{id}/{tipo?}', [FacturaController::class, 'descargarArchivo'])->name('factura.archivo');

// Descargar archivos de la factura
Route::get('/factura/archivo/{id}/{tipo?}', [FacturaController::class, 'descargarArchivo'])->name('factura.archivo');

// =========================================================
// RUTAS DE MERCADO PAGO Y TRADICIONALES
// =========================================================
Route::get('/calendar', function () { return view('pages.calender', ['title' => 'Calendar']); })->name('calendar');
Route::get('/profile', function () { return view('pages.profile', ['title' => 'Profile']); })->name('profile');
Route::get('/signin', function () { return view('pages.auth.signin', ['title' => 'Sign In']); })->name('signin');
Route::get('/signup', function () { return view('pages.auth.signup', ['title' => 'Sign Up']); })->name('signup');

Route::post('/pagar', [PagoController::class, 'iniciarPago'])->name('pago.iniciar');
Route::get('/pago/exito', [PagoController::class, 'pagoExitoso'])->name('pago.exito');
Route::get('/pago/fallo', [PagoController::class, 'pagoFallido'])->name('pago.fallo');
Route::get('/pago/pendiente', [PagoController::class, 'pagoPendiente'])->name('pago.pendiente');

Route::get('/pagar-con-tarjeta', [BrickPagoController::class, 'mostrarFormulario'])->name('brick.form');
Route::post('/procesar-pago', [BrickPagoController::class, 'procesarPago'])->name('brick.procesar');

// =========================================================
// RUTAS DE TERMINAL POINT (FÍSICA)
// =========================================================
Route::post('/terminal/cobrar', [TerminalController::class, 'cobrarEnTerminal']);
Route::get('/terminal/estado/{id}', [TerminalController::class, 'verificarEstado']);
Route::get('/mis-terminales', function () {
    $response = Http::withHeaders(['Authorization' => 'Bearer APP_USR-3611189794742413-041809-56f3d298a20d175ac8db7489fd3eef13-409289088'])
        ->get('https://api.mercadopago.com/point/integration-api/devices');
    return $response->json();
});