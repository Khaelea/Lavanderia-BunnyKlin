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
                // Si NO factura, limpiamos todo lo fiscal
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
                // Si SÍ factura y marcó que usa la misma dirección
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

            // 2. CÁLCULO DE LA SUSCRIPCIÓN CON PHP (Carbon)
            if (!empty($datos['subscription_id']) && !empty($datos['start_subscription'])) {
                $suscripcion = Subscription::query()->find($datos['subscription_id']);

                if ($suscripcion) {
                    // Convertimos la fecha de inicio a Carbon
                    $fechaInicio = Carbon::parse($datos['start_subscription']);

                    // addMonthsNoOverflow evita errores como: 31 Enero + 1 mes = 3 Marzo.
                    // Lo ajustará correctamente al 28 de Febrero.
                    $datos['end_subscription'] = $fechaInicio->addMonthsNoOverflow($suscripcion->duration_months)->toDateString();
                }
            } else {
                $datos['end_subscription'] = null;
            }

            // 3. Limpiamos el campo virtual 'start_subscription' para que Eloquent no
            // intente guardarlo en la base de datos (ya que no existe esa columna en clients)
            unset($datos['start_subscription']);

            // 4. Guardamos
            if ($client) {
                $client->update($datos);
                return $client;
            }

            return Client::query()->create($datos);
        });
    }

    public function eliminarCliente(Client $client): bool
    {
        // Gracias al nullOnDelete en las migraciones de ventas y pedidos,
        // eliminar al cliente no romperá tu historial financiero.
        return $client->delete();
    }
}
