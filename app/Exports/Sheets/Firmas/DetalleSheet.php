<?php

namespace App\Exports\Sheets\Firmas;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Override;

class DetalleSheet implements FromArray, WithTitle
{
    public function __construct(protected Collection $data) {}


    #[Override]
    public function array(): array
    {
        $tittle_data = [
            [
                'id_socio' => 'ACCION',
                'nombre_socio' => 'NOMBRE SOCIO',
                'deleted_at' => 'ELIMINADO EL',
                'concepto' => 'CONCEPTO',
                'fecha' => 'FECHA',
                'cargo' => 'CARGO',
                'abono' => 'ABONO',
                'saldo' => 'SALDO',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
        ];

        foreach ($this->data as $key => $row) {
            array_push($tittle_data, [
                'id_socio' => $row->id_socio,
                'nombre_socio' => $row->nombre_socio,
                'deleted_at' => $row->deleted_at,
                'concepto' => $row->concepto,
                'fecha' => $row->fecha,
                'cargo' => $row->cargo,
                'abono' => $row->abono,
                'saldo' => $row->saldo,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }


        return $tittle_data;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Detalle';
    }
}
