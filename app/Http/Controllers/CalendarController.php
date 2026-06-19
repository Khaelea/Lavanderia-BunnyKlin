<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\ClientSubscription;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // 1. Validamos y obtenemos mes y año. 
        $month = (int) $request->get('month', date('n'));
        $year = (int) $request->get('year', date('Y'));

        // 2. Creamos la fecha base siempre en el día 1 para evitar errores de desbordamiento
        $date = Carbon::createFromDate($year, $month, 1);

        // 3. Calculamos navegación usando copias limpias
        $prevDate = $date->copy()->subMonth();
        $nextDate = $date->copy()->addMonth();

        $data = [
            'currentMonth'     => $date->month,
            'currentMonthName' => $date->translatedFormat('F'),
            'currentYear'      => $date->year,
            'daysInMonth'      => $date->daysInMonth,
            'firstDayOfWeek'   => $date->startOfMonth()->dayOfWeek,
            
            // Navegación exacta para los botones superiores
            'prevMonth'        => $prevDate->month,
            'prevYear'         => $prevDate->year,
            'nextMonth'        => $nextDate->month,
            'nextYear'         => $nextDate->year,
            
            'today'            => Carbon::now()->day,
            'isCurrentMonth'   => ($date->isSameMonth(Carbon::now())),
            'years'            => range(Carbon::now()->year - 5, Carbon::now()->year + 5),
        ];

        $orders = Order::with('client')
            ->whereNotNull('delivery_date')
            ->orderBy('delivery_date')
            ->get()
            ->map(fn($o) => [
                'id'           => $o->id,
                'reference'    => $o->reference,
                'name'         => $o->client->name ?? 'Sin cliente',
                'service'      => $o->details ?? 'Servicio',
                'status'       => $o->status,
                'arrival_date'  => $o->arrival_date  ? \Carbon\Carbon::parse($o->arrival_date)->toDateString()  : null,
                'delivery_date' => $o->delivery_date ? \Carbon\Carbon::parse($o->delivery_date)->toDateString() : null,
                'total'        => $o->total_price,
            ]);

        $subscriptions = ClientSubscription::with(['client', 'subscription'])
            ->whereNotNull('end_date')
            ->where('status', 'active')
            ->orderBy('end_date')
            ->get()
            ->map(fn($s) => [
                'id'                  => $s->id,
                'name'                => $s->client->name ?? 'Sin cliente',
                'subscription'        => $s->subscription->name ?? 'Plan',
                'price'               => $s->subscription->price ?? 0,
                'kilos_per_month'     => $s->subscription->kilos_per_month ?? 0,
                'start_date'          => $s->start_date?->toDateString(),
                'subscriptionEndDate' => $s->end_date?->toDateString(),
                'status'              => $s->status,
            ]);

        // --- CAMBIO CLAVE AQUÍ ---
        if ($request->ajax()) {
            return response()->json([
                // Enviamos el HTML del calendario
                'html' => view('components.newcalender', compact('data'))->render(),
                // Enviamos los nuevos datos de navegación para "reprogramar" los botones externos
                'nextMonth' => $data['nextMonth'],
                'nextYear'  => $data['nextYear'],
                'prevMonth' => $data['prevMonth'],
                'prevYear'  => $data['prevYear'],
                'currentMonthName' => ucfirst($data['currentMonthName']) . ' ' . $data['currentYear']
            ]);
        }

        return view('calendar.index', compact('data', 'orders', 'subscriptions'));
    }
}