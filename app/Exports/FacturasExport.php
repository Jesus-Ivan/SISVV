<?php

namespace App\Exports;

use App\Models\Bodega;
use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\FromArray;


class FacturasExport implements FromArray
{
    public $data;
    public $proveedores = [];


    public function __construct($data)
    {
        $this->data = $data;
        $result = Proveedor::all();
        foreach ($result as $key => $prov) {
            $this->proveedores[$prov->id] = $prov->nombre;
        }
    }

    public function array(): array
    {
        //Encabezados
        $encabezados = [
            'folio_factura' => '#FACTURA',
            'fecha_factura' => 'F.FACTURA',
            'cuenta_contable' => 'CUENTA CONTABLE',
            'clave_presentacion' => '#PRESE.',
            'descripcion' => 'DESCRIPCION',
            'id_proveedor' => 'PROVEEDOR',
            'cantidad' => 'CANTIDAD',
            'costo_unitario' => 'COSTO UNITARIO',
            'iva' => 'IVA',
            'impuesto' => 'IMPUESTOS',
            'costo_con_impuesto' => 'COSTO C.IMPUESTO',
            'importe_sin_impuesto' => 'IMPORTE S.IMPUESTO',
            'importe' => 'IMPORTE FINAL',
            'observaciones' => 'OBSERVACIONES',
        ];

        //Crear array con los datos finales
        $data = [];
        //Agregar el encabezado
        $data[] = $encabezados;
        //Interar todos los resultados
        foreach ($this->data as $item) {
            //Agreagar cada item
            $data[] = [
                'folio_factura' => $item['folio_factura'],
                'fecha_factura' => $item['factura']['fecha_compra'],
                'cuenta_contable' => $item['factura']['cuenta_contable'],
                'clave_presentacion' => $item['clave_presentacion'],
                'descripcion' => $item['descripcion'],
                'id_proveedor' => $this->getProveedor($item['factura']['id_proveedor']),
                'cantidad' => $item['cantidad'],
                'costo_unitario' => $item['costo'],
                'iva' => $item['iva'],
                'costo_con_impuesto' => $item['costo_con_impuesto'],
                'importe_sin_impuesto' => $item['importe_sin_impuesto'],
                'impuesto' => $item['impuesto'],
                'importe' => $item['importe'],
                'observaciones' => $item['factura']['observaciones'],
            ];
        }

        return ($data);
    }

    /**
     * Recibe el id del proveedor y devuelve la el nombre
     */
    private function getProveedor($id)
    {
        return array_key_exists($id, $this->proveedores) ? $this->proveedores[$id] : $id;
    }
}
