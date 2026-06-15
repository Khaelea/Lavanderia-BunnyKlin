<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Client;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function guardarOrden(array $datos, ?Order $order = null): Order
    {
        return DB::transaction(function () use ($datos, $order) {

            $clientId = $datos['client_id'] ?? null;

            // Si no viene un ID, significa que es un cliente nuevo que escribió a mano
            if (!$clientId && !empty($datos['name'])) {
                // Buscamos si ya existe por nombre exacto para no duplicar por error,
                // si no existe, lo creamos guardando su teléfono (pueda ser null o string)
                $client = Client::query()->firstOrCreate(
                    ['name' => $datos['name']],
                    ['phone' => $datos['phone'] ?? null]
                );
                $clientId = $client->id;
            }

            // 2. OBTENER EL SERVICIO (Necesario para el SaleItem)
            // Traemos el servicio de la base de datos para tomar su nombre y precio
            $servicio = \App\Models\Service::query()->find($datos['service_id']);

            // --- MODO EDICIÓN ---
            if ($order) {
                $order->update([
                    'client_id'       => $clientId,
                    'service_id'      => $datos['service_id'],
                    'quantity'        => $datos['quantity'],
                    'details'         => $datos['details'] ?? null,
                    'total_price'     => $datos['total'],
                    'advance_payment' => $datos['advance'] ?? 0,
                    'status'          => $datos['status'],
                    'arrival_date'    => $datos['arrivalDate'],
                    'delivery_date'   => $datos['deliveryDate'] ?? null,
                ]);

                // Actualizamos la venta asociada
                $order->sale()->update([
                    'total' => $datos['total'],
                    'client_id' => $clientId
                ]);

                // Actualizamos el renglón del ticket (SaleItem)
                $saleItem = $order->sale->items()->first();
                if ($saleItem && $servicio) {
                    $saleItem->update([
                        'item_type'      => \App\Models\Service::class,
                        'item_id'        => $servicio->id,
                        'name_snapshot'  => $servicio->name,
                        'price_snapshot' => $servicio->price,
                        'quantity'       => $datos['quantity'],
                        'subtotal'       => $datos['total'],
                    ]);
                }

                return $order;
            }

            // --- MODO CREACIÓN ---

            // 3. Creamos la venta financiera (Cabecera del ticket)
            // Nota: Al no pasar 'reference', el modelo Sale usará su método boot() para generar el folio 'BK-XXXX'
            $sale = Sale::query()->create([
                'reference'      => $datos['reference'],
                'client_id'      => $clientId,
                'total'          => $datos['total'],
                'payment_method' => 'Pendiente',
                'status'         => 'pendiente',
            ]);

            // 4. CREAMOS EL RENGLÓN DEL TICKET (SaleItem) 🚀
            if ($servicio) {
                $sale->items()->create([
                    'item_type'      => \App\Models\Service::class,
                    'item_id'        => $servicio->id,
                    'name_snapshot'  => $servicio->name,
                    'price_snapshot' => $servicio->price,
                    'quantity'       => $datos['quantity'],
                    // Usamos el total calculado en JS, ya que podría tener descuento de suscripción
                    'subtotal'       => $datos['total'],
                ]);
            }

            // 5. Creamos la orden operativa
            $nuevaOrden = Order::query()->create([
                'reference'       => $datos['reference'], // Este es el 'ORD-XXXX'
                'sale_id'         => $sale->id,
                'client_id'       => $clientId,
                'service_id'      => $datos['service_id'],
                'quantity'        => $datos['quantity'],
                'details'         => $datos['details'] ?? null,
                'total_price'     => $datos['total'],
                'advance_payment' => $datos['advance'] ?? 0,
                'status'          => $datos['status'],
                'arrival_date'    => $datos['arrivalDate'],
                'delivery_date'   => $datos['deliveryDate'] ?? null,
            ]);

            // 6. LÓGICA DE SUSCRIPCIÓN (DESCONTAR KILOS)
            if ($clientId) {
                $clienteDB = Client::query()->find($clientId);

                if ($clienteDB && $clienteDB->currentSubscription && $clienteDB->currentSubscription->currentCycle) {
                    $ciclo = $clienteDB->currentSubscription->currentCycle;
                    $kilosRestantes = max(0, $ciclo->kilos_allowed - $ciclo->kilos_consumed);

                    if ($kilosRestantes > 0) {
                        $kilosAConsumir = min($datos['quantity'], $kilosRestantes);
                        $ciclo->increment('kilos_consumed', $kilosAConsumir);
                    }
                }
            }

            return $nuevaOrden;
        });
    }

    public function eliminarOrden(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            // Eliminar la orden también eliminará la venta en cascada (por tu migración)
            return $order->delete();
        });
    }
}
