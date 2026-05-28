<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Subscription;
use App\Models\Supply;

class CatalogoService
{
    public function guardarElemento(array $datos)
    {
        return match ($datos['category']) {
            'services' => Service::query()->create([
                'name'           => $datos['name'],
                'clave_prodserv' => $datos['clave_prodserv'] ?? null, // <-- AGREGAR
                'price'          => $datos['price'],
                'description'    => $datos['description'] ?? null,
<<<<<<< HEAD
                'is_active'      => $datos['is_active'] ?? true,
                'is_for_orders'  => $datos['is_for_orders'] ?? false,
=======
                'is_active'      => true
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
            ]),

            'supplies'      => Supply::query()->create([
                'name'           => $datos['name'],
                'clave_prodserv' => $datos['clave_prodserv'] ?? null, // <-- AGREGAR
                'price'          => $datos['price'],
                'stock'          => $datos['stock'] ?? 0,
                'unit'           => $datos['unit'] ?? 'Pza',
<<<<<<< HEAD
                'is_active'      => $datos['is_active'] ?? true,
=======
                'is_active'      => true
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
            ]),

            'subscriptions' => Subscription::query()->create([
                'name'            => $datos['name'],
                'clave_prodserv'  => $datos['clave_prodserv'] ?? null, // <-- AGREGAR
                'price'           => $datos['price'],
                'duration_months' => $datos['duration_months'] ?? 1,
                'description'     => $datos['description'] ?? null,
<<<<<<< HEAD
                'is_active'       => $datos['is_active'] ?? true,
=======
                'is_active'       => true
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
            ]),

            default => throw new \Exception('Categoría no válida'),
        };
    }

    public function actualizarElemento(array $datos)
    {
        $id = $datos['id'];

        return match ($datos['category']) {
            'services' => tap(Service::query()->findOrFail($id), function ($service) use ($datos) {
                $service->update([
                    'name'           => $datos['name'],
                    'clave_prodserv' => $datos['clave_prodserv'] ?? null, // <-- AGREGAR
                    'price'          => $datos['price'],
                    'description'    => $datos['description'] ?? null,
<<<<<<< HEAD
                    'is_active'       => $datos['is_active'] ?? true,
                    'is_for_orders'  => $datos['is_for_orders'] ?? false,
=======
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
                ]);
            }),

            'supplies' => tap(Supply::query()->findOrFail($id), function ($supply) use ($datos) {
                $supply->update([
                    'name'           => $datos['name'],
                    'clave_prodserv' => $datos['clave_prodserv'] ?? null, // <-- AGREGAR
                    'price'          => $datos['price'],
                    'stock'          => $datos['stock'] ?? 0,
                    'unit'           => $datos['unit'] ?? 'Pza',
<<<<<<< HEAD
                    'is_active'       => $datos['is_active'] ?? true,
=======
>>>>>>> c50d591b03ec3e02d87b228327f0c8ed7dee8ece
                ]);
            }),

            'subscriptions' => tap(Subscription::query()->findOrFail($id), function ($subscription) use ($datos) {
                $subscription->update([
                    'name'            => $datos['name'],
                    'clave_prodserv'  => $datos['clave_prodserv'] ?? null, // <-- AGREGAR
                    'price'           => $datos['price'],
                    'duration_months' => $datos['duration_months'] ?? 1,
                    'description'     => $datos['description'] ?? null,
                    'is_active'       => $datos['is_active'] ?? true,
                ]);
            }),

            default => throw new \Exception('Categoría no válida'),
        };
    }

    public function eliminarElemento(string $category, int $id)
    {
        return match ($category) {
            'services'      => \App\Models\Service::query()->findOrFail($id)->delete(),
            'supplies'      => \App\Models\Supply::query()->findOrFail($id)->delete(),
            'subscriptions' => \App\Models\Subscription::query()->findOrFail($id)->delete(),
            default         => throw new \Exception('Categoría no válida'),
        };
    }
}