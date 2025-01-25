<?php

namespace App\Livewire\Almacen\Mermas;

use App\Models\CatalogoVistaVerde;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Principal extends Component
{
    //Propiedad que almacena el articulo seleccionado del modal
    #[Locked]
    public $articulo = ['codigo' => null, 'nombre' => null];
    //Propiedades complementarias del modal
    public $tipo_merma, $origen_merma, $cantidad, $id_unidad, $observaciones;
    
    #[On('selected-articulo')]
    public function onSelectedArticulo(CatalogoVistaVerde $articulo)
    {
        $this->articulo = $articulo->toArray();
    }



    public function render()
    {
        return view('livewire.almacen.mermas.principal');
    }
}
