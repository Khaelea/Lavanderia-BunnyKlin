<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    
}