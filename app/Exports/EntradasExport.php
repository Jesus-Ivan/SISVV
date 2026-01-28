<?php

namespace App\Exports;

use App\Exports\Sheets\Entradas\Registro;
use App\Models\Bodega;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EntradasExport implements WithMultipleSheets
{
    public $data;
    public $bodegas = [];
    public $convertir_insumos = null;

    public function __construct($data, $convertir_insumos)
    {
        $this->data = $data;
        //Crear array indexado de las bodegas
        $bodegas = Bodega::all();
        foreach ($bodegas as $key => $row) {
            $this->bodegas[$row['clave']] = $row['descripcion'];
        }
        $this->convertir_insumos = $convertir_insumos;
    }

    public function sheets(): array
    {
        $sheets = [];   //Definimos las hojas del excel
        //La hoja principal de reporte de entradas
        $sheets[] = new Registro($this->crearDatos(), 'ORIGINAL');
        if ($this->convertir_insumos) {
            //agregar la hoja convertida
            $sheets[] = new Registro($this->crearDatos(true), 'CONVERTIDO');
        }
        return $sheets;
    }

    public function crearDatos($is_converted = false): array
    {
        //Encabezados
        $encabezados = [
            'folio_entrada' => '#ENTRADA',
            'fecha_existencias' => 'FECHA EXISTENCIAS',
            'bodega' => 'BODEGA',
            'user_name' => 'RESPONSABLE',
            'clave_presentacion' => '#PRESENTACION',
            'clave_insumo' => '#INSUMO',
            'descripcion' => 'DESCRIPCION',
            'id_proveedor' => 'PROVEEDOR',
            'factura' => 'FACTURA',
            'cuenta_contable' => 'M.PAGO',
            'cantidad' => 'CANTIDAD',
            'unidad' => 'UNIDAD',
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
            $data[] = $this->getData($item, $is_converted);
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

    private function getData($item, $is_converted)
    {
        if (!$is_converted) {
            $aux = [
                'folio_entrada' => $item['folio_entrada'],
                'fecha_existencias' => $item['entrada']['fecha_existencias'],
                'bodega' =>  $this->getBodega($item['entrada']['clave_bodega']),
                'user_name' => $item['entrada']['nombre'],
                'clave_presentacion' => $item['clave_presentacion'],
                'clave_insumo' => $item['clave_insumo'],
                'descripcion' => $item['descripcion'],
                'id_proveedor' => $item['proveedor']['nombre'],
                'factura' => $item['factura'],
                'cuenta_contable' => $item['cuenta_contable'],
                'cantidad' => $item['cantidad'],
                'unidad' => $item['insumo']['unidad']['descripcion'],
                'costo_unitario' => $item['costo_unitario'],
                'iva' => $item['iva'],
                'costo_con_impuesto' => $item['costo_con_impuesto'],
                'importe_sin_impuesto' => $item['importe_sin_impuesto'],
                'impuesto' => $item['impuesto'],
                'importe' => $item['importe']
            ];
        } else {
            $aux = [
                'folio_entrada' => $item['folio_entrada'],
                'fecha_existencias' => $item['entrada']['fecha_existencias'],
                'bodega' =>  $this->getBodega($item['entrada']['clave_bodega']),
                'user_name' => $item['entrada']['nombre'],
                'clave_presentacion' => $this->getClavePresentacion($item),
                'clave_insumo' => $item['clave_insumo'],
                'descripcion' => $this->getDescripcionPresentacion($item),
                'id_proveedor' => $item['proveedor']['nombre'],
                'factura' => $item['factura'],
                'cuenta_contable' => $item['cuenta_contable'],
                'cantidad' => $this->getCantidadPresentacion($item),
                'unidad' => $item['insumo']['unidad']['descripcion'],
                'costo_unitario' => $this->getCostoUnitario($item),
                'iva' => $item['iva'],
                'costo_con_impuesto' => $this->getCostoImpuesto($item),
                'importe_sin_impuesto' => $item['importe_sin_impuesto'],
                'impuesto' => $item['impuesto'],
                'importe' => $item['importe']
            ];
        }

        return $aux;
    }


    private function getClavePresentacion($item)
    {
        if ($item['clave_presentacion'])
            return $item['clave_presentacion'];
        elseif (reset($item['insumo']['presentaciones']))
            return reset($item['insumo']['presentaciones'])['clave'];
        else
            return 'N/A';
    }

    private function getDescripcionPresentacion($item)
    {
        if ($item['clave_presentacion']) {
            return $item['descripcion'];
        } elseif (reset($item['insumo']['presentaciones'])) {
            return reset($item['insumo']['presentaciones'])['descripcion'];
        } else {
            return 'N/A';
        }
    }

    private function getCantidadPresentacion($item)
    {
        if ($item['clave_presentacion']) {
            return $item['cantidad'];
        } else if (reset($item['insumo']['presentaciones'])) {
            return round($item['cantidad'] / reset($item['insumo']['presentaciones'])['rendimiento'], 4);
        } else {
            return 'N/A';
        }
    }
    private function getCostoUnitario($item)
    {
        if ($item['clave_presentacion']) {
            return $item['costo_unitario'];
        } else if (reset($item['insumo']['presentaciones'])) {
            return round($item['costo_unitario'] * reset($item['insumo']['presentaciones'])['rendimiento'], 4);
        } else {
            return 'N/A';
        }
    }
    private function getCostoImpuesto($item)
    {
        if ($item['clave_presentacion']) {
            return $item['costo_con_impuesto'];
        } else if (reset($item['insumo']['presentaciones'])) {
            return round($item['costo_con_impuesto'] * reset($item['insumo']['presentaciones'])['rendimiento'], 4);
        } else {
            return 'N/A';
        }
    }
}
