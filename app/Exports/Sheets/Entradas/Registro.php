<?php

namespace App\Exports\Sheets\Entradas;

use App\Models\Bodega;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class Registro implements FromArray, WithTitle
{
    public $data, $tittle;


    public function __construct($data, $tittle)
    {
        //Resguardar los valores en las propiedades
        $this->data = $data;
        $this->tittle = $tittle;
    }

    public function array(): array
    {
        //Crear array con los datos finales
        $insumos = [];

        //Interar todos los resultados
        foreach ($this->data as $key => $item) {
            //Agreagar cada item
            $insumos[] = [
                'folio_entrada' => $item['folio_entrada'],
                'clave_presentacion' => $item['clave_presentacion'],
                'clave_insumo' => $item['clave_insumo'],
                'fecha_existencias' => $item['fecha_existencias'],
                'bodega' =>  $item['bodega'],
                'descripcion' => $item['descripcion'],
                'id_proveedor' => $item['id_proveedor'],
                'factura' => $item['factura'],
                'cuenta_contable' => $item['cuenta_contable'],
                'cantidad' => $item['cantidad'],
                'unidad' => $item['unidad'],
                'costo_unitario' => $item['costo_unitario'],
                'iva' => $item['iva'],
                'costo_con_impuesto' => $item['costo_con_impuesto'],
                'importe_sin_impuesto' => $item['importe_sin_impuesto'],
                'impuesto' => $item['impuesto'],
                'importe' => $item['importe']
            ];
        }

        return $insumos;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->tittle;
    }
}
