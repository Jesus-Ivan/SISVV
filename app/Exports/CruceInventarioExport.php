<?php

namespace App\Exports;

use App\Constants\AlmacenConstants;
use App\Exports\Sheets\Existencias\BodegaInsumo;
use App\Libraries\InventarioService;
use App\Models\Bodega;
use App\Models\ConceptoAlmacen;
use App\Models\Grupos;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CruceInventarioExport implements WithMultipleSheets
{
    use Exportable;
    public $clave_bodega, $fecha, $fecha_fin, $grupos, $service;
    public $conceptos;

    public function __construct($clave_bodega = null, $fecha, $fecha_fin, $grupos)
    {
        $this->service = new InventarioService(); //Objeto para consultar existencias
        $this->clave_bodega = $clave_bodega;
        $this->fecha = $fecha;
        $this->fecha_fin = $fecha_fin;
        $this->grupos = Grupos::whereIn('id', $grupos)->get();
        $this->conceptos = ConceptoAlmacen::all();
    }

    public function sheets(): array
    {
        $sheets = [];   //Definimos las hojas del excel

        if (!$this->clave_bodega) {
            //Buscar todas las bodegas
            $bodegas = Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
            foreach ($bodegas as $bodega) {
                $exis_mov = $this->obtenerInfo($bodega);
                //Agregar hojas de las existencias de los insumos
                $sheets[] = new BodegaInsumo(
                    $exis_mov,
                    $this->fecha,
                    $this->fecha_fin,
                    $bodega
                );
            }
        } else {
            //Buscar una sola bodega
            $bodega = Bodega::find($this->clave_bodega);
            //Obtener las existencias y los movimientos
            $exis_mov = $this->obtenerInfo($bodega);

            //Agregar hoja de existencias de los insumos
            $sheets[] = new BodegaInsumo(
                $exis_mov,
                $this->fecha,
                $this->fecha_fin,
                $bodega
            );
        }
        return $sheets;
    }

    /**
     * Obtiene los datos necesarios para el cruze de existencias-ventas
     */
    private function obtenerInfo(Bodega $bodega)
    {
        //Insumos iniciales a consultar 
        $data = [];
        $movimientos = [];
        //Crear fecha un dia antes de la fecha de inicio del reporte
        $fecha_existencias = Carbon::parse($this->fecha)->subDay()->toDateString();

        foreach ($this->grupos as $grupo) {
            //Consultar las existencias de un grupo de insumos
            $insumos = $this->service->consultarInsumos($grupo, $fecha_existencias, "23:59", $bodega->clave);

            //Obtener los movimientos por concepto
            foreach ($this->conceptos as $key => $concepto) {
                $movimientos[$concepto->clave] =
                    $this->service->obtenerMovimientosConceptos(
                        [$concepto->clave],
                        $this->fecha,
                        $this->fecha_fin,
                        $bodega->clave
                    );
            }
            //Del resultado, agregarlo a los datos, de forma indexada.
            foreach ($insumos as $row_insumo) {
                $data[$row_insumo['clave']] = $row_insumo;
            }
        }

        return [
            'insumos' => $data,
            ...$movimientos
        ];
    }
}
