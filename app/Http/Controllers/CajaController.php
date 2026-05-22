<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CajaController extends Controller
{
    public function corte()
    {
        // 1. Rango de tiempo del turno (Desde las 8:00 AM de hoy hasta ahora)
        $inicioTurno = Carbon::Today();
        $finTurno = Carbon::now();

        // 2. Consulta limpia usando name_snapshot de sale_items
        $desgloseServicios = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$inicioTurno, $finTurno])
            ->select(
                'sale_items.name_snapshot as servicio', // <-- Tu columna real de nombres
                DB::raw('SUM(sale_items.quantity) as cantidad'),
                DB::raw('SUM(sale_items.subtotal) as total_recaudado')
            )
            ->groupBy('sale_items.name_snapshot')
            ->get();

        // 3. Sumamos el total bruto de la recaudación del turno
        $totalBruto = $desgloseServicios->sum('total_recaudado');

        // 3. --- LÓGICA LOCAL PARA "CAJA - EFECTIVO" ---
        
        $fondoInicial = 500.00; // Tu fondo fijo de la imagen

        // Calculamos los ingresos REALES de la base de datos, pero SOLO si se pagaron en "Efectivo"
        $ingresosEfectivo = DB::table('sales')
            ->whereBetween('created_at', [$inicioTurno, $finTurno])
            ->where('payment_method', 'Efectivo')
            ->sum('total');

        // Simulamos valores locales para Gastos y Retiros (Puedes cambiar estos números para probar)
        $retirosAutorizados = 0.00; 
        $gastosOperativos = 0.00;

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
}