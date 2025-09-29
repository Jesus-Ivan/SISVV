<?php

namespace App\Exports;

use App\Exports\Sheets\ProdVentas\ProdEliminados;
use App\Exports\Sheets\ProdVentas\ProdVendidos;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use function PHPUnit\Framework\isNull;

class ProductosVendExport implements WithMultipleSheets
{
    use Exportable;
    protected $ventas, $prod_eliminados;

    public function __construct(array $ventas, $prod_eliminados = null)
    {
        $this->ventas = $ventas;
        //Si no es null
        if (!is_null($prod_eliminados)) {
            //Convertirlo en array
            $this->prod_eliminados = $prod_eliminados->toArray();
        }
    }

    public function sheets(): array
    {
        $sheets = [];   //Definimos las hojas del excel
        //La hoja principal de productos vendidos
        $sheets[] = new ProdVendidos($this->ventas);
        //Si se paso el array de productos eliminados
        if (!is_null($this->prod_eliminados)) {
            //Agregar hoja de productos eliminados
            $sheets[] = new ProdEliminados($this->prod_eliminados);
        }
        return $sheets;
    }
}
