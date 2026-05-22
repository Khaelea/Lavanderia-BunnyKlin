<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    // Campos que permitimos llenar masivamente (Mass Assignment)
    protected $fillable = [
        'name', 'phone', 'email',
        // Dirección General
        'codigo_postal', 'calle', 'numero_exterior', 'numero_interior', 'colonia', 'ciudad', 'estado',
        // Suscripción
        'subscription_id', 'end_subscription',
        // Datos Fiscales
        'rfc', 'razon_social', 'regimen_fiscal', 'same_billing_address',
        // Dirección Fiscal
        'fiscal_codigo_postal', 'fiscal_calle', 'fiscal_numero_exterior', 'fiscal_numero_interior', 'fiscal_colonia', 'fiscal_ciudad', 'fiscal_estado',
    ];

    // Casteo de datos: Le decimos a Laravel que trate este campo como un objeto Carbon (Fecha)
    protected $casts = [
        'end_subscription' => 'date',
        'same_billing_address' => 'boolean',
    ];

    // --- ACCESOR (Campo virtual) ---
    // Te permite usar $client->has_active_subscription en tu frontend o controladores
    public function getHasActiveSubscriptionAttribute(): bool
    {
        return $this->end_subscription && $this->end_subscription->isFuture();
    }

    // --- RELACIONES ---

    /**
     * Un cliente puede tener una suscripción asociada.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Un cliente puede tener muchas ventas (historial de tickets).
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Un cliente puede tener muchos pedidos operativos en el sistema.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
