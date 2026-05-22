<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Subscription;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(protected ClientService $clientService)
    {
    }

    // Carga inicial para AlpineJS
    public function apiInit()
    {
        // Traemos a los clientes con su relación de suscripción cargada
        $clients = Client::with('subscription')->latest()->get();

        // Traemos solo los planes activos
        $subscriptions = Subscription::query()->where('is_active', true)->get();

        return response()->json([
            'clients' => $clients,
            'subscriptions' => $subscriptions
        ]);
    }

    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            // Generales y Suscripción
            'name'                   => 'required|string|max:100',
            'phone'                  => 'nullable|string|max:20',
            'email'                  => 'nullable|email|max:255',
            'subscription_id'        => 'nullable|exists:subscriptions,id',
            'start_subscription'     => 'nullable|date',

            // Dirección Operativa (General)
            'codigo_postal'          => 'nullable|string|max:5',
            'calle'                  => 'nullable|string|max:255',
            'numero_exterior'        => 'nullable|string|max:20',
            'numero_interior'        => 'nullable|string|max:20',
            'colonia'                => 'nullable|string|max:255',
            'ciudad'                 => 'nullable|string|max:255',
            'estado'                 => 'nullable|string|max:255',

            // Datos Fiscales Generales
            'wantsBilling'           => 'boolean',
            'same_billing_address'   => 'boolean',
            'rfc'                    => 'nullable|string|max:14',
            'razon_social'           => 'nullable|string|max:255',
            'regimen_fiscal'         => 'nullable|string|max:10',

            // NUEVO: Dirección Fiscal
            'fiscal_codigo_postal'   => 'nullable|string|max:5',
            'fiscal_calle'           => 'nullable|string|max:255',
            'fiscal_numero_exterior' => 'nullable|string|max:20',
            'fiscal_numero_interior' => 'nullable|string|max:20',
            'fiscal_colonia'         => 'nullable|string|max:255',
            'fiscal_ciudad'          => 'nullable|string|max:255',
            'fiscal_estado'          => 'nullable|string|max:255',
        ]);

        $client = $this->clientService->guardarCliente($datosValidados);
        $client->load('subscription'); // Cargamos la relación para devolverla al frontend

        return response()->json(['success' => true, 'client' => $client]);
    }

    public function update(Request $request, Client $client)
    {
        $datosValidados = $request->validate([
            // Generales y Suscripción
            'name'                   => 'required|string|max:100',
            'phone'                  => 'nullable|string|max:20',
            'email'                  => 'nullable|email|max:255',
            'subscription_id'        => 'nullable|exists:subscriptions,id',
            'start_subscription'     => 'nullable|date',

            // Dirección Operativa (General)
            'codigo_postal'          => 'nullable|string|max:5',
            'calle'                  => 'nullable|string|max:255',
            'numero_exterior'        => 'nullable|string|max:20',
            'numero_interior'        => 'nullable|string|max:20',
            'colonia'                => 'nullable|string|max:255',
            'ciudad'                 => 'nullable|string|max:255',
            'estado'                 => 'nullable|string|max:255',

            // Datos Fiscales Generales
            'wantsBilling'           => 'boolean',
            'same_billing_address'   => 'boolean',
            'rfc'                    => 'nullable|string|max:14',
            'razon_social'           => 'nullable|string|max:255',
            'regimen_fiscal'         => 'nullable|string|max:10',

            // NUEVO: Dirección Fiscal
            'fiscal_codigo_postal'   => 'nullable|string|max:5',
            'fiscal_calle'           => 'nullable|string|max:255',
            'fiscal_numero_exterior' => 'nullable|string|max:20',
            'fiscal_numero_interior' => 'nullable|string|max:20',
            'fiscal_colonia'         => 'nullable|string|max:255',
            'fiscal_ciudad'          => 'nullable|string|max:255',
            'fiscal_estado'          => 'nullable|string|max:255',
        ]);

        $client = $this->clientService->guardarCliente($datosValidados, $client);
        $client->load('subscription');

        return response()->json(['success' => true, 'client' => $client]);
    }

    public function destroy(Client $client)
    {
        $this->clientService->eliminarCliente($client);
        return response()->json(['success' => true]);
    }
}
