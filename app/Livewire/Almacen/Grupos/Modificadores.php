<?php

namespace App\Livewire\Almacen\Grupos;

use App\Models\GruposModificadores;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Modificadores extends Component
{
    #[Validate('required|min:2|max:200')]
    public $descripcionModif;

    //Propiedades para editar un registro
    public $edit_id_modificador;
    public $edit_descripcionModif;

    public function register()
    {
        try {
            $validated = $this->validate();
            GruposModificadores::create([
                'descripcion' => $validated['descripcionModif']
            ]);
            session()->flash('success', 'Modificador registrado con éxito');
            $this->reset('descripcionModif');
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

    public function editModificador(GruposModificadores $modificador)
    {
        $this->edit_id_modificador = $modificador->id;
        $this->edit_descripcionModif = $modificador->descripcion;
    }

    public function cancelarEditInsumo()
    {
        $this->reset();
    }

    public function updateModificador(GruposModificadores $modificador)
    {
        //Validamos los datos editados
        $validated = $this->validate([
            'edit_descripcionModif' => 'required|min:2|max:200'
        ]);

        $modificador = GruposModificadores::find($this->edit_id_modificador);
        //Actualizamos en la base de datos
        $modificador->update([
            'descripcion' => $validated['edit_descripcionModif']
        ]);

        try {
            $modificador->update($validated);
            session()->flash('success', "Registro modificado correctamente");
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

    public function deleteModificador(GruposModificadores $modificador)
    {
        try {
            $modificador->delete($modificador->id);
            session()->flash('success', "Modificador eliminado correctamente");
        } catch (\Throwable $th) {
            session()->flash('fail', $th->getMessage());
        }
        //Emitir evento para mostrar el action-message
        $this->dispatch('open-action-message');
        $this->reset();
    }

    public function render()
    {
        $result = DB::table('grupos_modificadores')->get();
        return view('livewire.almacen.grupos.modificadores', [
            'listaModificadores' => $result
        ]);
    }
}
