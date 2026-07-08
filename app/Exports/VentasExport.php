<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;

class VentasExport implements FromArray
{
    protected $data;
    protected $puntos_venta, $metodo_pago;

    public function __construct(Collection $data, array $puntos_venta, array $metodo_pago)
    {
        $this->data = $data;
        $this->puntos_venta = $puntos_venta;
        $this->metodo_pago = $metodo_pago;
    }

    public function array(): array
    {
        $ventasDia[] = [
            'movimiento' => 'MOVIMIENTO',
            'folio' => 'FOLIO',
            'fecha' => 'FECHA VENTA',
            'fecha_p' => 'FECHA PAGO',
            'tipo_venta' => 'TIPO VENTA',
            'id_socio' => 'NO. SOCIO',
            'nombre_socio' => 'SOCIO',
            'total' => 'TOTAL',
            'propina' => 'PROPINA',
            'tipo_pago' => 'METODO PAGO',
            'clave_punto_venta' => 'ZONA',
            'num_comensales' => 'COMENSALES',
        ];

        foreach ($this->data as $venta) {
            foreach ($venta->detallesCaja as $key => $detalle) {
                $ventasDia[] = [
                    'movimiento' => $detalle->id,
                    'folio' => $detalle->folio_venta,
                    'fecha' => $detalle->fecha_venta,
                    'fecha_p' => $detalle->fecha_pago,
                    'tipo_venta' => $venta->tipo_venta,
                    'id_socio' => $detalle->id_socio,
                    'nombre_socio' => $detalle->nombre,
                    'total' => $detalle->monto,
                    'propina' => $detalle->propina,
                    'tipo_pago' => $this->metodo_pago[$detalle->id_tipo_pago],
                    'clave_punto_venta' => $this->puntos_venta[$venta->clave_punto_venta],
                    'num_comensales' => $key == 0 ? $venta->num_comensales : null
                ];
            }
        }
        return $ventasDia;
    }
}
