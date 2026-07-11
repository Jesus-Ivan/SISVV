<?php

namespace App\Exports\Sheets\Firmas;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Override;

class GeneralSheet implements FromArray, WithTitle
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
                'monto' => 'MONTO TOTAL',
            ],
        ];

        foreach ($this->data as $key => $subCollection) {
            array_push($tittle_data, [
                'id_socio' => $key,
                'nombre_socio' => $subCollection[0]->nombre_socio,
                'deleted_at' => $subCollection[0]->deleted_at,
                'monto' => array_sum(array_column($subCollection->toArray(), 'saldo')),
            ]);
        }


        return $tittle_data;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'General';
    }
}
