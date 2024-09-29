<?php

namespace App\Livewire\Almacen\Entradas;

use App\Models\Entrada as ModelsEntrada;
use App\Models\OrdenCompra;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Entrada extends Component
{
    public $folio_search,  $orden_result = [];

    public function buscarOrden()
    {
        //Si existe una orden de compra
        if (OrdenCompra::find($this->folio_search)) {
            //Buscar los detalles de la orden de compra
            $this->orden_result = DB::table('detalles_compras')
                ->where('folio_orden', $this->folio_search)
                ->get()
                ->toArray();
        }
        
    }
    #[Computed()]
    public function entradas()
    {
        return ModelsEntrada::all();
    }
    public function render()
    {
        return view('livewire.almacen.entradas.entrada');
    }
}
