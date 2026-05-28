<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Point\PointClient;
use MercadoPago\Exceptions\MPApiException;
use Throwable;

class TerminalController extends Controller
{
    private $accessToken;
    private $deviceId;

    public function __construct()
    {
        $this->accessToken = env('MERCADOPAGO_ACCESS_TOKEN');
        $this->deviceId = env('MERCADOPAGO_POINT_DEVICE_ID');

        MercadoPagoConfig::setAccessToken($this->accessToken);
    }

    public function cobrarEnTerminal(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric|min:5'
        ]);

        try {
            $client = new PointClient();
            
            $montoCentavos = (int) ($request->total * 100);

            $paymentIntentRequest = [
                "amount" => $montoCentavos,
                "additional_info" => [
                    "external_reference" => 'BK-' . time(),
                    "print_on_terminal" => true
                ]
            ];

            $response = $client->createPaymentIntent(
                $this->deviceId,
                $paymentIntentRequest
            );

            \Log::info('PAYMENT INTENT OK', [
                'response' => $response
            ]);

            return response()->json([
                'success' => true,
                'payment_intent_id' => $response->id
            ]);

        } catch (MPApiException $e) {
            
            $apiResponse = $e->getApiResponse();
            $errorDetalle = $apiResponse ? $apiResponse->getContent() : 'Sin detalles';

            \Log::error('ERROR DE API MERCADO PAGO', [
                'message' => $e->getMessage(),
                'status_code' => $e->getStatusCode(),
                'response' => $errorDetalle
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Rechazado por Mercado Pago',
                'details' => $errorDetalle
            ], 500);

        } catch (Throwable $e) {
            
            \Log::error('ERROR INTERNO', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verificarEstado($id)
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->get("https://api.mercadopago.com/point/integration-api/payment-intents/{$id}");

            \Log::info('ESTADO PAYMENT INTENT', [
                'status_http' => $response->status(),
                'body' => $response->json()
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'status' => 'RETRY',
                    'error' => 'HTTP ' . $response->status()
                ]);
            }

            $data = $response->json();

            // 1. Extraemos el estado real del dinero
            $estadoPago = $data['payment']['state'] ?? $data['payment']['status'] ?? null;
            
            if ($estadoPago) {
                $estadoPago = strtolower($estadoPago);
            }

            // 2. Extraemos el estado general de la terminal
            $estadoIntent = $data['state'] ?? 'UNKNOWN';

            // 3. EL PARCHE PARA EL PROBLEMA DEL TICKET:
            // Si el dinero ya pasó ("approved"), forzamos el estado general a FINISHED.
            // Así pos.js cierra la venta exitosamente aunque la terminal marque error de papel u otro problema no monetario.
            if ($estadoPago === 'approved') {
                $estadoIntent = 'FINISHED';
            }

            return response()->json([
                'success' => true,
                'status' => $estadoIntent,
                'payment_status' => $estadoPago,
            ]);

        } catch (Throwable $e) {
            \Log::error('ERROR CONSULTANDO ESTADO', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'status' => 'RETRY',
                'error' => $e->getMessage()
            ]);
        }
    }
}