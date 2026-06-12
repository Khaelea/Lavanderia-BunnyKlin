<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    protected $fillable = [
        'user_id',
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
        return $query->where('user_id', auth()->id())
                     ->whereNull('corte_id');
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

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}