<?php

namespace App\Exports;

use App\Exports\Sheets\ProdVentas\ProdEliminados;
use App\Exports\Sheets\ProdVentas\ProdVendidos;
use App\Exports\Sheets\ProdVentas\TotalVendidos;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class ProdVendTotalExport implements WithMultipleSheets
{
    use Exportable;
    protected $productos;

    public function __construct(array $productos)
    {
        $this->productos = $productos;
    }

    public function sheets(): array
    {
        $sheets = [];   //Definimos las hojas del excel
        foreach ($this->productos as $clave_punto => $vendidos) {
            //La hoja principal de productos vendidos
            $sheets[] = new TotalVendidos($clave_punto, $vendidos);
        }
        return $sheets;
    }
}
