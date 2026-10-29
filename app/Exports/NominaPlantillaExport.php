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
    public function __construct(
        public ?int $idPeriodo = null,
        public bool $soloIncompletos = false
    ) {}

    public function collection()
    {
        $periodo = $this->idPeriodo ? NominaPeriodo::find($this->idPeriodo) : null;

        if ($this->soloIncompletos && $periodo) {
            $empleados = NominaEmpleado::with(['area', 'puesto', 'historialLaboral'])
                ->where(function ($q) use ($periodo) {
                    $q->laborandoEnPeriodo($periodo->fecha_inicio, $periodo->fecha_fin)
                      ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $periodo->id));
                })
                ->orderBy('id', 'asc')
                ->get();

            $recibosPeriodo = NominaRecibo::where('id_periodo', $this->idPeriodo)->get()->groupBy('id_empleado');
            $items = collect([]);

            foreach ($empleados as $emp) {
                $ciclo = $emp->historialLaboral->first(function ($c) use ($periodo) {
                    return $c->fecha_ingreso <= $periodo->fecha_fin && ($c->fecha_baja === null || $c->fecha_baja >= $periodo->fecha_inicio);
                });
                $salarioBase = $ciclo ? (float) $ciclo->salario_base : (float) $emp->salario_base;
                $recibosEmp = $recibosPeriodo->get($emp->id, collect([]));
                $emitido = (float) $recibosEmp->sum('total_percepciones');
                $faltante = max(0, round($salarioBase - $emitido, 2));

                if ($faltante > 0.01) {
                    $fiscal = $emp->tipo_pago_predeterminado === 'TRANSFERENCIA' ? $faltante : 0;
                    $efectivo = $emp->tipo_pago_predeterminado !== 'TRANSFERENCIA' ? $faltante : 0;

                    $items->push([
                        'id_empleado'          => $emp->id,
                        'nombre_completo'      => $emp->nombre_completo,
                        'tipo_empleado'        => $emp->tipo_empleado,
                        'area'                 => $emp->area ? $emp->area->nombre : '',
                        'puesto'               => $emp->puesto ? $emp->puesto->nombre : '',
                        'nomina_fiscal'        => $fiscal,
                        'diferencia_efectivo'  => $efectivo,
                        'horas_extras'         => 0,
                        'bonos_otros'          => 0,
                        'descuento_adelanto'   => 0,
                        'descuento_infonavit'  => 0,
                        'otros_descuentos'     => 0,
                        'observaciones'        => 'Recibo complementario - Saldo pendiente',
                    ]);
                }
            }

            return $items;
        }

        $query = NominaEmpleado::with(['area', 'puesto', 'historialLaboral']);
        if ($periodo) {
            $query->where(function ($q) use ($periodo) {
                $q->laborandoEnPeriodo($periodo->fecha_inicio, $periodo->fecha_fin)
                  ->orWhereHas('recibos', fn($rq) => $rq->where('id_periodo', $periodo->id));
            });
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

            $fiscal = $emp->tipo_pago_predeterminado === 'TRANSFERENCIA' ? $salarioBase : 0;
            $efectivo = $emp->tipo_pago_predeterminado !== 'TRANSFERENCIA' ? $salarioBase : 0;

            return [
                'id_empleado'          => $emp->id,
                'nombre_completo'      => $emp->nombre_completo,
                'tipo_empleado'        => $emp->tipo_empleado,
                'area'                 => $emp->area ? $emp->area->nombre : '',
                'puesto'               => $emp->puesto ? $emp->puesto->nombre : '',
                'nomina_fiscal'        => $fiscal,
                'diferencia_efectivo'  => $efectivo,
                'horas_extras'         => 0,
                'bonos_otros'          => 0,
                'descuento_adelanto'   => 0,
                'descuento_infonavit'  => 0,
                'otros_descuentos'     => 0,
                'observaciones'        => '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID_EMPLEADO',
            'NOMBRE_COMPLETO',
            'TIPO_EMPLEADO',
            'AREA',
            'PUESTO',
            'NOMINA_FISCAL',
            'DIFERENCIA_EFECTIVO',
            'HORAS_EXTRAS',
            'BONOS_OTROS',
            'DESCUENTO_ADELANTO',
            'DESCUENTO_INFONAVIT',
            'OTROS_DESCUENTOS',
            'OBSERVACIONES',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => $this->soloIncompletos ? 'FF991B1B' : 'FF1E3A8A'], // Rojo oscuro si es saldos pendientes, Índigo si es base
                ],
            ],
        ];
    }
}
