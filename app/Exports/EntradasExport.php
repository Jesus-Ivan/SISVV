<?php

namespace App\Exports;

use App\Models\DetallesEntrada;
use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class EntradasExport implements FromArray
{
    public $fInicio;
    public $fFin;
    public $id_proveedor;

    public function __construct($fInicio, $fFin, $id_proveedor)
    {
        $this->fInicio = $fInicio;
        $this->fFin = $fFin;
        $this->id_proveedor = $id_proveedor;
    }

    public function array(): array
    {
        //Encabezados
        $encabezados = [
            'codigo_producto' => 'CODIGO',
            'folio_entrada' => 'FOLIO ENTRADA',
            'nombre' => 'NOMBRE',
            'id_proveedor' => 'PROVEEDOR',
            'cantidad' => 'CANTIDAD',
            'peso' => 'PESO',
            'costo_unitario' => 'COSTO UNITARIO',
            'importe' => 'IMPORTE',
            'tipo_compra' => 'TIPO COMPRA',
            'iva' => 'IVA',
            'fecha_compra' => 'FECHA COMPRA',
        ];

        //Obtener los proveedores
        $proveedores = Proveedor::all();

        /**
         * Obtener los detalles de entradas
         */
        if ($this->id_proveedor) {
            $result = DetallesEntrada::where([
                ['fecha_compra', '>=', $this->fInicio],
                ['fecha_compra', '<=', $this->fFin],
                ['id_proveedor', '<=', $this->id_proveedor],
            ])->get();
        } else {
            $result = DetallesEntrada::where([
                ['fecha_compra', '>=', $this->fInicio],
                ['fecha_compra', '<=', $this->fFin]
            ])->get();
        }


        //Crear array con los datos finales
        $data = [];
        //Agregar el encabezado
        $data[] = $encabezados;
        //Interar todos los resultados
        foreach ($result as $item) {
            //Agreagar cada item
            $data[] = [
                'codigo_producto' => $item['codigo_producto'],
                'folio_entrada' => $item['folio_entrada'],
                'nombre' => $item['nombre'],
                'id_proveedor' => $item['id_proveedor'],
                'cantidad' => $item['cantidad'],
                'peso' => $item['peso'],
                'costo_unitario' => $item['costo_unitario'],
                'importe' => $item['importe'],
                'tipo_compra' => $item['tipo_compra'],
                'iva' => $item['iva'],
                'fecha_compra' => $item['fecha_compra'],
            ];
        }

        return ($data);
    }
}
