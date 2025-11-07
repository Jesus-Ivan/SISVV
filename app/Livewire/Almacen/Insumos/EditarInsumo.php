<?php

namespace App\Livewire\Almacen\Insumos;

use App\Constants\AlmacenConstants;
use App\Livewire\Forms\InsumoForm;
use App\Models\Insumo;
use App\Models\Unidad;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class EditarInsumo extends Component
{
    public InsumoForm $form;

    public function mount($clave)
    {
        //Buscar el insumo
        $insumo = Insumo::find($clave);
        if ($insumo) {
            //Setar los valores editables en el form
            $this->form->setValues($insumo);
        } else {
            //redirigir al usuario en caso de no existir la presentacion
            $this->redirectRoute('almacen.insumos');
        }
    }

    #[Computed()]
    public function grupos()
    {
        $result = DB::table('grupos')
            ->where('tipo', AlmacenConstants::INSUMOS_KEY)
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

    public function eliminarInsumo($indexSubTable)
    {
        $this->form->marcarInsumo($indexSubTable);
    }


    public function guardar()
    {
        try {
            $this->form->actualizarInsumo();
            //Mensage de session para el alert
            session()->flash('success', 'Insumo actualizado exitosamente');
            //redirigir al usuario en caso de exito
            $this->redirectRoute('almacen.insumos');
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

    public function changedCantidad($index)
    {
        $this->form->recalcularSubtotales($index);
    }

    public function render()
    {
        return view('livewire.almacen.insumos.editar-insumo');
    }
}
