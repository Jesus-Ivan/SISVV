<?php

namespace App\Livewire\Almacen\Facturas;

use App\Constants\AlmacenConstants;
use App\Models\DetallesCompra;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RegistroNuevo extends Component
{
    //Lista de articulos para la factura
    public array $listaArticulos;
    public $selectedArticulos = [];
    
    public $folio_search;
    public $result_orden = [];

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    /**
     * En caso de contrar con una orden de compra previa, se puede buscar
     * directamente por el folio de la orden de compra para enlistar los articulos
     */
    public function buscarOrden()
    {
        if (OrdenCompra::find($this->folio_search)) {
            $result = DetallesCompra::all()
                ->where('folio_orden', $this->folio_search)
                ->toArray();
            //
            $this->result_orden = array_map(function ($row){
                $row['importe'] = 0;
            return $row;
            }, $result);
        } else {
            $this->result_orden = [];
        }
    }

    public function agregarArticulo()
    {
        
    }

    public function render()
    {
        return view(
            'livewire.almacen.facturas.registro-nuevo', [
                'metodo_pago' => AlmacenConstants::METODOS_PAGO
            ]);
    }
}
