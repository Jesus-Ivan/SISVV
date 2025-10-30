<?php

namespace App\Exports\Sheets\ProdVentas;

use App\Models\DetallesVentaProducto;
use App\Models\PuntoVenta;
use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithTitle;

class TotalVendidos implements FromArray, WithTitle
{
    protected $clave_punto, $vendidos;

    public function __construct($clave_punto, array $vendidos)
    {
        $this->clave_punto = $clave_punto;
        $this->vendidos = $vendidos;
    }

    public function array(): array
    {
        //Array auxiliar de los productos.
        $productos = [];
        //Agregamos el encabezado
        $productos[] = [
            'clave' => '#CLAVE',
            'descripcion' => 'DESCRIPCION',
            'total' => 'TOTAL',
            'precio' => 'PRECIO U.',
        ];

        //Para cada producto de una venta
        foreach ($this->vendidos as $key => $producto) {
            //Lo agregamos al array
            $productos[] = [
                'clave' => $producto['clave_producto'] ?? $producto['codigo_catalogo'],
                'descripcion' => $producto['productos']['descripcion'] ?? $producto['catalogo_productos']['nombre'],
                'total' => $producto['total_vendido'],
                'precio' => $this->getPrecio($producto)
            ];
        }


        //Retornamos el array listo
        return $productos;
    }


    private function getPrecio($producto)
    {
        if (array_key_exists('productos', $producto)) {
            if (array_key_exists('precio_con_impuestos', $producto['productos']))
                return $producto['productos']['precio_con_impuestos'];
        } else if (array_key_exists('catalogo_productos', $producto)) {
            if (array_key_exists('catalogo_productos', $producto['catalogo_productos']))
                return $producto['catalogo_productos']['costo_unitario'];
        } else {
            return 'N/R';
        }
    }


    /**
     * @return string
     */
    public function title(): string
    {
        $result = PuntoVenta::find($this->clave_punto);
        return $result ? $result->nombre : $this->clave_punto;
    }
}
