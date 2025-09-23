<?php

namespace App\Exports\Sheets\ProdVentas;

use App\Models\DetallesVentaProducto;
use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithTitle;

class ProdVendidos implements FromArray, WithTitle
{
    protected $ventas;

    public function __construct(array $ventas)
    {
        $this->ventas = $ventas;
    }

    public function array(): array
    {
        //Array auxiliar de los productos.
        $productos = [];
        //Agregamos el encabezado
        $productos[] = [
            'id_socio' => 'NO.SOCIO',
            'nombre' => 'NOMBRE',
            'clave_catalogo' => '#LEGACY',
            'clave_producto' => '#PRODUCTO',
            'folio_venta' => 'FOLIO VENTA',
            'punto' =>  'PUNTO',
            'descripcion' => 'DESCRIPCION',
            'cantidad' => 'CANTIDAD',
            'precio' => 'PRECIO',
            'importe' => 'IMPORTE',
            'fecha' => 'FECHA',
            'observaciones' => 'OBSERVACIONES'
        ];
        foreach ($this->ventas as $venta) {
            //Para cada producto de una venta
            foreach ($venta['detalles_productos'] as $key => $producto) {
                //Lo agregamos al array
                $productos[] = [
                    'id_socio' => $venta['id_socio'],
                    'nombre' => $venta['nombre'],
                    'clave_catalogo' => $producto['codigo_catalogo'],
                    'clave_producto' => $producto['clave_producto'],
                    'folio_venta' => $producto['folio_venta'],
                    'punto' =>  $venta['punto_venta']['nombre'],
                    'descripcion' => $producto['nombre'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'importe' => $producto['subtotal'],
                    'fecha' => $producto['inicio'],
                    'observaciones' => $venta['observaciones']
                ];
            }
        }
        //Retornamos el array listo
        return $productos;
    }


    /**
     * @return string
     */
    public function title(): string
    {
        return 'Vendidos';
    }
}
