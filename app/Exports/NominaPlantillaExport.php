<?php

namespace App\Exports;

use App\Models\NominaEmpleado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NominaPlantillaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        $empleados = NominaEmpleado::with(['area', 'puesto'])
            ->where('activo', true)
            ->orderBy('id', 'asc')
            ->get();

        return $empleados->map(function ($emp) {
            $fiscal = $emp->tipo_pago_predeterminado === 'TRANSFERENCIA' ? (float) $emp->salario_base : 0;
            $efectivo = $emp->tipo_pago_predeterminado !== 'TRANSFERENCIA' ? (float) $emp->salario_base : 0;

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
                    'startColor' => ['argb' => 'FF1E3A8A'], // Indigo oscuro institucional
                ],
            ],
        ];
    }
}
