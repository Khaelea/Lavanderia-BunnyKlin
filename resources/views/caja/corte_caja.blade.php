<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        td, th { font-family: DejaVu Sans, sans-serif; }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; padding: 32px 36px; }

        /* ── ENCABEZADO ── */
        .header-tabla { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        .header-logo { width: 110px; vertical-align: middle; }
        .header-logo img { width: 95px; height: auto; }
        .header-info { vertical-align: middle; padding-left: 16px; border-left: 3px solid #1e3a8a; }
        .header-info h1 { font-size: 15px; color: #1e3a8a; font-weight: bold; margin-bottom: 4px; }
        .header-info p { font-size: 10px; color: #64748b; line-height: 1.7; }
        .header-corte { vertical-align: middle; text-align: right; }
        .header-corte .badge {
            display: inline-block; background-color: #1e3a8a; color: white;
            font-size: 10px; font-weight: bold; padding: 4px 12px;
            border-radius: 4px; margin-bottom: 8px; letter-spacing: 0.05em;
        }
        .header-corte p { font-size: 10px; color: #64748b; line-height: 1.8; }
        .header-corte .folio { color: #1e3a8a; font-weight: bold; font-size: 11px; margin-top: 2px; }

        .divisor { border: none; border-top: 1.5px solid #e2e8f0; margin: 0 0 20px 0; }

        /* ── SECCIONES ── */
        .seccion { margin-bottom: 22px; }
        .seccion-titulo {
            font-size: 9.5px; font-weight: bold; color: #ffffff;
            text-transform: uppercase; letter-spacing: 0.08em;
            background-color: #1e3a8a; padding: 5px 10px;
            margin-bottom: 0; border-radius: 4px 4px 0 0;
        }

        /* ── TABLA DE SERVICIOS ── */
        table.servicios { width: 100%; border-collapse: collapse; }
        table.servicios thead tr { background-color: #1e3a8a; }
        table.servicios thead th {
            padding: 8px 10px; text-align: left;
            font-size: 10px; color: white; font-weight: bold;
        }
        table.servicios tbody tr:nth-child(even) { background-color: #f8fafc; }
        table.servicios tbody tr:nth-child(odd) { background-color: #ffffff; }
        table.servicios tbody td {
            padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px;
        }
        table.servicios tfoot td {
            padding: 8px 10px; font-size: 11px; font-weight: bold;
            background-color: #f1f5f9; border-top: 2px solid #cbd5e1;
        }

        /* ── GRID DE RESUMEN ── */
        .resumen-grid { width: 100%; border-collapse: collapse; margin-top: 0; }
        .resumen-col-izq { width: 50%; vertical-align: top; padding-right: 10px; }
        .resumen-col-der { width: 50%; vertical-align: top; padding-left: 10px; }

        /* ── FILAS DE DATOS ── */
        .tabla-datos { width: 100%; border-collapse: collapse; }
        .tabla-datos tr { border-bottom: 1px solid #f1f5f9; }
        .tabla-datos tr:nth-child(even) { background-color: #f8fafc; }
        .tabla-datos td { padding: 7px 10px; font-size: 11px; font-family: DejaVu Sans, sans-serif; }
        .tabla-datos td:last-child { text-align: right; font-weight: 600; font-size: 11px; font-family: DejaVu Sans, sans-serif; }
        .label-dato { color: #64748b; }

        /* ── CAJA DE TOTALES ── */
        .total-box {
            background-color: #eff6ff; border: 1.5px solid #bfdbfe;
            border-radius: 6px; padding: 0; margin-top: 18px; overflow: hidden;
        }
        .total-box-titulo {
            background-color: #1e3a8a; color: white; font-size: 9.5px;
            font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em;
            padding: 5px 10px; border-radius: 4px 4px 0 0;
        }
        .tabla-totales { width: 100%; border-collapse: collapse; }
        .tabla-totales tr { border-bottom: 1px solid #dbeafe; }
        .tabla-totales tr:last-child { border-bottom: none; background-color: #dbeafe; }
        .tabla-totales td { padding: 7px 12px; font-size: 11px; }
        .tabla-totales td:last-child { text-align: right; font-weight: 700; }

        /* ── COLORES ── */
        .verde  { color: #16a34a; }
        .rojo   { color: #dc2626; }
        .azul   { color: #1e3a8a; font-weight: bold; }
        .ambar  { color: #d97706; }
        .gris   { color: #64748b; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* ── FIRMAS ── */
        .pie { margin-top: 40px; width: 100%; border-collapse: collapse; }
        .firma-col { width: 50%; text-align: center; padding-top: 48px; }
        .firma-linea { border-top: 1px solid #94a3b8; width: 65%; margin: 0 auto 6px; }
        .firma-label { font-size: 10px; color: #64748b; }

        /* ── PIE DE PÁGINA ── */
        .folio-box {
            background-color: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 4px; padding: 7px 12px; font-size: 9.5px;
            color: #94a3b8; text-align: center; margin-top: 24px;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO CON LOGO E INFO DEL NEGOCIO --}}
    <table class="header-tabla">
        <tr>
            {{-- LOGO --}}
            <td class="header-logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @else
                    <div style="width:100px; height:60px; background:#e2e8f0;
                                border-radius:6px; text-align:center;
                                font-size:9px; color:#94a3b8; padding-top:22px;">
                        Sin logo
                    </div>
                @endif
            </td>

            {{-- DATOS DEL NEGOCIO --}}
            <td class="header-info">
                <h1>{{ $negocio['nombre'] }}</h1>
                <p>{{ $negocio['direccion'] }}</p>
                <p>{{ $negocio['ciudad'] }}</p>
                <p>Tel: {{ $negocio['telefono'] }}</p>
            </td>

            {{-- DATOS DEL CORTE --}}
            <td class="header-corte">
                <div class="badge">CORTE DE CAJA</div>
                <p>Fecha: {{ $fechaCorte }}</p>
                <p>Hora de cierre: {{ $horaCorte }}</p>
                <p style="margin-top:4px; color:#1e3a8a; font-weight:bold;">
                    Folio: CC-{{ now()->format('YmdHi') }}
                </p>
            </td>
        </tr>
    </table>

    <hr class="divisor">

    {{-- DESGLOSE DE VENTAS --}}
    <div class="seccion">
        <div class="seccion-titulo">Desglose de Ventas por Servicio</div>
        <table class="servicios">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Total Recaudado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($desgloseServicios as $item)
                <tr>
                    <td>{{ $item->servicio }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">${{ number_format($item->total_recaudado, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center" style="color:#94a3b8; padding:16px;">
                        Sin ventas registradas en este turno.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total Bruto</td>
                    <td class="text-right">${{ number_format($totalBruto, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- RESUMEN FINANCIERO --}}
    <table class="resumen-grid">
        <tr>
            <td class="resumen-col-izq">
                <div class="seccion-titulo">Movimientos de Caja</div>
                <table class="tabla-datos">
                    <tr><td class="label-dato">Fondo inicial</td><td>${{ number_format($fondoInicial, 2) }}</td></tr>
                    <tr><td class="label-dato">Ingresos en efectivo</td><td class="verde">+${{ number_format($ingresosEfectivo, 2) }}</td></tr>
                    <tr><td class="label-dato">Gastos operativos</td><td class="rojo">-${{ number_format($gastosOperativos, 2) }}</td></tr>
                    <tr><td class="label-dato">Retiros autorizados</td><td class="rojo">-${{ number_format($retirosAutorizados, 2) }}</td></tr>
                    <tr><td class="label-dato">Pagos digitales</td><td>${{ number_format($totalBruto - $ingresosEfectivo, 2) }}</td></tr>
                </table>
            </td>
            <td class="resumen-col-der">
                <div class="seccion-titulo">Resultado del Arqueo</div>
                @php $diferencia = $efectivoContado - $efectivoFinal; @endphp
                <table class="tabla-datos">
                    <tr><td class="label-dato">Efectivo esperado</td><td>${{ number_format($efectivoFinal, 2) }}</td></tr>
                    <tr><td class="label-dato">Efectivo contado</td><td>${{ number_format($efectivoContado, 2) }}</td></tr>
                    <tr>
                        <td class="label-dato">Diferencia</td>
                        <td>
                            @if(abs($diferencia) < 0.01)
                                <span class="verde">$0.00 (Cuadrada)</span>
                            @elseif($diferencia > 0)
                                <span class="ambar">+${{ number_format($diferencia, 2) }} (Sobrante)</span>
                            @else
                                <span class="rojo">-${{ number_format(abs($diferencia), 2) }} (Faltante)</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- TOTAL NETO --}}
    <div class="total-box">
        <div class="total-box-titulo">Resumen Final del Turno</div>
        <table class="tabla-totales">
            <tr>
                <td class="gris">Total Bruto del Turno</td>
                <td class="azul">${{ number_format($totalBruto, 2) }}</td>
            </tr>
            <tr>
                <td class="gris">Total Gastos y Salidas</td>
                <td class="rojo">-${{ number_format($gastosOperativos + $retirosAutorizados, 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Neto del Turno</td>
                <td class="azul">${{ number_format($totalBruto - $gastosOperativos - $retirosAutorizados, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- FIRMAS --}}
    <table class="pie">
        <tr>
            <td class="firma-col">
                <div class="firma-linea"></div>
                <div class="firma-label">Cajero Responsable</div>
            </td>
            <!--
            <td class="firma-col">
                <div class="firma-linea"></div>
                <div class="firma-label">Supervisor / Autoriza</div>
            </td>
            -->
        </tr>
    </table>

    {{-- FOLIO AL PIE --}}
    <div class="folio-box">
        Documento generado el {{ $fechaCorte }} a las {{ $horaCorte }} &nbsp;|&nbsp;
        {{ $negocio['nombre'] }} &nbsp;|&nbsp; {{ $negocio['ciudad'] }}
    </div>

</body>
</html>