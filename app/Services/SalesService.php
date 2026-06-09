<?php

namespace App\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function procesarVenta(array $datos)
    {
        return DB::transaction(function () use ($datos) {

            $sale = Sale::query()->create([
                'total'          => $datos['total'],
                'payment_method' => $datos['metodo_pago'],
                // 'client_id'   => null // (Asegúrate de mapearlo si lo recibes en $datos)
            ]);

            $mapaModelos = [
                'services'      => \App\Models\Service::class,
                'supplies'      => \App\Models\Supply::class,
                'subscriptions' => \App\Models\Subscription::class,
            ];

            foreach ($datos['detalles'] as $item) {
                $modeloClase = $mapaModelos[$item['category']] ?? null;

                if (!$modeloClase) {
                    throw new \Exception("Categoría de producto no reconocida: " . $item['category']);
                }

                // 1. Guardamos el renglón en el ticket de venta
                $sale->items()->create([
                    'item_type'      => $modeloClase,
                    'item_id'        => $item['id'],
                    'name_snapshot'  => $item['name'],
                    'price_snapshot' => $item['price'],
                    'quantity'       => $item['quantity'],
                    'subtotal'       => $item['price'] * $item['quantity'],
                ]);

                // 2. NUEVO: Reducir el stock SÓLO si el artículo es un insumo (supply)
                if ($item['category'] === 'supplies') {
                    $supply = \App\Models\Supply::query()->find($item['id']);

                    if ($supply) {
                        // Validación de seguridad: Evitar vender si no hay stock suficiente
                        if ($supply->stock < $item['quantity']) {
                            throw new \Exception("Stock insuficiente para el producto: " . $supply->name . ". Stock actual: " . $supply->stock);
                        }

                        // El método decrement() hace una consulta directa y rápida a la BD
                        // restando la cantidad vendida a la columna 'stock'
                        $supply->decrement('stock', $item['quantity']);
                    }
                }
            }

            return $sale;
        });
    }

    public function eliminarVenta(int $id)
    {
        $venta = Sale::query()->findOrFail($id);
        // Al eliminar el modelo, la BD se encarga de los sale_items en cascada
        return $venta->delete();
    }

    public function eliminarVentasMasivas(array $ids)
    {
        return Sale::query()->whereIn('id', $ids)->delete();
    }

}
