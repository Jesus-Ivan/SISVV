<?php

namespace App\Exports;

use App\Constants\PuntosConstants;
use App\Exports\Sheets\Comandas\Comandas;
use App\Exports\Sheets\Comandas\ComandasEliminadas;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ComandasExport implements WithMultipleSheets
{
    use Exportable;
    public $rows, $prod_eliminados;

    public function __construct(array $result, $prod_eliminados = null)
    {
        $this->rows = $result;
        //Si no es null (los productos eliminados)
        if (!is_null($prod_eliminados)) {
            //Convertirlo en array
            $this->prod_eliminados = $prod_eliminados->toArray();
        }
    }

    public function sheets(): array
    {
        $sheets = [];   //Definimos las hojas del excel
        //La hoja principal de productos vendidos
        $sheets[] = new Comandas($this->rows);
        //Si se paso el array de productos eliminados
        if (!is_null($this->prod_eliminados)) {
            //Agregar hoja de productos eliminados
            $sheets[] = new ComandasEliminadas($this->prod_eliminados);
        }
        return $sheets;
    }
}
