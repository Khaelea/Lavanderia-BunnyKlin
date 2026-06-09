<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    protected $fillable = [
        'corte_id',
        'tipo',
        'monto',
        'concepto_o_responsable',
        'fecha_turno',
    ];

    protected $casts = [
        'monto'       => 'decimal:2',
        'fecha_turno' => 'date',
    ];

    // Scope para filtrar por turno actual
    public function scopeDelTurno($query)
    {
        return $query->where('fecha_turno', today());
    }

    public function scopeGastos($query)
    {
        return $query->where('tipo', 'gasto');
    }

    public function scopeRetiros($query)
    {
        return $query->where('tipo', 'retiro');
    }

    // Relación con el corte
    public function corte()
    {
        return $this->belongsTo(CorteCaja::class, 'corte_id');
    }
}