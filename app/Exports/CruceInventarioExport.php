<?php

namespace App\Exports;

use App\Constants\AlmacenConstants;
use App\Exports\Sheets\Existencias\BodegaInsumo;
use App\Libraries\InventarioService;
use App\Models\Bodega;
use App\Models\Grupos;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CruceInventarioExport implements WithMultipleSheets
{
    use Exportable;
    public $clave_bodega, $fecha, $grupos, $service;

    public function __construct($clave_bodega = null, $fecha, $grupos)
    {
        $this->service = new InventarioService(); //Objeto para consultar existencias
        $this->clave_bodega = $clave_bodega;
        $this->fecha = $fecha;
        $this->grupos = Grupos::whereIn('id', $grupos)->get();
    }

    public function sheets(): array
    {
        $sheets = [];   //Definimos las hojas del excel

        if (!$this->clave_bodega) {
            //Buscar todas las bodegas
            $bodegas = Bodega::where('tipo', AlmacenConstants::BODEGA_INTER_KEY)->get();
            foreach ($bodegas as $bodega) {
                $exis = $this->obtenerInfo($bodega);
                //Agregar hojas de las existencias de los insumos
                $sheets[] = new BodegaInsumo(
                    $exis['insumos'],
                    $exis['s_vent'],
                    $exis['e_direc'],
                    $exis['e_trap'],
                    $exis['s_trap'],
                    $exis['ajuste'],
                    $this->fecha,
                    $bodega
                );
            }
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

        foreach ($this->grupos as $grupo) {
            //Consultar las existencias de un grupo de insumos
            $insumos = $this->service->consultarInsumos($grupo, $this->fecha, "00:00", $bodega->clave);
            //Obtener los movimientos
            $entradas_directas = $this->service->obtenerExistenciasConceptos([AlmacenConstants::ENT_KEY], $this->fecha, $bodega->clave);
            $entradas_trasp = $this->service->obtenerExistenciasConceptos(["ETA"], $this->fecha, $bodega->clave);
            $salida_trasp = $this->service->obtenerExistenciasConceptos(["STA"], $this->fecha, $bodega->clave);
            $ventas = $this->service->obtenerExistenciasConceptos(["SPV"], $this->fecha, $bodega->clave);
            $ajustes_inv = $this->service->obtenerExistenciasConceptos(["EPA", "SPA"], $this->fecha, $bodega->clave);
            //Del resultado, agregarlo a los datos, de forma indexada.
            foreach ($insumos as $row_insumo) {
                $data[$row_insumo['clave']] = $row_insumo;
            }
        }

        return [
            'insumos' => $data,
            'e_direc' => $entradas_directas,
            'e_trap' => $entradas_trasp,
            's_trap' => $salida_trasp,
            's_vent' => $ventas,
            'ajuste' => $ajustes_inv
        ];
    }
}
