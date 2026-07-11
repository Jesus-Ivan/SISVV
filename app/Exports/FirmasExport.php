<?php

namespace App\Exports;

use App\Exports\Sheets\Firmas\DetalleSheet;
use App\Exports\Sheets\Firmas\GeneralSheet;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FirmasExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(protected Collection $resultados) {}

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        /**
         * Agregar hojas al resporte
         */
        $sheets[] = new DetalleSheet($this->resultados);

        $resultados_agrupados = $this->resultados->groupBy('id_socio');
        $sheets[] = new GeneralSheet($resultados_agrupados);

        return $sheets;
    }
}
