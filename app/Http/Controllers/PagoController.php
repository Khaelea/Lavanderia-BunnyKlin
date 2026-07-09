<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    public function iniciarPago(Request $request)
    {
        $baseUrl = config('app.url'); // http://127.0.0.1:8000
        $data = [
            "items" => [
                [
                    "title"       => "Servicio de Lavandería",
                    "description" => "Lavado al seco y planchado",
                    "quantity"    => 1,
                    "unit_price"  => 5.00,
                    "currency_id" => "MXN"
                ]
            ],
            "back_urls" => [
                "success" => $baseUrl . '/pago/exito',
                "failure" => $baseUrl . '/pago/fallo',
                "pending" => $baseUrl . '/pago/pendiente'
            ],
            // "auto_return" => "approved", // Desactivado para evitar el error
        ];

        $respuesta = $this->mercadoPagoService->crearPreferencia($data);

        if (isset($respuesta['init_point'])) {
            return redirect()->away($respuesta['init_point']);
        }

        // Si hay error, el servicio ya lo muestra con echo y exit
    }

    public function pagoExitoso(Request $request)
    {
        return view('pagos.exito');
    }

    public function pagoFallido(Request $request)
    {
        return view('pagos.fallo');
    }

    public function pagoPendiente(Request $request)
    {
        return view('pagos.pendiente');
    }
}
