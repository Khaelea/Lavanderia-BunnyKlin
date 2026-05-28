<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Supply extends Model
{
    protected $fillable = ['name', 'clave_prodserv', 'price', 'stock', 'unit', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function salesHistory(): MorphMany
    {
        return $this->morphMany(SaleItem::class, 'item');
    }
}