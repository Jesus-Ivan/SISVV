<?php

namespace App\Exports;

use App\Models\DetallesVentaProducto;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductosVendExport implements FromArray
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
            'clave_catalogo' => '#CATALOGO',
            'clave_producto' => 'CLAVE PRODUCTO',
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
            //Buscamos todos los productos de la venta
            $productosVentas = DetallesVentaProducto::with('catalogoProductos')->where('folio_venta', $venta['folio'])
                ->get();
            //Para cada producto de una venta
            foreach ($productosVentas as $key => $producto) {
                //Lo agregamos al array
                $productos[] = [
                    'clave_catalogo' => $producto->codigo_catalogo,
                    'clave_producto' => $producto->clave_producto,
                    'folio_venta' => $producto->folio_venta,
                    'punto' =>  $venta['punto_venta']['nombre'],
                    'descripcion' => $producto->nombre ?: $producto->catalogoProductos->nombre,
                    'cantidad' => $producto->cantidad,
                    'precio' => $producto->precio,
                    'importe' => $producto->subtotal,
                    'fecha' => $producto['inicio'],
                    //'fecha' => substr($venta['fecha_apertura'], 0, 10),     //Removemos la hora
                    'observaciones' => $venta['observaciones']
                ];
            }
        }
        //Retornamos el array listo
        return $productos;
    }
}
