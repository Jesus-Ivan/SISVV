<?php

namespace App\Exports;

use App\Models\NominaRecibo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NominaReporteRecibosExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    private int $totalRows = 0;

    public function __construct(
        public ?int $anio = null,
        public ?int $idPeriodo = null,
        public ?int $idEmpleado = null,
        public ?string $metodoPago = null,
        public ?string $estadoPago = null
    ) {}

    public function collection()
    {
        $query = NominaRecibo::with(['periodo', 'empleado.area', 'empleado.puesto']);

        if ($this->idPeriodo) {
            $query->where('id_periodo', $this->idPeriodo);
        } elseif ($this->anio) {
            $query->whereHas('periodo', function ($pq) {
                $pq->whereYear('fecha_inicio', $this->anio);
            });
        }

        if ($this->idEmpleado) {
            $query->where('id_empleado', $this->idEmpleado);
        }

        if ($this->metodoPago && $this->metodoPago !== 'TODOS') {
            $query->where('metodo_pago', $this->metodoPago);
        }

        if ($this->estadoPago && $this->estadoPago !== 'TODOS') {
            $query->where('estado_pago', $this->estadoPago);
        }

        $recibos = $query->orderBy('id_periodo', 'desc')
            ->orderBy('id_empleado_snapshot', 'asc')
            ->get();

        $rows = collect([]);
        $totales = [
            'nomina_fiscal'       => 0.0,
            'diferencia_efectivo' => 0.0,
            'horas_extras'        => 0.0,
            'bonos_otros'         => 0.0,
            'total_percepciones'  => 0.0,
            'descuento_adelanto'  => 0.0,
            'descuento_infonavit' => 0.0,
            'otros_descuentos'    => 0.0,
            'total_deducciones'   => 0.0,
            'neto_pagar'          => 0.0,
        ];

        foreach ($recibos as $r) {
            $fiscal = (float) $r->nomina_fiscal;
            $efectivo = (float) $r->diferencia_efectivo;
            $he = (float) $r->horas_extras;
            $bonos = (float) $r->bonos_otros;
            $percepciones = (float) $r->total_percepciones;
            $adelanto = (float) $r->descuento_adelanto;
            $infonavit = (float) $r->descuento_infonavit;
            $otrosDesc = (float) $r->otros_descuentos;
            $deducciones = (float) $r->total_deducciones;
            $neto = (float) $r->neto_pagar;

            $totales['nomina_fiscal']       += $fiscal;
            $totales['diferencia_efectivo'] += $efectivo;
            $totales['horas_extras']        += $he;
            $totales['bonos_otros']         += $bonos;
            $totales['total_percepciones']  += $percepciones;
            $totales['descuento_adelanto']  += $adelanto;
            $totales['descuento_infonavit'] += $infonavit;
            $totales['otros_descuentos']    += $otrosDesc;
            $totales['total_deducciones']   += $deducciones;
            $totales['neto_pagar']          += $neto;

            $periodoFechas = $r->periodo 
                ? "{$r->periodo->fecha_inicio?->format('d/m/Y')} - {$r->periodo->fecha_fin?->format('d/m/Y')}"
                : ($r->fecha_inicio ? "{$r->fecha_inicio->format('d/m/Y')} - {$r->fecha_fin->format('d/m/Y')}" : 'Sin Período');

            $nombrePeriodo = $r->periodo?->nombre ?: ($r->periodo?->folio ?? 'N/A');

            $rows->push([
                'folio'               => $r->folio,
                'periodo'             => $periodoFechas,
                'nombre_periodo'      => $nombrePeriodo,
                'id_empleado'         => $r->id_empleado_snapshot ?: $r->id_empleado,
                'nombre_completo'     => $r->nombre_empleado_snapshot,
                'area'                => $r->area_snapshot ?: ($r->empleado?->area?->nombre ?? ''),
                'puesto'              => $r->puesto_snapshot ?: ($r->empleado?->puesto?->nombre ?? ''),
                'tipo_empleado'       => $r->tipo_empleado_snapshot ?: ($r->empleado?->tipo_empleado ?? ''),
                'tipo_recibo'         => $r->tipo_recibo,
                'metodo_pago'         => $r->metodo_pago,
                'estado_pago'         => $r->estado_pago,
                'fecha_pago'          => $r->fecha_pago ? $r->fecha_pago->format('d/m/Y') : '',
                'fecha_pago_real'     => $r->fecha_pago_real ? $r->fecha_pago_real->format('d/m/Y') : '',
                'nomina_fiscal'       => $fiscal,
                'diferencia_efectivo' => $efectivo,
                'horas_extras'        => $he,
                'bonos_otros'         => $bonos,
                'total_percepciones'  => $percepciones,
                'descuento_adelanto'  => $adelanto,
                'descuento_infonavit' => $infonavit,
                'otros_descuentos'    => $otrosDesc,
                'total_deducciones'   => $deducciones,
                'neto_pagar'          => $neto,
                'observaciones'       => $r->observaciones ?: '',
            ]);
        }

        if ($rows->isNotEmpty()) {
            $rows->push([
                'folio'               => 'TOTALES (' . $recibos->count() . ' RECIBOS)',
                'periodo'             => '',
                'nombre_periodo'      => '',
                'id_empleado'         => '',
                'nombre_completo'     => '',
                'area'                => '',
                'puesto'              => '',
                'tipo_empleado'       => '',
                'tipo_recibo'         => '',
                'metodo_pago'         => '',
                'estado_pago'         => '',
                'fecha_pago'          => '',
                'fecha_pago_real'     => '',
                'nomina_fiscal'       => $totales['nomina_fiscal'],
                'diferencia_efectivo' => $totales['diferencia_efectivo'],
                'horas_extras'        => $totales['horas_extras'],
                'bonos_otros'         => $totales['bonos_otros'],
                'total_percepciones'  => $totales['total_percepciones'],
                'descuento_adelanto'  => $totales['descuento_adelanto'],
                'descuento_infonavit' => $totales['descuento_infonavit'],
                'otros_descuentos'    => $totales['otros_descuentos'],
                'total_deducciones'   => $totales['total_deducciones'],
                'neto_pagar'          => $totales['neto_pagar'],
                'observaciones'       => '',
            ]);
        }

        $this->totalRows = $rows->count();

        return $rows;
    }

    public function headings(): array
    {
        return [
            'FOLIO',
            'PERIODO',
            'NOMBRE PERIODO',
            'ID EMPLEADO',
            'EMPLEADO',
            'AREA',
            'PUESTO',
            'TIPO EMPLEADO',
            'TIPO RECIBO',
            'METODO PAGO',
            'ESTADO PAGO',
            'FECHA PAGO',
            'FECHA PAGO REAL',
            'NOMINA FISCAL',
            'DIFERENCIA EFECTIVO',
            'HORAS EXTRAS',
            'BONOS / OTROS',
            'TOTAL PERCEPCIONES',
            'AMORT. ADELANTO',
            'DESC. INFONAVIT',
            'OTROS DESCUENTOS',
            'TOTAL DEDUCCIONES',
            'NETO A PAGAR',
            'OBSERVACIONES',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'N' => '"$"#,##0.00',
            'O' => '"$"#,##0.00',
            'P' => '"$"#,##0.00',
            'Q' => '"$"#,##0.00',
            'R' => '"$"#,##0.00',
            'S' => '"$"#,##0.00',
            'T' => '"$"#,##0.00',
            'U' => '"$"#,##0.00',
            'V' => '"$"#,##0.00',
            'W' => '"$"#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A8A'], // Indigo oscuro institucional
                ],
            ],
        ];

        if ($this->totalRows > 0) {
            $lastRow = $this->totalRows + 1;
            $styles[$lastRow] = [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF1E293B']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E8F0'], // Slate-200
                ],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF0F172A']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF0F172A']],
                ],
            ];
        }

        return $styles;
    }
}

