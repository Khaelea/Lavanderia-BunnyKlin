<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClientService
{
    /**
     * Crea o actualiza un cliente.
*/

    public function guardarCliente(array $datos, ?Client $client = null): Client
    {
        return DB::transaction(function () use ($datos, $client) {

            // 1. Lógica de Facturación y Direcciones
            if (empty($datos['wantsBilling']) || $datos['wantsBilling'] == false) {
                $datos['rfc'] = null;
                $datos['razon_social'] = null;
                $datos['regimen_fiscal'] = null;
                $datos['same_billing_address'] = false;
                $datos['fiscal_codigo_postal'] = null;
                $datos['fiscal_calle'] = null;
                $datos['fiscal_numero_exterior'] = null;
                $datos['fiscal_numero_interior'] = null;
                $datos['fiscal_colonia'] = null;
                $datos['fiscal_ciudad'] = null;
                $datos['fiscal_estado'] = null;
            } else {
                if (!empty($datos['same_billing_address']) && $datos['same_billing_address'] == true) {
                    $datos['fiscal_codigo_postal']   = $datos['codigo_postal'] ?? null;
                    $datos['fiscal_calle']           = $datos['calle'] ?? null;
                    $datos['fiscal_numero_exterior'] = $datos['numero_exterior'] ?? null;
                    $datos['fiscal_numero_interior'] = $datos['numero_interior'] ?? null;
                    $datos['fiscal_colonia']         = $datos['colonia'] ?? null;
                    $datos['fiscal_ciudad']          = $datos['ciudad'] ?? null;
                    $datos['fiscal_estado']          = $datos['estado'] ?? null;
                }
            }

            // 2. Extraemos los datos de suscripción para que no choquen con la tabla de clientes
            $subId = $datos['subscription_id'] ?? null;
            $subStart = $datos['start_subscription'] ?? null;
            unset($datos['subscription_id'], $datos['start_subscription'], $datos['wantsBilling']);

            // 3. Guardamos el cliente primero
            if ($client) {
                $client->update($datos);
            } else {
                $client = Client::query()->create($datos);
            }

            // 4. LÓGICA DE LA NUEVA ARQUITECTURA DE SUSCRIPCIONES
            if ($subId && $subStart) {
                $suscripcion = \App\Models\Subscription::query()->find($subId);

                if ($suscripcion) {
                    $fechaInicio = \Carbon\Carbon::parse($subStart);
                    // Calculamos el fin del contrato total (ej. 6 meses)
                    $fechaFinContrato = $fechaInicio->copy()->addMonthsNoOverflow($suscripcion->duration_months);

                    // A) Opcional: Si el cliente tenía un contrato activo viejo, lo cancelamos para evitar duplicados
                    $client->clientSubscriptions()->where('status', 'active')->update(['status' => 'canceled']);

                    // B) Creamos el nuevo contrato en client_subscriptions
                    $contrato = $client->clientSubscriptions()->create([
                        'subscription_id' => $suscripcion->id,
                        'start_date'      => $fechaInicio->toDateString(),
                        'end_date'        => $fechaFinContrato->toDateString(),
                        'status'          => 'active',
                    ]);

                    // C) Generamos su primer ciclo mensual (1 mes exacto)
                    $cicloFin = $fechaInicio->copy()->addMonthNoOverflow();

                    // (Por si el contrato durara menos de un mes, evitamos que el ciclo lo rebase)
                    if ($cicloFin->greaterThan($fechaFinContrato)) {
                        $cicloFin = $fechaFinContrato;
                    }

                    $contrato->cycles()->create([
                        'cycle_start'    => $fechaInicio->toDateString(),
                        'cycle_end'      => $cicloFin->toDateString(),
                        'kilos_allowed'  => $suscripcion->kilos_per_month,
                        'kilos_consumed' => 0, // Inicia con 0 kilos consumidos
                    ]);
                }
            }

            return $client;
        });
    }

    public function eliminarCliente(Client $client): bool
    {
        // Gracias al nullOnDelete en las migraciones de ventas y pedidos,
        // eliminar al cliente no romperá tu historial financiero.
        return $client->delete();
    }
}
