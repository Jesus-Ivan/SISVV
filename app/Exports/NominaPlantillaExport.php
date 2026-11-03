<?php

namespace App\Exports;

use App\Models\NominaEmpleado;
use App\Models\NominaPeriodo;
use App\Models\NominaRecibo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NominaPlantillaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected ?NominaPeriodo $periodo = null;
    protected bool $esEspecial = false;

    public function __construct(
        public ?int $idPeriodo = null,
        public bool $soloIncompletos = false
    ) {
        if ($this->idPeriodo) {
            $this->periodo = NominaPeriodo::find($this->idPeriodo);
            if ($this->periodo && in_array($this->periodo->tipo_periodo, ['AGUINALDO', 'FINIQUITO'])) {
                $this->esEspecial = true;
            }
        }
    }

    public function collection()
    {
        $periodo = $this->periodo;

        if ($this->soloIncompletos && $periodo) {
            $query = NominaEmpleado::with([
                'area',
                'puesto',
                'historialLaboral',
                'asignacionesPeriodos' => fn($q) => $q->where('id_periodo', $periodo->id),
            ]);

            if ($periodo->tipo_periodo === 'FINIQUITO') {
                // En Finiquito solo auditar asignados o con recibo
                $query->where(function ($q) use ($periodo) {
                    $q->whereHas('asignacionesPeriodos', fn($aq) => $aq->where('id_periodo', $periodo->id))
                      ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $periodo->id));
                });
            } else {
                $query->where(function ($q) use ($periodo) {
                    $q->laborandoEnPeriodo($periodo->fecha_inicio, $periodo->fecha_fin)
                      ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $periodo->id));
                });
            }

            $empleados = $query->orderBy('id', 'asc')->get();
            $recibosPeriodo = NominaRecibo::where('id_periodo', $this->idPeriodo)->get()->groupBy('id_empleado');
            $items = collect([]);

            foreach ($empleados as $emp) {
                $ciclo = $emp->historialLaboral->first(function ($c) use ($periodo) {
                    return $c->fecha_ingreso <= $periodo->fecha_fin && ($c->fecha_baja === null || $c->fecha_baja >= $periodo->fecha_inicio);
                });
                $salarioBase = $ciclo ? (float) $ciclo->salario_base : (float) $emp->salario_base;

                $asignacion = $emp->asignacionesPeriodos->first();
                $montoAcordado = $asignacion ? (float) $asignacion->monto_acordado : 0.0;
                $montoObjetivo = $montoAcordado > 0 ? $montoAcordado : $salarioBase;

                $recibosEmp = $recibosPeriodo->get($emp->id, collect([]));
                $emitido = (float) $recibosEmp->sum('total_percepciones');
                $faltante = max(0, round($montoObjetivo - $emitido, 2));

                if ($faltante > 0.01) {
                    $fiscal = $emp->tipo_pago_predeterminado === 'TRANSFERENCIA' ? $faltante : 0;
                    $efectivo = $emp->tipo_pago_predeterminado !== 'TRANSFERENCIA' ? $faltante : 0;

                    $row = [
                        'id_empleado'         => $emp->id,
                        'nombre_completo'     => $emp->nombre_completo,
                        'tipo_empleado'       => $emp->tipo_empleado,
                        'area'                => $emp->area ? $emp->area->nombre : '',
                        'puesto'              => $emp->puesto ? $emp->puesto->nombre : '',
                        'nomina_fiscal'       => $fiscal,
                        'diferencia_efectivo' => $efectivo,
                        'horas_extras'        => 0,
                        'bonos_otros'         => 0,
                    ];

                    if ($this->esEspecial) {
                        $row['monto_acordado_total']   = $montoAcordado;
                        $row['monto_aguinaldo']        = 0;
                        $row['monto_vacaciones']       = 0;
                        $row['monto_prima_vacacional'] = 0;
                    }

                    $row['descuento_adelanto']  = 0;
                    $row['descuento_infonavit'] = 0;
                    $row['otros_descuentos']    = 0;
                    $row['observaciones']       = 'Recibo complementario - Saldo pendiente';

                    $items->push($row);
                }
            }

            return $items;
        }

        $query = NominaEmpleado::with([
            'area',
            'puesto',
            'historialLaboral',
            'asignacionesPeriodos' => fn($q) => $periodo ? $q->where('id_periodo', $periodo->id) : $q->whereRaw('1 = 0'),
        ]);

        if ($periodo) {
            if ($periodo->tipo_periodo === 'FINIQUITO') {
                // Si ya hay empleados asignados o con recibo, exportar solo esos. Si aún no hay nadie, exportar activos para permitir seleccionar.
                $tieneAsignados = NominaEmpleado::where(function ($q) use ($periodo) {
                    $q->whereHas('asignacionesPeriodos', fn($aq) => $aq->where('id_periodo', $periodo->id))
                      ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $periodo->id));
                })->exists();

                if ($tieneAsignados) {
                    $query->where(function ($q) use ($periodo) {
                        $q->whereHas('asignacionesPeriodos', fn($aq) => $aq->where('id_periodo', $periodo->id))
                          ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $periodo->id));
                    });
                } else {
                    $query->where('activo', true);
                }
            } else {
                $query->where(function ($q) use ($periodo) {
                    $q->laborandoEnPeriodo($periodo->fecha_inicio, $periodo->fecha_fin)
                      ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $periodo->id));
                });
            }
        } else {
            $query->where('activo', true);
        }

        $empleados = $query->orderBy('id', 'asc')->get();

        return $empleados->map(function ($emp) use ($periodo) {
            $salarioBase = (float) $emp->salario_base;
            if ($periodo) {
                $ciclo = $emp->historialLaboral->first(function ($c) use ($periodo) {
                    return $c->fecha_ingreso <= $periodo->fecha_fin && ($c->fecha_baja === null || $c->fecha_baja >= $periodo->fecha_inicio);
                });
                if ($ciclo) {
                    $salarioBase = (float) $ciclo->salario_base;
                }
            }

            $asignacion = $emp->asignacionesPeriodos->first();
            $montoAcordado = $asignacion ? (float) $asignacion->monto_acordado : 0.0;
            $montoInicial = $montoAcordado > 0 ? $montoAcordado : $salarioBase;

            $fiscal = $emp->tipo_pago_predeterminado === 'TRANSFERENCIA' ? $montoInicial : 0;
            $efectivo = $emp->tipo_pago_predeterminado !== 'TRANSFERENCIA' ? $montoInicial : 0;

            $row = [
                'id_empleado'         => $emp->id,
                'nombre_completo'     => $emp->nombre_completo,
                'tipo_empleado'       => $emp->tipo_empleado,
                'area'                => $emp->area ? $emp->area->nombre : '',
                'puesto'              => $emp->puesto ? $emp->puesto->nombre : '',
                'nomina_fiscal'       => $fiscal,
                'diferencia_efectivo' => $efectivo,
                'horas_extras'        => 0,
                'bonos_otros'         => 0,
            ];

            if ($this->esEspecial) {
                $row['monto_acordado_total']   = $montoAcordado;
                $row['monto_aguinaldo']        = 0;
                $row['monto_vacaciones']       = 0;
                $row['monto_prima_vacacional'] = 0;
            }

            $row['descuento_adelanto']  = 0;
            $row['descuento_infonavit'] = 0;
            $row['otros_descuentos']    = 0;
            $row['observaciones']       = '';

            return $row;
        });
    }

    public function headings(): array
    {
        $headers = [
            'ID_EMPLEADO',
            'NOMBRE_COMPLETO',
            'TIPO_EMPLEADO',
            'AREA',
            'PUESTO',
            'NOMINA_FISCAL',
            'DIFERENCIA_EFECTIVO',
            'HORAS_EXTRAS',
            'BONOS_OTROS',
        ];

        if ($this->esEspecial) {
            $headers[] = 'MONTO_ACORDADO_TOTAL';
            $headers[] = 'MONTO_AGUINALDO';
            $headers[] = 'MONTO_VACACIONES';
            $headers[] = 'MONTO_PRIMA_VACACIONAL';
        }

        $headers[] = 'DESCUENTO_ADELANTO';
        $headers[] = 'DESCUENTO_INFONAVIT';
        $headers[] = 'OTROS_DESCUENTOS';
        $headers[] = 'OBSERVACIONES';

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $baseStyle = [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => $this->soloIncompletos ? 'FF991B1B' : 'FF1E3A8A'],
                ],
            ],
        ];

        if ($this->esEspecial) {
            // Columnas J1 a M1 corresponden a MONTO_ACORDADO_TOTAL, MONTO_AGUINALDO, MONTO_VACACIONES, MONTO_PRIMA_VACACIONAL
            $sheet->getStyle('J1:M1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD97706'], // Color ámbar / dorado distintivo
                ],
            ]);
        }

        return $baseStyle;
    }
}
