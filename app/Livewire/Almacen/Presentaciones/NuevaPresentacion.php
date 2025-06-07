<?php

namespace App\Livewire\Almacen\Presentaciones;

use App\Constants\AlmacenConstants;
use App\Livewire\Forms\PresentacionForm;
use App\Models\Insumo;
use App\Models\Proveedor;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NuevaPresentacion extends Component
{
    //Formulario para los datos
    public PresentacionForm $form;

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    #[Computed()]
    public function grupos()
    {
        $result = DB::table('grupos')
            ->where('tipo', AlmacenConstants::INSUMOS_KEY)
            ->get();
        return $result;
    }

    #[On('selected-insumo')]
    public function onSelectedInsumo($clave)
    {
        //Guardar valores del insumo base seleccionado
        $this->form->setInsumoBase($clave);
    }

    public function guardar()
    {
        try {
            $this->form->guardarPresentacion();
            //Mensage de session para el alert
            session()->flash('success', 'Presentacion registrada correctamente');
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

    public function limpiarInsumoBase()
    {
        $this->form->limpiarInsumoBase();
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

    public function render()
    {
        return view('livewire.almacen.presentaciones.nueva-presentacion');
    }
}
