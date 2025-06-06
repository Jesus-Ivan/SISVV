<?php

namespace App\Livewire\Almacen\Grupos;

use App\Constants\AlmacenConstants;
use App\Models\Grupos;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Insumos extends Component
{
    #[Validate('required|min:2|max:200')]
    public $descripcionInsum;

    #[Validate('required')]
    public $clasificacionInsum;

    //Propiedades para poder editar un registro
    public $edit_id_insumo;
    public $edit_descripcionInsum;
    public $edit_clasificacionInsum;

    public function register()
    {
        try {
            $validated = $this->validate();
            Grupos::create([
                'descripcion' => $validated['descripcionInsum'],
                'tipo' => AlmacenConstants::INSUMOS_KEY,
                'clasificacion' => $validated['clasificacionInsum'],
            ]);
            session()->flash('success', 'Insumo registrado con éxito');
            $this->reset('descripcionInsum', 'clasificacionInsum');
        } catch (ValidationException $e) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $e;
        } catch (\Throwable $th) {
            //Enviamos flash message, al action-message (En cualquier otro caso de excepcion)
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
    }

    public function editInsumo(Grupos $insumo)
    {
        $this->edit_id_insumo = $insumo->id;
        $this->edit_descripcionInsum = $insumo->descripcion;
        $this->edit_clasificacionInsum = $insumo->clasificacion;
    }

    public function cancelarEditInsumo()
    {
        $this->reset();
    }

    public function updateInsumo(Grupos $insumo)
    {   
        //Validamos los datos editados
        $validated = $this->validate([
            'edit_descripcionInsum' => 'required|min:2|max:200',
            'edit_clasificacionInsum' => 'required'
        ]);

        $insumo = Grupos::find($this->edit_id_insumo);
        //Actualizamos en la base de datos
        $insumo->update([
            'descripcion' => $validated['edit_descripcionInsum'],
            'clasificacion' => $validated['edit_clasificacionInsum']
        ]);

        try {
            $insumo->update($validated);
            session()->flash('success', "Insumo modificado correctamente");
        } catch (ValidationException $e) {
            //Si es una excepcion de validacion, volverla a lanzar a la vista
            throw $e;
        } catch (\Throwable $th) {
            //Enviamos flash message, al action-message (En cualquier otro caso de excepcion)
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
        $this->reset();
    }

    public function deleteInsumo(Grupos $insumo)
    {
        try {
            $insumo->delete($insumo->id);
            session()->flash('success', "Insumo eliminado correctamente");
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
        $this->reset();
    }

    public function render()
    {
        $insumos = Grupos::where('tipo', '!=', 'PRODU')->get();

        return view(
            'livewire.almacen.grupos.insumos',
            [
                'clasificaciones' => [
                    AlmacenConstants::ALIMENTOS_KEY,
                    AlmacenConstants::BEBIDAS_KEY
                ],
                'listaInsumos' => $insumos
            ]
        );
    }
}
