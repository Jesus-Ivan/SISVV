<?php

namespace App\Libraries;

use App\Constants\AlmacenConstants;
use App\Models\Caja;
use App\Models\DetallesVentaProducto;
use App\Models\Insumo;
use App\Models\MovimientosAlmacen;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Exception;

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
     * Crea los movimientos de la bodega, para descontar del inventario
     */
    public function descontarStock($productos, Caja $caja)
    {
        //Obtener todos los insumos
        $insumos = Insumo::all();

        foreach ($productos as $prod) {
            //Si el producto a iterar no tiene receta
            if (!count($prod['receta']))
                continue;   //Omitir iteracion

            //Buscamos la bodega de donde se descontara el producto
            $bodega = array_filter($prod['bodega'], function ($bod) use ($caja) {
                return $bod['clave_punto'] == $caja->clave_punto_venta;
            });
            //Validacion
            if (!count($bodega)) {
                throw new Exception(
                    "Falta propiedad Producto-Bodega: "
                        . $prod['nombre_producto']
                        . ", Para el punto: "
                        . $caja->clave_punto_venta
                );
            }
            //Fechas auxiliares
            $f_apertura = Carbon::parse($caja->fecha_apertura);
            $f_existencias = Carbon::parse($caja->fecha_cierre);
            //Si la fecha de apertura es diferente a la de existencias
            if (!$f_apertura->isSameDay($f_existencias)) {
                //Modificar la fecha, para que coincida con la fecha de existencias ('movimientos_almacen')
                $f_existencias->hours(23)->minutes(30)->seconds(00);
            }
            foreach ($prod['receta'] as $key => $insumo) {
                MovimientosAlmacen::create([
                    'corte_caja' => $caja->corte,
                    'clave_concepto' => AlmacenConstants::SAL_VENTA_KEY,
                    'clave_insumo' => $insumo['clave_insumo'],
                    'descripcion' => $insumos->find($insumo['clave_insumo'])->descripcion,
                    'clave_bodega' => reset($bodega)['clave_bodega'],
                    'cantidad_insumo' => -1 * ($insumo['cantidad'] * $prod['total_vendido']),
                    'costo' => 0,
                    'iva' => 0,
                    'costo_con_impuesto' => 0,
                    'importe' => 0,
                    'fecha_existencias' => $f_existencias->toDateTimeString(),
                ]);
            }
        }
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
