<?php

namespace App\Imports;

use App\Models\DetallesComprobaciones;
use App\Models\PeriodoComprobaciones;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ComprobacionesImport implements ToCollection, WithHeadingRow
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
        //Creamos el registro para las comprobaciones
        DB::transaction(function () use ($rows) {
            $periodo = PeriodoComprobaciones::create([
                'id_user' => auth()->user()->id,
                'user_name' => auth()->user()->name,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin
            ]);

            //Creamos los detalles de las comprobaciones
            foreach ($rows as $row) {
                DetallesComprobaciones::create([
                    'folio_periodo' => $periodo->folio,
                    'fecha_nota' => $row['fecha_nota'],
                    'tipo_documento' => $row['tipo_documento'],
                    'proveedor' => $row['proveedor'],
                    'area' => $row['area'],
                    'concepto' => $row['concepto'],
                    'importe' => $row['importe'],
                    'forma_pago' => $row['forma_pago']
                ]);
            }
        }, 2);
    }
}
