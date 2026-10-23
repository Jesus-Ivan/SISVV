<?php

namespace App\Exports;

use App\Models\NominaEmpleado;
use App\Models\NominaMovimientoCuenta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NominaCuentasExport implements WithMultipleSheets
{
    public ?int $id_empleado;
    public ?string $tipo_empleado;
    public ?string $fecha_inicio;
    public ?string $fecha_fin;

    public function __construct(?int $id_empleado = null, ?string $tipo_empleado = null, ?string $fecha_inicio = null, ?string $fecha_fin = null)
    {
        $this->id_empleado = $id_empleado;
        $this->tipo_empleado = $tipo_empleado;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function sheets(): array
    {
        return [
            new NominaBalanzaSheet($this->id_empleado, $this->tipo_empleado),
            new NominaMovimientosSheet($this->id_empleado, $this->tipo_empleado, $this->fecha_inicio, $this->fecha_fin),
        ];
    }
}

class NominaBalanzaSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting, WithTitle
{
    public ?int $id_empleado;
    public ?string $tipo_empleado;

    public function __construct(?int $id_empleado = null, ?string $tipo_empleado = null)
    {
        $this->id_empleado = $id_empleado;
        $this->tipo_empleado = $tipo_empleado;
    }

    public function collection()
    {
        $query = NominaEmpleado::with('area', 'puesto')->orderBy('id', 'asc');
        if ($this->id_empleado) {
            $query->where('id', $this->id_empleado);
        }
        if ($this->tipo_empleado && in_array($this->tipo_empleado, ['PLANTA', 'TEMPORAL'])) {
            $query->where('tipo_empleado', $this->tipo_empleado);
        }

        return $query->get()->map(function ($emp) {
            return [
                'id' => $emp->id,
                'nombre' => $emp->nombre_completo,
                'tipo' => $emp->tipo_empleado,
                'area' => $emp->area ? $emp->area->nombre : 'N/A',
                'puesto' => $emp->puesto ? $emp->puesto->nombre : 'N/A',
                'estado' => $emp->activo ? 'ACTIVO' : 'INACTIVO',
                'saldo_adelantos' => (float) $emp->saldo_adelantos,
            ];
        });
    }

    public function headings(): array
    {
        return [
            '# ID',
            'Nombre Completo',
            'Tipo Empleado',
            'Área',
            'Puesto',
            'Estatus',
            'Saldo Adelantos / Deudor',
        ];
    }

    public function title(): string
    {
        return 'Balanza de Saldos';
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF065F46'], // Verde oscuro institucional
                ],
            ],
        ];
    }
}

class NominaMovimientosSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting, WithTitle
{
    public ?int $id_empleado;
    public ?string $tipo_empleado;
    public ?string $fecha_inicio;
    public ?string $fecha_fin;

    public function __construct(?int $id_empleado = null, ?string $tipo_empleado = null, ?string $fecha_inicio = null, ?string $fecha_fin = null)
    {
        $this->id_empleado = $id_empleado;
        $this->tipo_empleado = $tipo_empleado;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function collection()
    {
        $query = NominaMovimientoCuenta::with('empleado', 'usuario')->orderBy('fecha', 'desc')->orderBy('id', 'desc');

        if ($this->id_empleado) {
            $query->where('id_empleado', $this->id_empleado);
        }
        if ($this->tipo_empleado && in_array($this->tipo_empleado, ['PLANTA', 'TEMPORAL'])) {
            $query->whereHas('empleado', fn($q) => $q->where('tipo_empleado', $this->tipo_empleado));
        }
        if ($this->fecha_inicio) {
            $query->whereDate('fecha', '>=', $this->fecha_inicio);
        }
        if ($this->fecha_fin) {
            $query->whereDate('fecha', '<=', $this->fecha_fin);
        }

        return $query->get()->map(function ($mov) {
            return [
                'fecha' => $mov->fecha ? $mov->fecha->format('Y-m-d') : '',
                'id_empleado' => $mov->id_empleado,
                'nombre' => $mov->empleado ? $mov->empleado->nombre_completo : 'N/A',
                'tipo_movimiento' => $mov->tipo_movimiento,
                'naturaleza' => $mov->naturaleza,
                'monto' => (float) $mov->monto,
                'saldo_anterior' => (float) $mov->saldo_anterior,
                'saldo_nuevo' => (float) $mov->saldo_nuevo,
                'referencia' => $mov->referencia,
                'usuario' => $mov->usuario ? $mov->usuario->name : 'Sistema',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            '# ID Empleado',
            'Nombre Empleado',
            'Tipo Movimiento',
            'Naturaleza (Cargo/Abono)',
            'Monto',
            'Saldo Anterior',
            'Saldo Nuevo',
            'Referencia',
            'Registrado Por',
        ];
    }

    public function title(): string
    {
        return 'Historial Movimientos';
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'H' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A8A'],
                ],
            ],
        ];
    }
}

