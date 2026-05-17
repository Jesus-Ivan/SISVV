<?php

namespace App\Exports\Sheets\Comandas;

use App\Models\ConceptoCancelacion;
use App\Models\DetallesVentaProducto;
use App\Models\EstadoProductoVenta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithTitle;

class Comandas implements FromArray, WithTitle
{
    protected $ventas;
    protected $estado = [];

    public function __construct(array $ventas)
    {
        $this->ventas = $ventas;
        $result = EstadoProductoVenta::all();
        foreach ($result as $key => $value) {
            $this->estado[$value['id']] = $value['descripcion'];
        }
    }

    public function array(): array
    {
        //Agregamos el encabezado
        $final[] = [
            'folio_venta' => '#VENTA',
            'punto_venta' =>  'PUNTO VENTA',
            'f_inicio' => 'FECHA INICIO',
            'f_fin' => 'FECHA FIN',
            'estado' => 'ESTADO',
            'clave' => 'CLAVE PROD.',
            'descripcion' => 'DESCRIPCION',
            'cantidad' => 'CANTIDAD',
            'observaciones' => 'OBSERVACIONES',
        ];

        foreach ($this->ventas as $key => $v) {
            foreach ($v['detalles_productos'] as $i => $prod) {
                $final[] = [
                    'folio_venta' => $prod['folio_venta'],
                    'punto_venta' =>  $v['punto_venta']['nombre'],
                    'f_inicio' => $prod['inicio'],
                    'f_fin' => $prod['terminado'],
                    'estado' => $this->estado[$prod['id_estado']],
                    'clave' => $prod['clave_producto'],
                    'descripcion' => $prod['nombre'],
                    'cantidad' => $prod['cantidad'],
                    'observaciones' => $prod['observaciones'],
                ];
            }
        }

        return $final;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Comandas';
    }
}
