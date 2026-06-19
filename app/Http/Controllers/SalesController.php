<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SalesService;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function __construct(protected SalesService $ventaService)
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'total'       => 'required|numeric|min:0',
            'metodo_pago' => 'required|string',
            'detalles'    => 'required|array|min:1',
        ]);

        try {
            $datosVenta = $request->all();
            $datosVenta['user_id'] = Auth::id(); 

            $venta = $this->ventaService->procesarVenta($datosVenta);
            $venta->load('user');

            // Convertimos la venta a un Arreglo Puro para inyectar el nombre sin que Laravel lo borre
            $ventaArray = $venta->toArray();
            $ventaArray['nombre_vendedor'] = $venta->user ? $venta->user->name : 'Desconocido';

            return response()->json([
                'success' => true,
                'venta'   => $ventaArray
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function apiHistorial(Request $request)
    {
        $query = \App\Models\Sale::query();

        if ($request->tipo === 'dia' && $request->fecha) {
            $query->whereDate('created_at', $request->fecha);
        } elseif ($request->tipo === 'mes' && $request->fecha) {
            [$anio, $mes] = explode('-', $request->fecha);
            $query->whereYear('created_at', $anio)->whereMonth('created_at', $mes);
        } elseif ($request->tipo === 'folio' && $request->fecha) {
            $query->where('reference', 'LIKE', '%' . $request->fecha . '%');
        }

        $totalFiltro = $query->sum('total');

        $ventas = $query->with(['items', 'user'])->latest()->paginate(10)->withQueryString();

        // Convertimos toda la paginación a Arreglo Puro
        $paginacionArray = $ventas->toArray();

        // Inyectamos el nombre en cada renglón de forma directa
        foreach ($paginacionArray['data'] as &$venta) {
            if (isset($venta['user']) && $venta['user']) {
                $venta['nombre_vendedor'] = $venta['user']['name'];
            } else {
                $venta['nombre_vendedor'] = 'Desconocido';
            }
        }

        return response()->json([
            'paginacion'   => $paginacionArray,
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