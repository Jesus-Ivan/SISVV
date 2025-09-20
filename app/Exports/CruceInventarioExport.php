<?php

namespace App\Exports;

use App\Models\Bodega;
use App\Models\DetallesEntrada;
use App\Models\Proveedor;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class CruceInventarioExport implements FromArray
{
    public $data,  $fecha;
    public $ventas, $entradas_directas, $entradas_trasp, $salida_trasp, $ajustes_inv;


    public function __construct($data, $ventas, $entradas_directas, $entradas_trasp, $salida_trasp, $ajustes_inv, $fecha)
    {
        //Resguardar los valores en las propiedades
        $this->data = $data;
        $this->ventas = $ventas;
        $this->entradas_directas = $entradas_directas;
        $this->entradas_trasp = $entradas_trasp;
        $this->salida_trasp = $salida_trasp;
        $this->ajustes_inv = $ajustes_inv;
        $fechaAux = Carbon::parse($fecha)->locale('es');
        $this->fecha = $fechaAux->shortDayName . ' ' . $fechaAux->format('d-m-Y');
    }

    public function array(): array
    {
        //Encabezados
        $encabezados = [
            'clave' => 'CLAVE',
            'descripcion' => 'DESCRIPCION',
            'unidad' => 'UNIDAD',
            'stock_sistema' => 'STOCK SISTEMA',
            'entrada_directa' => 'E.DIRECTA',
            'entrada_traspaso' => 'E.TRASPASO',
            'salida_traspaso' => 'S.TRASPASO',
            'ventas' => 'VENTAS',
            'ajuste_inventario' => 'AJUSTE.INV',
            'stock_final' => 'STOCK FINAL',
            'stock_real' => 'STOCK REAL',
        ];

        //Crear array con los datos finales
        $insumos = [];
        //Agregar fila de la fecha
        $insumos[] = [
            'clave' => '',
            'descripcion' => '',
            'unidad' => '',
            'stock_sistema' => $this->fecha,
            'entrada_directa' => '',
            'entrada_traspaso' => '',
            'salida_traspaso' => '',
            'ventas' => '',
            'ajuste_inventario' => '',
            'stock_final' => '',
            'stock_real' => '',
        ];
        //Agregar el encabezado
        $insumos[] = $encabezados;

        //Interar todos los resultados
        foreach ($this->data as $key => $item) {
            //Agreagar cada item
            $insumos[] = [
                'clave' => $item['clave'],
                'descripcion' => $item['descripcion'],
                'unidad' => $item['unidad_descripcion'],
                'stock_sistema' => $item['existencias_insumo'],
                'entrada_directa' => $this->findValue($key, $this->entradas_directas, "total_cantidad"),
                'entrada_traspaso' => $this->findValue($key, $this->entradas_trasp, "total_cantidad"),
                'salida_traspaso' => $this->findValue($key, $this->salida_trasp, "total_cantidad"),
                'ventas' => $this->findValue($key, $this->ventas, "total_cantidad"),
                'ajuste_inventario' => $this->findValue($key, $this->ajustes_inv, "total_cantidad"),
                'stock_final' => 0,
                'stock_real' => 0,
            ];
        }

        return ($insumos);
    }

    /**
     * Verifica si existe la key en el array y devuelve el su valor, en caso contrario NULL
     */
    public function findValue(string $key, array $array, $property)
    {
        return array_key_exists($key, $array) ? $array[$key][$property] : null;
    }
}
