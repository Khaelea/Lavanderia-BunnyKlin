<?php

namespace App\Services;

use Facturapi\Facturapi;

class FacturacionService
{
    protected $facturapi;

    public function __construct()
    {
        // Instanciamos Facturapi con la llave que pusimos en el .env
        $this->facturapi = new Facturapi(config('services.facturapi.key'));
    }

    public function crearFactura(array $cliente, array $items, $formaPago, string $metodoPago)
    {
        // 1. Extraer y limpiar el Uso de CFDI
        $usoCFDI = $cliente['use'] ?? 'S01'; 
        unset($cliente['use']);

        // 2. FORMATEO CRÍTICO: Forzamos que la forma de pago sea texto de 2 dígitos
        // Si llega "1", se convierte en "01". Si llega 3, se convierte en "03".
        $formaPagoLimpia = sprintf("%02d", (int)$formaPago);

        try {
            $resultado = $this->facturapi->Invoices->create([
                "customer"       => $cliente,
                "items"          => $items,
                "payment_form"   => $formaPagoLimpia, // Aseguramos que tenga 2 dígitos
                "payment_method" => $metodoPago,
                "use"            => $usoCFDI, // Uso de CFDI
                "type"           => "I", // I = Ingreso, E = Egreso, N = Nómina
                //"dispatch"     => true // Esto la envía por correo automáticamente
            ]);

            // Verificamos que realmente devolvió algo con ID
            // Intentar comentar para ver si Hostgator no tiene problemas con la respuesta de Facturapi, ya que a veces devuelve un objeto sin ID pero con la factura creada. Si esto causa problemas, se puede ajustar la lógica para manejar ambos casos.
            if (!$resultado || !isset($resultado->id)) {
                throw new \Exception("Facturapi no devolvió una factura válida. Respuesta: " . json_encode($resultado));
            }

            return $resultado;

        } catch (\Exception $e) {
            throw new \Exception("Error en Facturapi: " . $e->getMessage());
        }
    }
}