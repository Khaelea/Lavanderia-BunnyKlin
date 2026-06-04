<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacturacionService;
use App\Models\Sale;

class FacturaController extends Controller
{
    protected $facturacion;

    public function __construct(FacturacionService $facturacion)
    {
        $this->facturacion = $facturacion;
    }

    public function create()
    {
        // Traemos las ventas con sus detalles (Relación definida en el modelo Sale)
        $ventas = Sale::with('detalles')
                    ->latest()
                    ->take(10)
                    ->get();

        return view('factura.crear', compact('ventas'));
    }

    public function facturar(Request $request)
    {
        // 1. Validaciones de entrada
        $request->validate([
            'venta_data'     => 'required|string',
            'legal_name'     => 'required|string|min:3',
            'tax_id'         => 'required|string',
            'tax_system'     => 'required',
            'use_cfdi'       => 'required', 
            'payment_method' => 'required', 
            'payment_form'   => 'required', 
            'email'          => 'required|email',
            'zip'            => 'required|digits:5',
        ]);

        // 2. Decodificamos la venta enviada por AlpineJS
        $venta = json_decode($request->venta_data);

        // Validamos que existan detalles (usamos 'detalles' para ser consistentes)
        if (!$venta || !isset($venta->detalles)) {
            return back()->withErrors(['error' => 'No has seleccionado una venta válida o la venta no tiene productos.'])->withInput();
        }

        // 3. Lógica para CFDI 4.0 y RFC Genérico
        $taxId = trim(strtoupper($request->tax_id));
        $isGeneric = ($taxId === 'XAXX010101000');

        $taxSystem = $isGeneric ? '616' : $request->tax_system;
        $useCfdi   = $isGeneric ? 'S01' : $request->use_cfdi;

        $cliente = [
            "legal_name" => strtoupper($request->legal_name),
            "tax_id"     => $taxId,
            "tax_system" => $taxSystem,
            "use"        => $useCfdi, // El Service se encargará de mover esto a la raíz
            "email"      => $request->email,
            "address"    => ["zip" => $request->zip]
        ];

        // 4. Mapeamos los items de la venta
        $items = [];
        foreach ($venta->detalles as $item) {

            // 1. DESCRIPCIÓN
            $description = $item->name_snapshot ?? ($item->name ?? 'Servicio de Lavandería');

            // 2. CLAVE SAT — la buscamos directo en la BD usando item_type e item_id
            $productKey = '91111502'; // fallback por defecto
            if (isset($item->item_type) && isset($item->item_id)) {
                $modelo = match($item->item_type) {
                    'App\\Models\\Service'      => \App\Models\Service::find($item->item_id),
                    'App\\Models\\Supply'       => \App\Models\Supply::find($item->item_id),
                    'App\\Models\\Subscription' => \App\Models\Subscription::find($item->item_id),
                    default => null
                };
                $productKey = $modelo?->clave_prodserv ?? '91111502';
            }

            // 3. UNIDAD
            $unitKey = $modelo?->unit ?? 'E48';

            $items[] = [
                "quantity" => $item->quantity,
                "product"  => [
                    "description"  => $description,
                    "product_key"  => (string) $productKey,
                    "unit_key"     => (string) $unitKey,
                    "price"        => $item->price_snapshot ?? ($item->price ?? 0),
                    "tax_included" => true
                ]
            ];
        }

        // dd([
        //     'cliente' => $cliente,
        //     'productos' => $items,
        //     'pago' => $request->payment_form
        // ]);

        try {
            // 5. Enviamos a Facturapi usando tu Service
            $factura = $this->facturacion->crearFactura(
                $cliente, 
                $items, 
                $request->payment_form, 
                $request->payment_method
            );

            // Guardamos el ID de Facturapi en la venta
            $ventaId = json_decode($request->venta_data)->id;
            \App\Models\Sale::where('id', $ventaId)->update([
                'facturapi_id' => $factura->id
            ]);
            
            // URL de descarga directa para evitar login de Facturapi
            //$pdfUrl = "https://www.facturapi.io/v2/invoices/{$factura->id}/pdf";
            
            return redirect()->route('factura.crear')->with(
                'success',
                '¡Factura creada con éxito! ' .
                '<a href="' . route('factura.archivo', ['id' => $factura->id, 'tipo' => 'pdf']) . '" target="_blank" style="color:blue; text-decoration:underline; font-weight:bold;">Ver PDF</a> · ' .
                '<a href="' . route('factura.archivo', ['id' => $factura->id, 'tipo' => 'zip']) . '" style="color:green; text-decoration:underline; font-weight:bold;">Descargar ZIP (PDF + XML)</a>'
            );

        } catch (\Exception $e) {
            return redirect()->route('factura.crear')
                ->withErrors(['error' => 'Error de Facturapi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function descargarArchivo($id, $tipo = 'pdf')
    {
        $apiKey = config('services.facturapi.key');

        $tipo = in_array($tipo, ['pdf', 'zip']) ? $tipo : 'pdf';

        $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
            ->get("https://www.facturapi.io/v2/invoices/{$id}/{$tipo}");

        if (!$response->successful()) {
            abort(404, 'No se pudo obtener el archivo');
        }

        $contentType = $tipo === 'pdf' ? 'application/pdf' : 'application/zip';
        $extension   = $tipo === 'pdf' ? 'pdf' : 'zip';

        return response($response->body(), 200, [
            'Content-Type'        => $contentType,
            'Content-Disposition' => 'inline; filename="factura-' . $id . '.' . $extension . '"',
        ]);
    }
}