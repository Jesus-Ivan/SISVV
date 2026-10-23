<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibos de Nómina</title>
    <style>
        @page {
            size: letter portrait;
            margin: 20pt 25pt;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #1f2937;
            line-height: 1.15;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .receipt-card {
            height: 340pt;
            box-sizing: border-box;
            position: relative;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 3pt;
            margin-bottom: 4pt;
        }
        .header-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            font-size: 6.5pt;
            color: #4b5563;
        }
        .badge-tipo {
            display: inline-block;
            background: #1e3a8a;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 2pt 5pt;
            border-radius: 3pt;
        }

        .info-table {
            width: 100%;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            padding: 3pt;
            border-radius: 3pt;
            margin-bottom: 4pt;
        }
        .info-label {
            font-size: 6.5pt;
            color: #6b7280;
            text-transform: uppercase;
        }
        .info-val {
            font-size: 7.5pt;
            font-weight: bold;
            color: #111827;
        }

        .concepts-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3pt;
        }
        .concepts-table th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-size: 6.5pt;
            padding: 2pt 3pt;
            text-transform: uppercase;
            border: 1px solid #d1d5db;
        }
        .concepts-table td {
            font-size: 7pt;
            padding: 2pt 3pt;
            border: 1px solid #e5e7eb;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3pt;
        }
        .neto-box {
            background-color: #eff6ff;
            border: 1.5px solid #1e3a8a;
            padding: 3pt;
            border-radius: 3pt;
            text-align: right;
        }
        .neto-amount {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
        }

        .obs-box {
            border: 1px dashed #9ca3af;
            background-color: #fafafa;
            padding: 2pt 4pt;
            border-radius: 2pt;
            font-size: 6.5pt;
            margin-bottom: 3pt;
        }

        .firma-container {
            margin-top: 4pt;
            width: 100%;
        }
        .leyenda {
            font-size: 5.5pt;
            color: #4b5563;
            text-align: justify;
            margin-bottom: 6pt;
        }
        .linea-firma {
            border-top: 1px solid #111827;
            width: 55%;
            margin: 0 auto;
            text-align: center;
            padding-top: 2pt;
            font-size: 6.5pt;
            font-weight: bold;
        }

        .divider {
            height: 25pt;
            border-bottom: 1px dashed #9ca3af;
            margin-bottom: 15pt;
            text-align: center;
            line-height: 25pt;
            font-size: 8pt;
            color: #9ca3af;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach($recibos->chunk(2) as $chunk)
    @foreach($chunk as $index => $recibo)
        <div class="receipt-card">
            <!-- Header -->
            <table class="header-table">
                <tr>
                    <td style="width: 70%;">
                        <div class="header-title">{{ $empresa['nombre'] }}</div>
                        <div class="header-subtitle">RFC: {{ $empresa['rfc'] }} | Tel: {{ $empresa['telefono'] }}</div>
                        <div class="header-subtitle">{{ $empresa['direccion'] }}</div>
                    </td>
                    <td style="width: 30%;" class="text-right">
                        <span class="badge-tipo">
                            @if($recibo->tipo_recibo === 'FINIQUITO')
                                FINIQUITO Y LIQUIDACIÓN
                            @elseif($recibo->tipo_recibo === 'AGUINALDO')
                                RECIBO DE AGUINALDO
                            @else
                                RECIBO DE NÓMINA
                            @endif
                        </span>
                        <div style="font-size: 7pt; color: #4b5563; margin-top: 2pt;">FOLIO: <strong>{{ $recibo->folio }}</strong></div>
                        <div style="font-size: 6.5pt; color: #6b7280;">Emisión: {{ $recibo->fecha_pago ? $recibo->fecha_pago->format('d/m/Y') : date('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>

            <!-- Datos del Empleado y Periodo -->
            <table class="info-table">
                <tr>
                    <td style="width: 15%;">
                        <div class="info-label">No. Empleado</div>
                        <div class="info-val">#{{ $recibo->id_empleado_snapshot }}</div>
                    </td>
                    <td style="width: 45%;">
                        <div class="info-label">Nombre del Trabajador</div>
                        <div class="info-val">{{ $recibo->nombre_empleado_snapshot }}</div>
                    </td>
                    <td style="width: 20%;">
                        <div class="info-label">Puesto</div>
                        <div class="info-val">{{ $recibo->puesto_snapshot ?: 'N/A' }}</div>
                    </td>
                    <td style="width: 20%;">
                        <div class="info-label">Área</div>
                        <div class="info-val">{{ $recibo->area_snapshot ?: 'N/A' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="info-label">Tipo</div>
                        <div class="info-val">{{ $recibo->tipo_empleado_snapshot ?: 'PLANTA' }}</div>
                    </td>
                    <td>
                        <div class="info-label">Periodo de Pago</div>
                        <div class="info-val">Del {{ $recibo->fecha_inicio->format('d/m/Y') }} al {{ $recibo->fecha_fin->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        <div class="info-label">Método de Pago</div>
                        <div class="info-val">{{ $recibo->metodo_pago }}</div>
                    </td>
                    <td>
                        <div class="info-label">Estatus</div>
                        <div class="info-val">{{ $recibo->estado_pago }}</div>
                    </td>
                </tr>
            </table>

            <!-- Tabla de Percepciones y Deducciones -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 3pt;">
                <tr>
                    <!-- Percepciones -->
                    <td style="width: 50%; vertical-align: top; padding-right: 3pt;">
                        <table class="concepts-table">
                            <thead>
                                <tr>
                                    <th>Percepciones</th>
                                    <th class="text-right" style="width: 35%;">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($recibo->nomina_fiscal > 0)
                                    <tr>
                                        <td>Sueldo / Nómina Fiscal</td>
                                        <td class="text-right">${{ number_format($recibo->nomina_fiscal, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->diferencia_efectivo > 0)
                                    <tr>
                                        <td>Diferencia en Efectivo</td>
                                        <td class="text-right">${{ number_format($recibo->diferencia_efectivo, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->horas_extras > 0)
                                    <tr>
                                        <td>Horas Extras</td>
                                        <td class="text-right">${{ number_format($recibo->horas_extras, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->bonos_otros > 0)
                                    <tr>
                                        <td>Bonos / Compensaciones</td>
                                        <td class="text-right">${{ number_format($recibo->bonos_otros, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->monto_aguinaldo > 0)
                                    <tr>
                                        <td>Aguinaldo</td>
                                        <td class="text-right">${{ number_format($recibo->monto_aguinaldo, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->monto_vacaciones > 0)
                                    <tr>
                                        <td>Vacaciones</td>
                                        <td class="text-right">${{ number_format($recibo->monto_vacaciones, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->monto_prima_vacacional > 0)
                                    <tr>
                                        <td>Prima Vacacional</td>
                                        <td class="text-right">${{ number_format($recibo->monto_prima_vacacional, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->total_percepciones == 0)
                                    <tr>
                                        <td>Sin percepciones</td>
                                        <td class="text-right">$0.00</td>
                                    </tr>
                                @endif
                                <tr style="background-color: #f9fafb; font-weight: bold;">
                                    <td>TOTAL PERCEPCIONES</td>
                                    <td class="text-right">${{ number_format($recibo->total_percepciones, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>

                    <!-- Deducciones -->
                    <td style="width: 50%; vertical-align: top; padding-left: 3pt;">
                        <table class="concepts-table">
                            <thead>
                                <tr>
                                    <th>Deducciones</th>
                                    <th class="text-right" style="width: 35%;">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($recibo->descuento_adelanto > 0)
                                    <tr>
                                        <td>Amortización de Adelanto</td>
                                        <td class="text-right">${{ number_format($recibo->descuento_adelanto, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->descuento_infonavit > 0)
                                    <tr>
                                        <td>Infonavit</td>
                                        <td class="text-right">${{ number_format($recibo->descuento_infonavit, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->otros_descuentos > 0)
                                    <tr>
                                        <td>Otros Descuentos / Ajustes</td>
                                        <td class="text-right">${{ number_format($recibo->otros_descuentos, 2) }}</td>
                                    </tr>
                                @endif
                                @if($recibo->total_deducciones == 0)
                                    <tr>
                                        <td style="color: #9ca3af;">Sin deducciones</td>
                                        <td class="text-right">$0.00</td>
                                    </tr>
                                @endif
                                <tr style="background-color: #f9fafb; font-weight: bold;">
                                    <td>TOTAL DEDUCCIONES</td>
                                    <td class="text-right">${{ number_format($recibo->total_deducciones, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Caja de Neto a Pagar -->
            <table class="totals-table">
                <tr>
                    <td style="vertical-align: middle; width: 65%;">
                        <div style="font-size: 6.5pt; color: #4b5563;">IMPORTE NETO EN LETRAS:</div>
                        <div style="font-size: 7pt; font-weight: bold; color: #111827;">{{ $recibo->total_letras }}</div>
                    </td>
                    <td style="width: 35%;">
                        <div class="neto-box">
                            <div style="font-size: 6.5pt; font-weight: bold; color: #1e3a8a;">NETO A PAGAR</div>
                            <div class="neto-amount">${{ number_format($recibo->neto_pagar, 2) }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Observaciones si existen -->
            @if($recibo->observaciones)
                <div class="obs-box">
                    <strong>Observaciones:</strong> {{ $recibo->observaciones }}
                </div>
            @endif

            <!-- Leyenda Legal y Firma -->
            <div class="firma-container">
                <div class="leyenda">
                    @if($recibo->tipo_recibo === 'FINIQUITO')
                        Hago constar que recibo a mi entera satisfacción de <strong>{{ $empresa['nombre'] }}</strong> la cantidad neta indicada en este recibo por concepto de finiquito y liquidación laboral, no reservándome reclamación ni acción laboral, civil o económica posterior.
                    @else
                        Recibí de conformidad de <strong>{{ $empresa['nombre'] }}</strong> el importe neto total señalado en este documento por concepto de sueldos y prestaciones devengadas correspondientes al periodo indicado.
                    @endif
                </div>

                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%; text-align: center;">
                            <br>
                            <div class="linea-firma">
                                ENTREGADO POR / PATRÓN
                            </div>
                        </td>
                        <td style="width: 50%; text-align: center;">
                            <br>
                            <div class="linea-firma">
                                FIRMA DE CONFORMIDAD DEL TRABAJADOR<br>
                                #{{ $recibo->id_empleado_snapshot }} - {{ $recibo->nombre_empleado_snapshot }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($index === 0 && count($chunk) > 1)
            <div class="divider">✂ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - </div>
        @endif
    @endforeach

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>

