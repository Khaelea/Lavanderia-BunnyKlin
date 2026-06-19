<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    // Agregamos 'reference' para que Laravel permita guardarlo
    protected $fillable = ['reference', 'client_id', 'total', 'payment_method', 'facturapi_id','user_id','corte_id',];

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
            if (! $sale->user_id && auth()->check()) {
                $sale->user_id = auth()->id();
            }

            if (! $sale->reference) {
                $lastSale = static::query()
                    ->where('reference', 'LIKE', 'BK-%')
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

    public function detalles(): HasMany
    {
        // Nota: Asegúrate de que el modelo de los items se llame 'SaleItem'
        // o cámbialo por el nombre correcto de tu modelo de detalles.
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    // EL MÉTODO CREATE VACÍO FUE ELIMINADO 🚀

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function corte(): BelongsTo
    {
        return $this->belongsTo(CorteCaja::class, 'corte_id');
    }
}
