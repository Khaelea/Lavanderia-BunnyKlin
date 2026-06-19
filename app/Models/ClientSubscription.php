<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSubscription extends Model
{
    protected $table = 'client_subscription';

    protected $fillable = [
        'client_id',
        'subscription_id',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan()
    {
        // Le llamamos 'plan' a la relación para no confundir con la clase
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function cycles()
    {
        return $this->hasMany(SubscriptionCycle::class);
    }

    // Helper vital: Trae exactamente el ciclo/mes en el que estamos hoy
    public function currentCycle()
    {
        return $this->hasOne(SubscriptionCycle::class)
            ->where('cycle_start', '<=', now())
            ->where('cycle_end', '>=', now());
    }
}
