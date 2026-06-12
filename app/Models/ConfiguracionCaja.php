<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionCaja extends Model
{
    protected $table = 'configuracion_caja';

    protected $fillable = [
        'fondo_inicial',
        'nombre_negocio',
        'direccion',
        'ciudad',
        'telefono',
        'codigo_postal'
    ];

    protected $casts = [
        'fondo_inicial' => 'decimal:2',
    ];

    /**
     * Siempre devuelve la única fila de configuración.
     * Si no existe, la crea con valores por defecto.
     */
    public static function obtener(): self
    {
        return self::firstOrCreate([], [
            'fondo_inicial'  => 0.00,
            'nombre_negocio' => 'Lavandería BunnyKlin',
            'direccion'      => 'Calle 5 de Mayo, Col. Centro',
            'ciudad'         => 'San Juan del Río, Qro.',
            'telefono'       => '427 123 4567',
            'codigo_postal'  => '76800',
        ]);
    }
}