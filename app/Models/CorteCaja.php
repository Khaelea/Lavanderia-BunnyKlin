<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorteCaja extends Model
{
    protected $table = 'cortes_caja';

    protected $fillable = [
        'folio',
        'fecha_cierre',
        'fondo_inicial',
        'total_ingresos',
        'total_gastos',
        'total_retiros',
        'efectivo_esperado',
        'efectivo_contado',
        'diferencia',
        'facturado',
        'facturapi_id',
    ];

    protected $casts = [
        'facturado'         => 'boolean',
        'fecha_cierre'      => 'datetime',
        'fondo_inicial'     => 'decimal:2',
        'total_ingresos'    => 'decimal:2',
        'total_gastos'      => 'decimal:2',
        'total_retiros'     => 'decimal:2',
        'efectivo_esperado' => 'decimal:2',
        'efectivo_contado'  => 'decimal:2',
        'diferencia'        => 'decimal:2',
    ];

    // En CorteCaja.php
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'corte_id');
    }

    // Helpers para filtrar por tipo
    public function gastos()
    {
        return $this->movimientos()->where('tipo', 'gasto');
    }

    public function retiros()
    {
        return $this->movimientos()->where('tipo', 'retiro');
    }
}