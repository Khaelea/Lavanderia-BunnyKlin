<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClientSubscription;
use Carbon\Carbon;

class ExpireSubscriptions extends Command
{
    // El nombre que usarás en la terminal para ejecutarlo
    protected $signature = 'subscriptions:expire';

    // Descripción para cuando corras php artisan list
    protected $description = 'Marca como expirados los contratos de suscripción cuya fecha de fin ya pasó';

    public function handle()
    {
        $this->info('Iniciando revisión de suscripciones expiradas...');

        // Buscamos contratos 'active' cuya fecha de fin estricta sea menor a hoy.
        // Usamos update() directamente en la consulta para que se ejecute
        // en un solo query de MySQL, lo cual es rapidísimo.
        $actualizados = ClientSubscription::query()
            ->where('status', 'active')
            ->whereDate('end_date', '<', Carbon::today())
            ->update(['status' => 'expired']);

        if ($actualizados > 0) {
            $this->info("¡Listo! Se han marcado {$actualizados} suscripciones como expiradas.");
        } else {
            $this->info('Todo en orden. No se encontraron suscripciones expiradas hoy.');
        }
    }

}
