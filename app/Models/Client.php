<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    // Campos que permitimos llenar masivamente (Mass Assignment)
    protected $fillable = [
        'name', 'phone', 'email',
        // Dirección General
        'codigo_postal', 'calle', 'numero_exterior', 'numero_interior', 'colonia', 'ciudad', 'estado',
        // Datos Fiscales
        'rfc', 'razon_social', 'regimen_fiscal', 'same_billing_address',
        // Dirección Fiscal
        'fiscal_codigo_postal', 'fiscal_calle', 'fiscal_numero_exterior', 'fiscal_numero_interior', 'fiscal_colonia', 'fiscal_ciudad', 'fiscal_estado',
    ];

    // Casteo de datos: Le decimos a Laravel que trate este campo como un objeto Carbon (Fecha)
    protected $casts = [
        'same_billing_address' => 'boolean',
    ];

    protected $appends = [
        'has_active_subscription',
        'subscription_name',
        'subscription_id',
        'start_subscription',
        'end_subscription'
    ];

    // --- ACCESOR (Campo virtual) ---
    // Te permite usar $client->has_active_subscription en tu frontend o controladores
    public function getHasActiveSubscriptionAttribute(): bool
    {
        return $this->currentSubscription !== null;
    }

    // --- ACCESORES DE COMPATIBILIDAD CON EL FRONTEND ---

    public function getSubscriptionNameAttribute()
    {
        // Si tiene suscripción activa, muestra ese nombre. Si no, muestra el de la última que tuvo.
        return $this->currentSubscription?->plan?->name
            ?? $this->latestSubscription?->plan?->name;
    }

    public function getEndSubscriptionAttribute()
    {
        // Si tiene suscripción activa, muestra su fin. Si no, muestra cuándo venció la última.
        return $this->currentSubscription?->end_date
            ?? $this->latestSubscription?->end_date;
    }

    public function getSubscriptionIdAttribute()
    {
        return $this->currentSubscription?->subscription_id;
    }

    public function getStartSubscriptionAttribute()
    {
        return $this->currentSubscription?->start_date;
    }

    // --- RELACIONES ---

    /**
     * Un cliente puede tener un historial de varios contratos/suscripciones.
     */
    public function clientSubscriptions(): HasMany
    {
        return $this->hasMany(ClientSubscription::class);
    }

    /**
     * Helper: Trae ÚNICAMENTE el contrato activo y vigente actualmente.
    */
    public function currentSubscription(): HasOne
    {
        return $this->hasOne(ClientSubscription::class)
            ->where('status', 'active')
            ->whereDate('end_date', '>=', today())
            ->latestOfMany();
    }

    public function latestSubscription(): HasOne
    {
        return $this->hasOne(ClientSubscription::class)->latestOfMany();
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
