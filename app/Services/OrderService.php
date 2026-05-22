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

            // 1. Manejo del Cliente (Buscar por teléfono o crear uno anónimo)
            $clientId = null;
            if (!empty($datos['phone'])) {
                $client = Client::query()->firstOrCreate(
                    ['phone' => $datos['phone']],
                    ['name' => $datos['name'] ?: 'Cliente Mostrador']
                );
                $clientId = $client->id;
            }

            if ($order) {
                // Modo EDICIÓN: Solo actualizamos la orden (y la venta si cambió el total)
                $order->update([
                    'client_id' => $clientId,
                    'service_id' => $datos['service_id'],
                    'quantity' => $datos['quantity'],
                    'details' => $datos['details'] ?? null,
                    'total_price' => $datos['total'],
                    'advance_payment' => $datos['advance'] ?? 0,
                    'status' => $datos['status'],
                    'arrival_date' => $datos['arrivalDate'],
                    'delivery_date' => $datos['deliveryDate'] ?? null,
                ]);

                // Actualizamos la venta asociada
                $order->sale()->update(['total' => $datos['total'], 'client_id' => $clientId]);

                return $order;
            }

            // Modo CREACIÓN:
            // 2. Creamos la venta financiera asociada
            $sale = Sale::query()->create([
                'reference' => $datos['ticket'],
                'client_id' => $clientId,
                'total' => $datos['total'],
                'payment_method' => 'Pendiente', // Se actualizará en el punto de venta
                'status' => 'pendiente',
            ]);

            // 3. Creamos la orden operativa
            return Order::query()->create([
                'sale_id' => $sale->id,
                'client_id' => $clientId,
                'service_id' => $datos['service_id'],
                'quantity' => $datos['quantity'],
                'details' => $datos['details'] ?? null,
                'total_price' => $datos['total'],
                'advance_payment' => $datos['advance'] ?? 0,
                'status' => $datos['status'],
                'arrival_date' => $datos['arrivalDate'],
                'delivery_date' => $datos['deliveryDate'] ?? null,
            ]);
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
