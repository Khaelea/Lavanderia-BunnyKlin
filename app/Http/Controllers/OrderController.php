<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function apiInit()
    {
        // Traemos las órdenes con su cliente y venta asociados
        $orders = Order::with(['client', 'sale', 'service'])->latest()->get();

        $services = \App\Models\Service::query()
            ->where('is_for_orders', true)
            ->where('is_active', true)
            ->get(['id', 'name', 'price']);

        return response()->json([
            'orders' => $orders,
            'services' => $services,
        ]);
    }

    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'client_id'    => 'nullable|exists:clients,id',
            'name'         => 'required|string',
            'phone'        => 'nullable|string',
            'service_id'   => 'required|exists:services,id',
            'quantity'     => 'required|numeric|min:0.1',
            'details'      => 'nullable|string',
            'total'        => 'required|numeric|min:0',
            'advance'      => 'nullable|numeric|min:0|lte:total',
            'status'       => 'required|string',
            'arrivalDate'  => 'required|date',
            'deliveryDate' => 'nullable|date',
        ]);

        $ultimoEncargo = \App\Models\Order::query()->latest('id')->first();

        $siguienteNumero = $ultimoEncargo ? $ultimoEncargo->id + 1 : 1;

        $datosValidados['reference'] = 'ORD-' . str_pad($siguienteNumero, 5, '0', STR_PAD_LEFT);

        $order = $this->orderService->guardarOrden($datosValidados);
        $order->load(['client', 'sale']); // Cargamos relaciones

        return response()->json(['success' => true, 'order' => $order]);
    }

    public function update(Request $request, Order $order)
    {
        $datosValidados = $request->validate([
            'name'         => 'required|string',
            'phone'        => 'nullable|string',
            'service_id'   => 'required|exists:services,id',
            'quantity'     => 'required|numeric|min:0.1',
            'details'      => 'nullable|string',
            'total'        => 'required|numeric|min:0',
            'advance'      => 'nullable|numeric|min:0',
            'status'       => 'required|string',
            'arrivalDate'  => 'required|date',
            'deliveryDate' => 'nullable|date',
        ]);

        $order = $this->orderService->guardarOrden($datosValidados, $order);
        $order->load(['client', 'sale']);

        return response()->json(['success' => true, 'order' => $order]);
    }

    public function destroy(Order $order)
    {
        $this->orderService->eliminarOrden($order);
        return response()->json(['success' => true]);
    }
}
