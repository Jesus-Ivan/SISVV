<?php

namespace App\Exports\Sheets\Existencias;

use App\Models\Bodega;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class BodegaInsumo implements FromArray, WithTitle
{
    public $data,  $fecha;
    public Bodega $bodega;
    public $ventas, $entradas_directas, $entradas_trasp, $salida_trasp, $ajustes_inv;


    public function __construct($data, $ventas, $entradas_directas, $entradas_trasp, $salida_trasp, $ajustes_inv, $fecha, Bodega $bodega)
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
        $this->bodega = $bodega;
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
                'entrada_directa' => $this->entradas_directas[$key]["total_cantidad"] ?? null,
                'entrada_traspaso' => $this->entradas_trasp[$key]["total_cantidad"] ?? null,
                'salida_traspaso' => $this->salida_trasp[$key]["total_cantidad"] ?? null,
                'ventas' => $this->ventas[$key]["total_cantidad"] ?? null,
                'ajuste_inventario' => $this->ajustes_inv[$key]["total_cantidad"] ?? null,
                'stock_final' => 0,
                'stock_real' => 0,
            ];
        }

        return $insumos;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->bodega->descripcion;
    }
}
