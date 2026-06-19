<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CatalogoService;

class CatalogoController extends Controller
{
    public function __construct(protected CatalogoService $catalogoService)
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'name'   => 'required|string|max:100',
            'price'   => 'required|numeric|min:0',
            'clave_prodserv' => 'nullable|string|max:8', // Validación para clave SAT
        ]);

        try {
            $item = $this->catalogoService->guardarElemento($request->all());

            return response()->json([
                'success' => true,
                'item'    => $item
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer',
            'category' => 'required|string',
            'name'     => 'required|string|max:100',
            'price'    => 'required|numeric|min:0',
            'clave_prodserv' => 'nullable|string|max:8', // Validación para clave SAT
        ]);

        try {
            $item = $this->catalogoService->actualizarElemento($request->all());

            return response()->json([
                'success' => true,
                'item'    => $item
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer',
            'category' => 'required|string',
        ]);

        try {
            // Llamamos al servicio pasando la categoría y el ID
            $this->catalogoService->eliminarElemento($request->category, $request->id);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function toggleEstado(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'category' => 'required|string|in:services,supplies,subscriptions,extras',
            'is_active' => 'required|boolean'
        ]);

        // Determinamos el modelo correcto según la categoría
        $modelo = match ($request->category) {
            'services' => \App\Models\Service::class,
            'supplies' => \App\Models\Supply::class,
            'subscriptions' => \App\Models\Subscription::class,
        };

        // Buscamos el elemento y actualizamos solo esa columna
        $elemento = $modelo::findOrFail($request->id);
        $elemento->update(['is_active' => $request->is_active]);

        return response()->json(['success' => true, 'is_active' => $elemento->is_active]);
    }
}
