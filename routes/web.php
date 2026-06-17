<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;         
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\PagoController;
use App\Http\Controllers\BrickPagoController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmpleadoController;

use App\Models\Service;
use App\Models\Supply;
use App\Models\Subscription;
use App\Models\Client;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================
// RUTAS PÚBLICAS (Login / Logout)
// =========================================================
Route::get('/signin', function () { return view('pages.auth.signin', ['title' => 'Sign In']); })->name('signin');
Route::get('/signup', function () { return view('pages.auth.signup', ['title' => 'Sign Up']); })->name('signup');

Route::post('/signin', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/'); 
    }

    return back()->withErrors([
        'email' => 'Las credenciales proporcionadas no son correctas.',
    ])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/signin');
})->name('logout');


// =========================================================
// RUTAS PROTEGIDAS GENERALES (Administradores y Cajeros)
// =========================================================
Route::middleware(['auth'])->group(function () {

    // 1. SECCIÓN PRINCIPAL (Punto de Venta)
    Route::get('/', function () {
        $services = Service::query()
            ->where('is_active', true)
            ->where('is_for_orders', false)
            ->get();
        $supplies = Supply::query()->where('is_active', true)->get();
        $subscriptions = Subscription::query()->where('is_active', true)->get();
        $clients = Client::query()->latest()->get(); // Integrado de origin/main para Alpine.js

        return view('pages.pos', [
            'title'         => 'Punto de Venta',
            'services'      => $services,
            'supplies'      => $supplies,
            'subscriptions' => $subscriptions,
            'clients'       => $clients
        ]);
    })->name('pos');

    // Ventas
    Route::post('/ventas/checkout', [SalesController::class, 'store'])->name('ventas.checkout');
    Route::get('/ventas/api-historial', [SalesController::class, 'apiHistorial']);
    
    // Clientes API
    Route::get('/api/clientes/init', function () {
        $clients = Client::with([
            'currentSubscription.plan',
            'currentSubscription.currentCycle'
        ])->latest()->get();
        $subscriptions = Subscription::query()->where('is_active', true)->get();

        return response()->json([
            'clients' => $clients,
            'subscriptions' => $subscriptions
        ]);
    });
    Route::post('/api/clientes', [ClientController::class, 'store']);
    Route::put('/api/clientes/{client}', [ClientController::class, 'update']);
    Route::delete('/api/clientes/{client}', [ClientController::class, 'destroy']);

    // Órdenes/Pedidos API
    Route::prefix('api/orders')->group(function () {
        Route::get('/init', [OrderController::class, 'apiInit']);
        Route::post('/', [OrderController::class, 'store']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });

    // Vistas de Navegación Operativa
    Route::get('/historial', function () { return view('pages.historial', ['title' => 'Historial de Ventas']); })->name('historial');
    Route::get('/maquinas', function () { return view('pages.maquinas', ['title' => 'Máquinas IoT']); })->name('maquinas');
    Route::get('/pedidos', function () { return view('pages.pedidos', ['title' => 'Pedidos y Encargos']); })->name('pedidos');
    Route::get('/clientes', function () { return view('pages.clientes', ['title' => 'Clientes y Suscripciones']); })->name('clientes');
    Route::get('/catalogo', function () { return view('pages.catalogo', ['title' => 'Servicios y Productos']); })->name('catalogo');
    
    Route::get('/servicios', function () {
        $services = Service::query()->latest()->get();
        return view('pages.servicios', ['title' => 'Servicios', 'services' => $services]);
    })->name('servicios');

    Route::get('/insumos', function () {
        $supplies = Supply::query()->latest()->get();
        return view('pages.insumos', ['title' => 'Inventario de Insumos', 'supplies' => $supplies]);
    })->name('insumos');

    Route::get('/inventario', function () {
        $insumos = Supply::where('is_active', true)->get();
        return view('pages.inventario', ['title' => 'Inventario de Insumos', 'insumosDb' => $insumos]);
    })->name('inventario');

    Route::get('/suscripciones', function () {
        $subscriptions = Subscription::query()->latest()->get();
        $totalSubscribedClients = Client::query()->whereNotNull('subscription_id')->where('end_subscription', '>=', now())->count();
        return view('pages.suscripciones', [
            'title' => 'Suscripciones',
            'subscriptions' => $subscriptions,
            'totalSubscribedClients' => $totalSubscribedClients
        ]);
    })->name('suscripciones');

    // Operaciones y Corte de Caja
    Route::get('/caja', [CajaController::class, 'corte'])->name('caja');
    Route::post('/caja/movimiento', [CajaController::class, 'movimiento'])->name('caja.movimiento');
    Route::post('/caja/generar-corte', [CajaController::class, 'generarCorte'])->name('caja.generarCorte');
    Route::post('/caja/factura-global', [CajaController::class, 'facturaGlobal'])->name('caja.facturaGlobal');
    Route::put('/caja/configuracion/fondo', [CajaController::class, 'actualizarFondo'])->name('caja.actualizarFondo'); // Agregada de origin/main
    
    Route::get('/newcalendar', [CalendarController::class, 'index'])->name('calendar.index');

    // 4. RUTAS DE FACTURACIÓN
    Route::get('/facturacion', function () { return view('pages.facturacion', ['title' => 'Facturación SAT']); })->name('facturacion');
    Route::get('/factura/crear', [FacturaController::class, 'create'])->name('factura.crear');
    Route::post('/factura/crear', [FacturaController::class, 'facturar'])->name('venta.facturar');
    Route::get('/factura/archivo/{id}/{tipo?}', [FacturaController::class, 'descargarArchivo'])->name('factura.archivo');
    Route::get('/clientes/buscar', [ClientController::class, 'buscar']);

    Route::get('/calendar', function () { return view('pages.calender', ['title' => 'Calendar']); })->name('calendar');
    Route::get('/profile', function () { return view('pages.profile', ['title' => 'Profile']); })->name('profile');

    // 5. RUTAS DE MERCADO PAGO Y TERMINAL
    Route::post('/pagar', [PagoController::class, 'iniciarPago'])->name('pago.iniciar');
    Route::get('/pago/exito', [PagoController::class, 'pagoExitoso'])->name('pago.exito');
    Route::get('/pago/fallo', [PagoController::class, 'pagoFallido'])->name('pago.fallo');
    Route::get('/pago/pendiente', [PagoController::class, 'pagoPendiente'])->name('pago.pendiente');
    Route::get('/pagar-con-tarjeta', [BrickPagoController::class, 'mostrarFormulario'])->name('brick.form');
    Route::post('/procesar-pago', [BrickPagoController::class, 'procesarPago'])->name('brick.procesar');
    
    Route::post('/terminal/cobrar', [TerminalController::class, 'cobrarEnTerminal']);
    Route::get('/terminal/estado/{id}', [TerminalController::class, 'verificarEstado']);
    Route::get('/mis-terminales', function () {
        $response = Http::withHeaders(['Authorization' => 'Bearer APP_USR-3611189794742413-041809-56f3d298a20d175ac8db7489fd3eef13-409289088'])
            ->get('https://api.mercadopago.com/point/integration-api/devices');
        return $response->json();
    });

    // =========================================================
    // RUTAS EXCLUSIVAS PARA ADMINISTRADORES
    // =========================================================
    Route::middleware(['admin'])->group(function () {
        
        // Eliminación de ventas (solo admin puede borrar ventas)
        Route::delete('/ventas/bulk', [SalesController::class, 'destroyBulk'])->name('ventas.bulkDestroy');
        Route::delete('/ventas/{id}', [SalesController::class, 'destroy'])->name('ventas.destroy');

        // Gestión de Personal
        Route::get('/personal', function () { 
            $empleados = User::latest()->get(); 
            return view('pages.personal', ['title' => 'Gestión de Personal', 'empleados' => $empleados]); 
        })->name('personal');
        
        Route::post('/personal/guardar', [EmpleadoController::class, 'store'])->name('personal.store');
        Route::delete('/personal/eliminar/{id}', [EmpleadoController::class, 'eliminarPorId'])->name('personal.eliminar_id');
        
        Route::get('/aprobar-cuenta/{token}', [EmpleadoController::class, 'aprobar'])->name('cuenta.aprobar');
        Route::get('/rechazar-cuenta/{token}', [EmpleadoController::class, 'rechazar'])->name('cuenta.rechazar');

        // Modificaciones al Catálogo
        Route::patch('/catalogo/toggle-estado', [CatalogoController::class, 'toggleEstado'])->name('catalogo.toggle');
        Route::post('/catalogo/guardar', [CatalogoController::class, 'store'])->name('catalogo.store');
        Route::put('/catalogo/actualizar', [CatalogoController::class, 'update'])->name('catalogo.update');
        Route::delete('/catalogo/eliminar', [CatalogoController::class, 'destroy'])->name('catalogo.destroy');
    });
});