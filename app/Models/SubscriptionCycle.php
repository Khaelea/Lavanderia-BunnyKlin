<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionCycle extends Model
{
    protected $fillable = [
        'client_subscription_id',
        'cycle_start',
        'cycle_end',
        'kilos_allowed',
        'kilos_consumed'
    ];

    protected $casts = [
        'cycle_start' => 'date',
        'cycle_end' => 'date',
        'kilos_allowed' => 'decimal:2',
        'kilos_consumed' => 'decimal:2',
    ];

    public function clientSubscription()
    {
        return $this->belongsTo(ClientSubscription::class);
    }

    // Helper para saber si al cliente aún le quedan kilos gratis este mes
    public function hasAvailableKilos(): bool
    {
        return $this->kilos_consumed < $this->kilos_allowed;
    }

    public function remainingKilos(): float
    {
        return max(0, $this->kilos_allowed - $this->kilos_consumed);
    }
}
