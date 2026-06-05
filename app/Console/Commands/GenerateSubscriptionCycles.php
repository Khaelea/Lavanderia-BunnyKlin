<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClientSubscription;
use Carbon\Carbon;

class GenerateSubscriptionCycles extends Command
{
    protected $signature = 'subscriptions:generate-cycles';
    protected $description = 'Genera el siguiente ciclo mensual para contratos activos que lo requieran';

    public function handle()
    {
        $today = Carbon::today();

        // 1. Buscamos contratos activos cuya fecha de fin total aún no llegue
        $contratos = ClientSubscription::with('plan')
            ->where('status', 'active')
            ->where('end_date', '>', $today)
            ->get();

        $creados = 0;

        foreach ($contratos as $contrato) {
            // 2. Obtenemos el ciclo más reciente de este contrato
            $ultimoCiclo = $contrato->cycles()->latest('cycle_end')->first();

            // 3. Si el último ciclo ya caducó (su fecha final es hoy o antes)
            if ($ultimoCiclo && Carbon::parse($ultimoCiclo->cycle_end)->lte($today)) {

                $nuevoInicio = Carbon::parse($ultimoCiclo->cycle_end);
                $nuevoFin = $nuevoInicio->copy()->addMonthNoOverflow();

                // 4. Tope de seguridad: El ciclo no puede durar más que el contrato total
                if ($nuevoFin->greaterThan(Carbon::parse($contrato->end_date))) {
                    $nuevoFin = Carbon::parse($contrato->end_date);
                }

                // 5. Creamos el mes 2 (o mes 3) con sus kilos renovados
                $contrato->cycles()->create([
                    'cycle_start'    => $nuevoInicio->toDateString(),
                    'cycle_end'      => $nuevoFin->toDateString(),
                    'kilos_allowed'  => $contrato->plan->kilos_per_month,
                    'kilos_consumed' => 0,
                ]);

                $creados++;
            }
        }

        $this->info("Revisión completada. Se generaron {$creados} nuevos ciclos.");
    }

}
