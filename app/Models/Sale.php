<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    // Agregamos 'reference' para que Laravel permita guardarlo
    protected $fillable = ['reference', 'client_id', 'total', 'payment_method'];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (! $sale->reference) {
                // Aquí ya usamos transacciones, pero como tu Service ya envuelve
                // todo en una transacción, esta funcionará perfecto.
                $lastSale = static::query()
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();

                $nextNumber = 1;

                if ($lastSale && $lastSale->reference) {
                    $lastNumber = intval(str_replace('BK-', '', $lastSale->reference));
                    $nextNumber = $lastNumber + 1;
                }

                $sale->reference = 'BK-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function detalles()
    {
        // Nota: Asegúrate de que el modelo de los items se llame 'SaleItem' 
        // o cámbialo por el nombre correcto de tu modelo de detalles.
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    // EL MÉTODO CREATE VACÍO FUE ELIMINADO 🚀
}