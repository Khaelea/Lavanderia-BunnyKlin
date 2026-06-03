<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SalesService;

class SalesController extends Controller
{
    public function __construct(protected SalesService $ventaService)
    {
    }

    public function store(Request $request)
    {
        // Validamos que venga el carrito y el total
        $request->validate([
            'total'       => 'required|numeric|min:0',
            'metodo_pago' => 'required|string',
            'detalles'    => 'required|array|min:1',
        ]);

        try {
            $venta = $this->ventaService->procesarVenta($request->all());

            return response()->json([
                'success' => true,
                'venta'   => $venta
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function apiHistorial(Request $request)
    {
        $query = \App\Models\Sale::query();

        // Filtrar por fecha si el usuario lo solicita desde Alpine
        if ($request->tipo === 'dia' && $request->fecha) {
            $query->whereDate('created_at', $request->fecha);
        } elseif ($request->tipo === 'mes' && $request->fecha) {
            // El input type="month" de HTML manda el formato "YYYY-MM"
            [$anio, $mes] = explode('-', $request->fecha);
            $query->whereYear('created_at', $anio)->whereMonth('created_at', $mes);
        } elseif ($request->tipo === 'folio' && $request->fecha) {
            $query->where('reference', 'LIKE', '%' . $request->fecha . '%');
        }

        // Calcular el total REAL de la consulta antes de paginar (de todos los registros)
        $totalFiltro = $query->sum('total');

        // Paginar los resultados. ->withQueryString() recuerda los parámetros (?tipo=dia...) en los botones de "Siguiente"
        $ventas = $query->with('items')->latest()->paginate(10)->withQueryString();

        // Devolvemos el paginador y la suma total estructurados
        return response()->json([
            'paginacion'   => $ventas,
            'total_filtro' => $totalFiltro
        ]);
    }

    public function destroy($id)
    {
        try {
            $this->ventaService->eliminarVenta($id);

            return response()->json([
                'success' => true,
                'message' => 'Venta eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroyBulk(Request $request)
    {
        // Validamos que nos manden un arreglo de IDs y que existan en la tabla sales
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:sales,id'
        ]);

        try {
            $this->ventaService->eliminarVentasMasivas($request->ids);

            return response()->json([
                'success' => true,
                'message' => 'Ventas eliminadas correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
