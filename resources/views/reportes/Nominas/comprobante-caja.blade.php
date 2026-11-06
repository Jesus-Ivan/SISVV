<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ $movimiento->folio ?? 'Comprobante' }}</title>
    <style>
        @page {
            margin: 16pt 18pt;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5pt;
            color: #1f2937;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        .recibo-card {
            page-break-inside: avoid;
            height: 47%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid {{ $movimiento->tipo_movimiento === 'ADELANTO_EFECTIVO' ? '#b91c1c' : '#047857' }};
            padding-bottom: 5pt;
            margin-bottom: 8pt;
        }

        .header-title {
            font-size: 12pt;
            font-weight: bold;
            color: #111827;
            letter-spacing: 0.5px;
        }

        .header-subtitle {
            font-size: 9pt;
            color: #4b5563;
        }

        .badge-tipo {
            display: inline-block;
            background: {{ $movimiento->tipo_movimiento === 'ADELANTO_EFECTIVO' ? '#b91c1c' : '#047857' }};
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            padding: 3pt 8pt;
            border-radius: 3pt;
            text-transform: uppercase;
        }

        .folio-box {
            text-align: right;
            font-family: 'Courier', monospace;
            font-size: 10pt;
            font-weight: bold;
            color: #111827;
            margin-top: 2pt;
        }

        .info-card {
            width: 100%;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            padding: 6pt 8pt;
            border-radius: 4pt;
            margin-bottom: 10pt;
        }

        .info-label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
        }

        .info-val {
            font-size: 9.5pt;
            font-weight: bold;
            color: #111827;
        }

        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8pt;
        }

        .breakdown-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5pt 8pt;
            border: 1px solid #d1d5db;
        }

        .breakdown-table td {
            font-size: 9pt;
            padding: 6pt 8pt;
            border: 1px solid #e5e7eb;
        }

        .monto-box {
            border: 1.5px solid {{ $movimiento->tipo_movimiento === 'ADELANTO_EFECTIVO' ? '#fca5a5' : '#a7f3d0' }};
            background-color: {{ $movimiento->tipo_movimiento === 'ADELANTO_EFECTIVO' ? '#fef2f2' : '#f0fdf4' }};
            border-radius: 4pt;
            padding: 5pt 8pt;
            min-height: 38pt;
        }

        .monto-label {
            font-size: 7.5pt;
            font-weight: bold;
            color: {{ $movimiento->tipo_movimiento === 'ADELANTO_EFECTIVO' ? '#991b1b' : '#065f46' }};
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .monto-val {
            font-size: 14pt;
            font-weight: bold;
            font-family: 'Courier', monospace;
            color: {{ $movimiento->tipo_movimiento === 'ADELANTO_EFECTIVO' ? '#b91c1c' : '#047857' }};
            margin-top: 2pt;
        }

        .letras-box-side {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4pt;
            padding: 5pt 8pt;
            min-height: 38pt;
        }

        .letras-title {
            font-size: 8pt;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
        }

        .letras-amount {
            font-size: 8.5pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 2pt;
            line-height: 1.2;
        }

        .obs-box {
            border: 1px dashed #9ca3af;
            background-color: #ffffff;
            border-radius: 3pt;
            padding: 5pt 7pt;
            font-size: 9pt;
            color: #374151;
            margin-bottom: 12pt;
        }

        .leyenda {
            font-size: 8pt;
            color: #4b5563;
            text-align: justify;
            line-height: 1.25;
            margin-bottom: 16pt;
        }

        .linea-firma {
            border-top: 1px solid #4b5563;
            width: 85%;
            margin: 0 auto;
            padding-top: 3pt;
            font-size: 9pt;
            font-weight: bold;
            color: #1f2937;
        }
    </style>
</head>

<body>
    <div class="recibo-card">
        <!-- Encabezado -->
        <table class="header-table">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="header-title">
                        @if ($movimiento->tipo_movimiento === 'ADELANTO_EFECTIVO')
                            VALE ANTICIPO EN EFECTIVO
                        @elseif($movimiento->tipo_movimiento === 'ABONO_DIRECTO')
                            RECIBO DE ABONO A DEUDA
                        @else
                            COMPROBANTE DE AJUSTE
                        @endif
                    </div>
                    <div class="header-subtitle"></div>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <div style="font-size: 8pt; color: #4b5563; margin-top: 2pt;">
                        FOLIO:<strong>{{ $movimiento->folio }}</strong></div>
                    <div style="font-size: 8pt; color: #6b7280; margin-top: 2pt;">
                        Fecha:
                        <strong>{{ $movimiento->fecha ? $movimiento->fecha->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Ficha del Trabajador -->
        <table class="info-card">
            <tr>
                <td style="width: 60%;">
                    <div class="info-label">Trabajador Beneficiario</div>
                    <div class="info-val">#{{ $empleado->id }} - {{ $empleado->nombre_completo }}</div>
                </td>
                <td style="width: 40%;">
                    <div class="info-label">Tipo de Contrato</div>
                    <div class="info-val">{{ $empleado->tipo_empleado ?? 'PLANTA' }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 4pt;">
                    <div class="info-label">Puesto Laboral</div>
                    <div class="info-val" style="font-size: 8pt;">
                        {{ $empleado->puesto?->nombre ?? 'Sin puesto asignado' }}
                    </div>
                </td>
                <td style="padding-top: 4pt;">
                    <div class="info-label">Área / Departamento</div>
                    <div class="info-val" style="font-size: 8pt;">{{ $empleado->area?->nombre ?? 'General' }}</div>
                </td>
            </tr>
        </table>

        <!-- Importe del Movimiento (Izquierda) e Importe en Letras (Derecha) -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8pt;">
            <tr>
                <td style="width: 44%; vertical-align: top; padding-right: 4pt;">
                    <div class="monto-box">
                        <div class="monto-label">
                            @if ($movimiento->tipo_movimiento === 'ADELANTO_EFECTIVO')
                                Importe Entregado en Efectivo
                            @elseif($movimiento->tipo_movimiento === 'ABONO_DIRECTO')
                                Importe Abonado en Efectivo
                            @else
                                Importe del Movimiento
                            @endif
                        </div>
                        <div class="monto-val">${{ number_format($movimiento->monto, 2) }}</div>
                    </div>
                </td>
                <td style="width: 56%; vertical-align: top; padding-left: 4pt;">
                    <div class="letras-box-side">
                        <div class="letras-title">Importe en Letras:</div>
                        <div class="letras-amount">{{ $totalLetras }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Motivo / Referencia -->
        <div class="obs-box">
            <strong>Concepto / Referencia:</strong>
            {{ $movimiento->referencia ?: 'Sin observaciones registradas' }}
        </div>

        <!-- Leyenda Legal -->
        <div class="leyenda">
            @if ($movimiento->tipo_movimiento === 'ADELANTO_EFECTIVO')
                Reconozco haber recibido en efectivo la cantidad neta indicada
                en el presente vale como anticipo a cuenta de mis percepciones de trabajo, y autorizo expresamente a
                la
                empresa para que dicho importe sea amortizado y deducido en mis recibos de nómina ordinarios o
                extraordinarios subsecuentes hasta la liquidación total del saldo.
            @elseif($movimiento->tipo_movimiento === 'ABONO_DIRECTO')
                Se hace constar la recepción formal en efectivo del importe señalado en este recibo por concepto de
                abono y
                amortización voluntaria directa a la cuenta de anticipos del trabajador, quedando reflejado el saldo
                nuevo
                restante en los registros contables del club.
            @else
                Se hace constar el ajuste manual formal de saldos efectuado en la cuenta del trabajador de
                conformidad
                con
                la revisión de aclaraciones administrativas de tesorería y nómina.
            @endif
        </div>

        <!-- Firmas -->
        <table style="width: 100%; margin-top: 10pt;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <br><br><br>
                    <div class="linea-firma">
                        AUTORIZADO POR<br>
                    </div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <br><br><br>
                    <div class="linea-firma">
                        NOMBRE Y FIRMA DEL TRABAJADOR<br>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <hr style="border-style: dashed; margin-bottom: 20pt">
</body>

</html>
