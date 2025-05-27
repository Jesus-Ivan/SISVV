<?php

namespace App\Livewire\Almacen\Presentaciones;

use App\Livewire\Forms\PresentacionForm;
use App\Models\Proveedor;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NuevaPresentacion extends Component
{
    public PresentacionForm $form;

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    #[On('selected-insumo')]
    public function onSelectedInsumo($clave)
    {
        //Guardar clave dsel insumo base seleccionado
        $this->form->insumo_base = $clave;
    }


    public function guardarNuevo()
    {
        try {
            $this->form->guardarPresentacion();
            //Abrir el action message
            session()->flash('success','Presentacion registrada correctamente');
            $this->dispatch('open-action-message');
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function render()
    {
        return view('livewire.almacen.presentaciones.nueva-presentacion');
    }
}
