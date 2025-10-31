<?php

namespace App\Exports\Sheets\Existencias;

use App\Models\Bodega;
use App\Models\ConceptoAlmacen;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class BodegaInsumo implements FromArray, WithTitle
{
    public $exis_insum;
    public $mov;
    public $fecha_exis, $fecha_inicio, $fecha_fin;
    public Bodega $bodega;

    public function __construct(array $exis_mov, $fecha_inicio, $fecha_fin, Bodega $bodega)
    {
        //Guardar por separado las existencias de los insumos
        $this->exis_insum = $exis_mov['insumos'];
        //Guardar por separado los movimientos de los insumos
        unset($exis_mov['insumos']);
        $this->mov = $exis_mov;
        //Fechas auxiliares
        $fecha_i = Carbon::parse($fecha_inicio)->locale('es');
        $fecha_f = Carbon::parse($fecha_fin)->locale('es');
        $fecha_e = Carbon::parse($fecha_inicio)->subDay()->locale('es');
        $this->fecha_inicio = $fecha_i->shortDayName . ' ' . $fecha_i->format('d-m-Y');
        $this->fecha_fin = $fecha_f->shortDayName . ' ' . $fecha_f->format('d-m-Y');
        $this->fecha_exis = $fecha_e->shortDayName . ' ' . $fecha_e->format('d-m-Y');
        $this->bodega = $bodega;
    }

    public function array(): array
    {
        //Buscar todos los conceptos de almacen
        $conceptos = ConceptoAlmacen::all();
        //Array auxiliar de los conceptos
        $encabezado_concepto = [];
        //crear el array (clave - descripcion)
        foreach ($conceptos as $key => $c) {
            $encabezado_concepto[$c->clave] = $c->descripcion;
        }

        //Concatenar los encabezados
        $encabezados = [
            'clave' => 'CLAVE',
            'descripcion' => 'DESCRIPCION',
            'unidad' => 'UNIDAD',
            'stock_sistema' => 'STOCK SISTEMA',
            ...$encabezado_concepto,
            'stock_final' => 'STOCK FINAL',
            'stock_real' => 'STOCK REAL',
        ];

        //Crear array con los datos finales
        $insumos = [];
        //Agregar fila (primera) de la fecha
        $insumos[] = [
            'clave' => '',
            'descripcion' => '',
            'unidad' => '',
            'stock_sistema' => $this->fecha_exis,
            '0' => '',
            '1' => $this->fecha_inicio,
            '2' => '--->',
            '3' => $this->fecha_fin,
        ];
        //Agregar el encabezado (segunda fila)
        $insumos[] = $encabezados;

        //Interar todos los resultados
        foreach ($this->exis_insum as $key => $insumo) {
            //Definir fila auxiliar con columnas iniciales
            $row_aux = [];
            $row_aux['clave'] = $key;
            $row_aux['descripcion'] = $insumo['descripcion'];
            $row_aux['unidad'] = $insumo['unidad_descripcion'];
            $row_aux['stock_sistema'] = $insumo['existencias_insumo'];

            foreach ($this->mov as $key_mov => $mov_total) {
                $row_aux[$key_mov] = $mov_total[$key]["total_cantidad"] ?? null;
            }

            //Agreagar cada item
            array_push($insumos, $row_aux);
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
