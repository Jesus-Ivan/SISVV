<?php

namespace App\Exports;

use App\Models\DetallesVentaProducto;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class VentasExport implements FromArray
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        //Agregamos el encabezado
        $ventasAux[] = [
            'folio' => 'FOLIO',
            'fecha' =>  'FECHA',
            'tipo_venta' => 'TIPO VENTA',
            'id_socio' => 'SOCIO',
            'nombre' => 'NOMBRE',
            'total' => 'TOTAL',
            'propina' => 'PROPINA',
            'clave_punto_venta' => 'ZONA',
        ];
        foreach ($this->data['detalles_pagos'] as $tipo_pago) {
            //Para cada producto de una venta
            foreach ($tipo_pago as $key => $item) {
                //Lo agregamos al array
                $ventasAux[] = [
                    'folio' => $item->folio_venta,
                    'fecha' =>  'FECHA',
                    'tipo_venta' => 'TIPO VENTA',
                    'id_socio' => 'SOCIO',
                    'nombre' => 'NOMBRE',
                    'total' => $item->monto,
                    'propina' => 'PROPINA',
                    'clave_punto_venta' => 'ZONA',
                ];
            }
        }
        //Retornamos el array listo
        return $ventasAux;
    }
}
