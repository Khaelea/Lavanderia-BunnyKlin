<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Service extends Model
{
    protected $fillable = ['name', 'clave_prodserv', 'price', 'description', 'is_active', 'is_for_orders',];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'is_for_orders' => 'boolean',
    ];

    public function salesHistory(): MorphMany
    {
        return $this->morphMany(SaleItem::class, 'item');
    }
}
