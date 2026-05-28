<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Subscription extends Model
{
    protected $fillable = ['name', 'clave_prodserv', 'price', 'duration_months', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function salesHistory(): MorphMany
    {
        return $this->morphMany(SaleItem::class, 'item');
    }
}