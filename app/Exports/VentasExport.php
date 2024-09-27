<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class VentasExport implements FromArray
{
    protected $data;
    protected $puntos_venta, $metodo_pago;

    public function __construct(array $data, array $puntos_venta, array $metodo_pago)
    {
        dd($data);
        $this->data = $data;
        $this->puntos_venta = $puntos_venta;
        $this->metodo_pago = $metodo_pago;
    }

    public function array(): array
    {
        $ventasDia[] = [
            'folio' => 'FOLIO',
            'fecha' => 'FECHA',
            'tipo_venta' => 'TIPO VENTA',
            'id_socio' => 'NO. SOCIO',
            'nombre_socio' => 'SOCIO',
            'total' => 'TOTAL',
            'propina' => 'PROPINA',
            'tipo_pago' => 'METODO PAGO',
            'clave_punto_venta' => 'ZONA',
            'observaciones' => 'OBSERVACIONES'
        ];

        foreach ($this->data['detalles_pagos'] as $tipo_pago) {
            foreach ($tipo_pago as $key => $item) {
                $ventasDia[] = [
                    'folio' => $item->folio,
                    'fecha' => $item->fecha_apertura,
                    'tipo_venta' => $item->tipo_venta,
                    'id_socio' => $item->id_socio,
                    'nombre_socio' => $item->nombre,
                    'total' => $item->monto,
                    'propina' => $item->propina,
                    'tipo_pago' => $this->metodo_pago[$item->id_tipo_pago],
                    'clave_punto_venta' => $this->puntos_venta[$item->clave_punto_venta],
                    'observaciones' => $item->observaciones
                ];
            }
        }
        return $ventasDia;
    }
}

