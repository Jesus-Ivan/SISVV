<?php

namespace App\Exports;

use App\Models\Bodega;
use App\Models\DetallesEntrada;
use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class EntradasExport implements FromArray
{
    public $data;
    public $bodegas = [];

    public function __construct($data)
    {
        $this->data = $data;
        //Crear array indexado de las bodegas
        $bodegas = Bodega::all();
        foreach ($bodegas as $key => $row) {
            $this->bodegas[$row['clave']] = $row['descripcion'];
        }
    }

    public function array(): array
    {
        //Encabezados
        $encabezados = [
            'folio_entrada' => 'FOLIO ENTRADA',
            'clave_presentacion' => '#PRESENTACION',
            'clave_insumo' => '#INSUMO',
            'fecha_existencias' => 'FECHA EXISTENCIAS',
            'bodega' => 'BODEGA',
            'descripcion' => 'DESCRIPCION',
            'id_proveedor' => 'PROVEEDOR',
            'cantidad' => 'CANTIDAD',
            'costo_unitario' => 'COSTO UNITARIO',
            'iva' => 'IVA',
            'costo_con_impuesto' => 'COSTO C.IMPUESTO',
            'importe_sin_impuesto' => 'IMPORTE SIN IMPUESTO',
            'impuesto' => 'IMPUESTOS',
            'importe' => 'IMPORTE FINAL'
        ];

        //Crear array con los datos finales
        $data = [];
        //Agregar el encabezado
        $data[] = $encabezados;
        //Interar todos los resultados
        foreach ($this->data as $item) {
            //Agreagar cada item
            $data[] = [
                'folio_entrada' => $item['folio_entrada'],
                'clave_presentacion' => $item['clave_presentacion'],
                'clave_insumo' => $item['clave_insumo'],
                'fecha_existencias' => $item['entrada']['fecha_existencias'],
                'bodega' =>  $this->getBodega($item['entrada']['clave_bodega']),
                'descripcion' => $item['descripcion'],
                'id_proveedor' => $item['proveedor']['nombre'],
                'cantidad' => $item['cantidad'],
                'costo_unitario' => $item['costo_unitario'],
                'iva' => $item['iva'],
                'costo_con_impuesto' => $item['costo_con_impuesto'],
                'importe_sin_impuesto' => $item['importe_sin_impuesto'],
                'impuesto' => $item['impuesto'],
                'importe' => $item['importe']
            ];
        }

        return ($data);
    }
    /**
     * Recibe la clave de la bodega y devuelve la descripcion
     */
    private function getBodega($clave)
    {
        return array_key_exists($clave, $this->bodegas) ? $this->bodegas[$clave] : $clave;
    }
}
