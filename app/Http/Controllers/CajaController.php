<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CajaController extends Controller
{
    public function corte()
    {
        // 1. Rango de tiempo del turno (Desde las 00:00 AM de hoy hasta ahora)
        $inicioTurno = Carbon::today();
        $finTurno = Carbon::now();

        // 2. Consulta limpia usando name_snapshot de sale_items
        $desgloseServicios = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$inicioTurno, $finTurno])
            ->select(
                'sale_items.name_snapshot as servicio',
                DB::raw('SUM(sale_items.quantity) as quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_recaudado')
            )
            ->groupBy('sale_items.name_snapshot')
            ->get();

        // 3. Sumamos el total bruto de la recaudación del turno
        $totalBruto = $desgloseServicios->sum('total_recaudado');

        $fondoInicial = 500.00; // Fondo fijo

        // Calculamos los ingresos REALES de la base de datos en Efectivo
        $ingresosEfectivo = DB::table('sales')
            ->whereBetween('created_at', [$inicioTurno, $finTurno])
            ->where('payment_method', 'Efectivo')
            ->sum('total');

        // --- SISTEMA LOCAL CON SESIÓN PARA PRUEBAS ---
        // Leemos los acumulados guardados localmente en la sesión (si no existen, inician en 0.00)
        $retirosAutorizados = session('local_retiros', 0.00); 
        $gastosOperativos = session('local_gastos', 0.00);

        // Aplicamos la fórmula matemática
        $efectivoFinal = $fondoInicial + $ingresosEfectivo - $retirosAutorizados - $gastosOperativos;

        // 4. Enviamos TODO a la vista
        return view('pages.caja', compact(
            'desgloseServicios', 
            'totalBruto',
            'fondoInicial',
            'ingresosEfectivo',
            'retirosAutorizados',
            'gastosOperativos',
            'efectivoFinal'
        ));
    }

    /**
     * Procesa los gastos y retiros de forma local usando la sesión de PHP
     */
    public function movimiento(Request $request)
    {
        // 1. Validamos que los datos requeridos lleguen bien desde el formulario
        $request->validate([
            'tipo' => 'required|in:gasto,retiro',
            'monto' => 'required|numeric|min:0.01',
            'concepto_o_responsable' => 'required|string|max:255'
        ]);

        $tipo = $request->input('tipo');
        $monto = (float) $request->input('monto');

        // 2. Guardamos el acumulador de manera local en la sesión del servidor
        if ($tipo === 'gasto') {
            $actual = session('local_gastos', 0.00);
            session(['local_gastos' => $actual + $monto]);
        } else {
            $actual = session('local_retiros', 0.00);
            session(['local_retiros' => $actual + $monto]);
        }

        // 3. Respondemos con éxito al JavaScript para que actualice la interfaz sin recargar
        return response()->json([
            'success' => true,
            'message' => 'Movimiento registrado localmente',
            'monto' => $monto,
            'tipo' => $tipo
        ]);
    }

    public function generarCorte(Request $request)
    {
        $inicioTurno = Carbon::today();
        $finTurno    = Carbon::now();

        // Los mismos datos que ya usas en corte()
        $desgloseServicios = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$inicioTurno, $finTurno])
            ->select(
                'sale_items.name_snapshot as servicio',
                DB::raw('SUM(sale_items.quantity) as quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_recaudado')
            )
            ->groupBy('sale_items.name_snapshot')
            ->get();

        $totalBruto        = $desgloseServicios->sum('total_recaudado');
        $fondoInicial      = 500.00;
        $ingresosEfectivo  = DB::table('sales')
                                ->whereBetween('created_at', [$inicioTurno, $finTurno])
                                ->where('payment_method', 'Efectivo')
                                ->sum('total');
        $retirosAutorizados = session('local_retiros', 0.00);
        $gastosOperativos   = session('local_gastos', 0.00);
        $efectivoFinal      = $fondoInicial + $ingresosEfectivo - $retirosAutorizados - $gastosOperativos;

        // Efectivo contado viene del formulario de arqueo
        $efectivoContado = (float) $request->input('efectivo_real', 0);

        // Logo en base64
        $logopath = public_path('images/logo/bklogo.png');
        $logoBase64 = '';
        if (file_exists($logopath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logopath));
        }

        //Variables para fecha y hora
        $fechaCorte = now()->format('d/m/Y');
        $horaCorte = now()->format('H:i:s');

        $negocio = [
            'nombre' => 'Lavandería BunnyKlin',
            'direccion' => 'Calle 5 de Mayo, Col. Centro',
            'ciudad' => 'San Juan del Río, Qro.',
            'telefono' => '427 123 4567',
        ];

        $pdf = Pdf::loadView('caja.corte_caja', compact(
            'desgloseServicios',
            'totalBruto',
            'fondoInicial',
            'ingresosEfectivo',
            'retirosAutorizados',
            'gastosOperativos',
            'efectivoFinal',
            'efectivoContado',
            'fechaCorte',
            'horaCorte',
            'logoBase64',
            'negocio'
        ))->setPaper('a4', 'portrait');

        // Nombre del archivo con fecha y hora
        $nombreArchivo = 'corte_caja_' . now()->format('Y-m-d_H-i') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    public function facturaGlobal(Request $request)
    {
        $request->validate([
            //'payment_form' => 'required|string',
            'periodicidad' => 'required|string'
        ]);

        $inicioTurno = Carbon::today();
        $finTurno    = Carbon::now();

        // Traemos las ventas completas del turno (no sus items)
        $ventas = DB::table('sales')
            ->whereBetween('created_at', [$inicioTurno, $finTurno])
            ->where(function ($query) {
                $query->whereNull('facturapi_id')
                    ->orWhere('facturapi_id', '');
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sale_items')
                    ->whereColumn('sale_items.sale_id', 'sales.id');
            })
            ->select('id', 'reference', 'total')
            ->get();

        if ($ventas->isEmpty()) {
            return response()->json(['message' => 'No hay ventas en este turno para facturar.'], 422);
        }

        // Construimos un item por cada venta usando su reference como concepto
        $items = $ventas->map(function ($venta) {
            return [
                'quantity' => 1,
                'product'  => [
                    'description'  => 'Venta ' . $venta->reference,
                    'product_key'  => '01010101',
                    'unit_key'     => 'ACT',
                    'price'        => (float) $venta->total,
                    'tax_included' => true
                ]
            ];
        })->values()->toArray();

        $cliente = [
            'legal_name' => 'PUBLICO EN GENERAL',
            'tax_id'     => 'XAXX010101000',
            'tax_system' => '616',
            'use'        => 'S01',
            'address'    => ['zip' => '76800']
        ];

        try {
            $facturacionService = app(\App\Services\FacturacionService::class);

            $factura = $facturacionService->crearFactura(
                $cliente,
                $items,
                // $request->payment_form,
                '99',
                'PPD'
            );

            // Asignamos el ID de Facturapi a cada venta incluida en la factura
            $idsVentas = $ventas->pluck('id')->toArray();
            DB::table('sales')
                ->whereIn('id', $idsVentas)
                ->update(['facturapi_id' => $factura->id]);

            /*Código funcional comentado por pruebas    
            $apiKey   = config('services.facturapi.key');
            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->get("https://www.facturapi.io/v2/invoices/{$factura->id}/zip");

            return response($response->body(), 200, [
                'Content-Type'        => 'application/zip',
                'Content-Disposition' => 'attachment; filename="factura_global_' . now()->format('Y-m-d') . '.zip"',
            ]);*/

            // Devolvemos JSON con el ID para que el JS construya los enlaces
            return response()->json([
                'success'     => true,
                'factura_id'  => $factura->id,
                'ventas_count' => count($idsVentas)
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}