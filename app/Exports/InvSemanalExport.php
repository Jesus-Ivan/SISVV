<?php

namespace App\Exports;

use App\Models\Bodega;
use App\Models\Cuota;
use App\Models\Grupos;
use App\Models\IntegrantesSocio;
use App\Models\SocioCuota;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvSemanalExport implements FromArray, WithColumnWidths, WithStyles
{
    public $rows, $fechas, $grupos, $bodega;
    public function __construct(array $result, array $fechas, Bodega $bodega)
    {
        $this->rows = $result;
        $this->fechas = $fechas;
        $this->bodega = $bodega;
    }

    public function array(): array
    {
        //Definir encabezados del excel
        $encabezados = [
            'A' => '',
            'B' => '',
            'C' => $this->fechas[0]->locale('es')->shortDayName . ' ' . $this->fechas[0]->toDateString(),
            'D' => '',
            'E' => '',
            'F' => $this->fechas[1]->locale('es')->shortDayName . ' ' . $this->fechas[1]->toDateString(),
            'G' => '',
            'H' => '',
            'I' => $this->fechas[2]->locale('es')->shortDayName . ' ' . $this->fechas[2]->toDateString(),
            'J' => '',
            'K' => '',
            'L' => $this->fechas[3]->locale('es')->shortDayName . ' ' . $this->fechas[3]->toDateString(),
            'M' => '',
            'N' => '',
            'O' => $this->fechas[4]->locale('es')->shortDayName . ' ' . $this->fechas[4]->toDateString(),
            'P' => '',
            'Q' => '',
            'R' => $this->fechas[5]->locale('es')->shortDayName . ' ' . $this->fechas[5]->toDateString(),
            'S' => '',
            'T' => '',
        ];
        //Definir segunda fila de la tabla
        $seg_fila = [
            'A' => $this->bodega->descripcion,
            'B' => 'GRUPO',
            'C' => 'E',
            'D' => 'P',
            'E' => 'R',
            'F' => 'E',
            'G' => 'P',
            'H' => 'R',
            'I' => 'E',
            'J' => 'P',
            'K' => 'R',
            'L' => 'E',
            'M' => 'P',
            'N' => 'R',
            'O' => 'E',
            'P' => 'P',
            'Q' => 'R',
            'R' => 'E',
            'S' => 'P',
            'T' => 'R',
        ];
        //Crear array final
        $final = [
            $encabezados,
            $seg_fila
        ];

        foreach ($this->rows as $value) {
            array_push(
                $final,
                ['A' => $value['descripcion'], 'B' => $value['grupo']['descripcion'],]
            );
        }

        return $final;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 9,
            'C' => 9,
            'D' => 15,
            'E' => 9,
            'F' => 9,
            'G' => 15,
            'H' => 9,
            'I' => 9,
            'J' => 15,
            'K' => 9,
            'L' => 9,
            'M' => 15,
            'N' => 9,
            'O' => 9,
            'P' => 15,
            'Q' => 9,
            'R' => 9,
            'S' => 15,
            'T' => 9,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilizar la primera fila con texto negrito
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
            // Styling an entire column.
            'A'  => ['font' => ['size' => 10]],
        ];
    }
}
