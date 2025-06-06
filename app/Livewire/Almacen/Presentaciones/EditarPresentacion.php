<?php

namespace App\Livewire\Almacen\Presentaciones;

use App\Constants\AlmacenConstants;
use App\Livewire\Forms\PresentacionForm;
use App\Models\Presentacion;
use App\Models\Proveedor;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class EditarPresentacion extends Component
{
    public PresentacionForm $form;     //Formulario

    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }

    #[Computed()]
    public function grupos()
    {
        $result = DB::table('grupos')
            ->where('tipo', AlmacenConstants::GRUPO_INSUMO_KEY)
            ->get();
        return $result;
    }

    #[On('selected-insumo')]
    public function onSelectedInsumo($clave)
    {
        //Guardar clave del insumo base seleccionado
        $this->form->setInsumoBase($clave);
    }

    public function mount($clave)
    {
        //Buscar la presentacion
        $presentacion = Presentacion::find($clave);
        if ($presentacion) {
            //Setar los valores editables en el form
            $this->form->setValues($presentacion);
        }else{
            //redirigir al usuario en caso de no existir la presentacion
            $this->redirectRoute('almacen.presentaciones');
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

    public function guardar()
    {
        try {
            //Intentar guardar los cambios
            $this->form->guardarCambios();
            //redirigir al usuario en caso de exito
            $this->redirectRoute('almacen.presentaciones');
        } catch (ValidationException $th) {
            //Lanzar la excepcion de validacion a la vista
            throw $th;
        } catch (Exception $e) {
            session()->flash('fail', $e->getMessage());
            $this->dispatch('open-action-message');
        }
    }

    public function render()
    {
        return view('livewire.almacen.presentaciones.editar-presentacion');
    }
}
