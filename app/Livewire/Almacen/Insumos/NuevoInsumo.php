<?php

namespace App\Livewire\Almacen\Insumos;

use App\Constants\AlmacenConstants;
use App\Livewire\Forms\InsumoForm;
use App\Models\Unidad;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NuevoInsumo extends Component
{
    public InsumoForm $form;


    #[Computed()]
    public function grupos()
    {
        $result = DB::table('grupos')
            ->where('tipo', AlmacenConstants::GRUPO_INSUMO_KEY)
            ->get();
        return $result;
    }

    #[Computed()]
    public function unidades()
    {
        return Unidad::all();
    }

    #[On('selected-insumo')]
    public function onSelectedInsumo($clave)
    {
        //Guardar el insumo seleccionado
        $this->form->agregarInsumo($clave);
    }

    public function eliminarInsumo($indexSubTable){
        $this->form->eliminarInsumoSeleccionado($indexSubTable);
    }


    public function guardar()
    {
        try {
            $this->form->guardarNuevoInsumo();
            //Mensage de session para el alert
            session()->flash('success', 'Insumo registrado exitosamente');
            //Evento para abrir el alert
            $this->dispatch('open-action-message');
        } catch (ValidationException $th) {
            //Lanzar la excepcion de validacion a la vista
            throw $th;
        } catch (Exception $e) {
            session()->flash('fail', $e->getMessage());
            //Evento para abrir el alert
            $this->dispatch('open-action-message');
        }
    }

    
    public function changedCosto()
    {
        $this->form->calcularPrecioIva();
    }

    public function changedIva()
    {
        $this->form->calcularPrecioIva();
    }
    public function changedCostoIva()
    {
        $this->form->calcularPrecioSinIva();
    }

    public function changedCantidad(){
        $this->form->recalcularSubtotales();
    }

    public function render()
    {
        return view('livewire.almacen.insumos.nuevo-insumo');
    }
}
