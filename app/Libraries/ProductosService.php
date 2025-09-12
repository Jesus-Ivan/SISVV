<?php

namespace App\Libraries;


use App\Models\Caja;
use App\Models\DetallesVentaProducto;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\Venta;

/**
 * Clase utilizada para la explosion de materiales al final del corte de caja
 */
class ProductosService
{
    /**
     * Obtiene el total de los productos vendidos, junto a su receta
     */
    public function getTotalProductos(Caja $caja)
    {
        //Obtener total de los productos vendidos, agrupados por clave
        $totalesPorProducto = DetallesVentaProducto::whereHas('venta', function ($query) use ($caja) {
            $query->where('corte_caja', $caja->corte);
        })
            ->select('clave_producto')
            ->selectRaw('SUM(cantidad) as total_vendido')
            ->groupBy('clave_producto')
            ->get()
            ->toArray();
        $totalesPorProducto = $this->crearAsociativo($totalesPorProducto, 'clave_producto');


        //Obtener las productos con su receta.
        $productos = Producto::with('receta', 'bodega')
            ->whereIn('clave', array_column($totalesPorProducto, 'clave_producto'))
            ->get()
            ->toArray();
        $productos = $this->crearAsociativo($productos, 'clave');

        $insumos_f = [];
        //Crear el array final
        foreach ($totalesPorProducto as $key => $value) {
            $insumos_f[] = [
                'clave_producto' => $key,
                'nombre_producto' => $productos[$key]['descripcion'],
                'total_vendido' => $value['total_vendido'],
                'receta' => $productos[$key]['receta'],
                'bodega' => $productos[$key]['bodega']
            ];
        }

        return $insumos_f;
    }

    /**
     * Funcion auxiliar que crea un array asociativo con las claves listas para busqueda indexada
     */
    public function crearAsociativo(array $original, string $key_name)
    {
        $claves = array_column($original, $key_name);
        return array_combine($claves, $original);
    }
}
