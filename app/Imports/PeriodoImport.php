<?php

namespace App\Imports;

use App\Models\DetallesPeriodoNomina;
use App\Models\PeriodoNomina;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PeriodoImport implements ToCollection, WithHeadingRow
{

    public $fecha_inicio, $fecha_fin;

    //Constructor
    public function __construct($fecha_inicio, $fecha_fin)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }


    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            //Crear el registro del periodo
            $periodo = PeriodoNomina::create([
                'id_user' => auth()->user()->id,
                'nombre' => auth()->user()->name,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
            ]);

            //Crear cada detalle de nomina
            foreach ($rows as $row) {
                DetallesPeriodoNomina::create([
                    'referencia_periodo' => $periodo->referencia,
                    'no_empleado' => $row[0],
                    'nombre' => $row['nombre'],
                    'area' => $row['area'],
                    'nomina_fiscal' => $row['nomina_fiscal'],
                    'diferencia_efectivo' => $row['diferencia_en_efectivo'],
                    'extras' => $row['extra'],
                    'total' => $row['total'],
                    'descuento' => $row['descuento'],
                    'infonavit' => $row['infonavit'],
                    'nomina_pagar' => $row['nomina_a_pagar'],
                    'fecha_inicio' => $this->fecha_inicio,
                    'fecha_fin' => $this->fecha_fin,
                    'observaciones' => $row['observaciones'],
                ]);
            }
        }, 2);
    }
}
