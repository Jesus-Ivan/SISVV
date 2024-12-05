<?php

namespace App\Livewire\Almacen\Unidades;

use App\Models\Unidad;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search;

    #[Validate('required|min:1|max:50')]
    public $descripcion;

    //PROPIEDADES PARA PODER EDITAR UN REGISTRO 
    public $edit_id_unidad;
    public $edit_descripcion_unidad;
    public $edit_estado_unidad;

    //REGISTRAR UNIDAD EN LA BASE DE DATOS
    public function register()
    {
        try {
            $validated = $this->validate();
            Unidad::create($validated);
            session()->flash('success', 'Unidad registrado con éxito');
            $this->reset('descripcion');
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

    public function editUnidad(Unidad $unidad)
    {
        $this->edit_id_unidad = $unidad->id;
        $this->edit_descripcion_unidad = $unidad->descripcion;
        $this->edit_estado_unidad = $unidad->estado;
    }

    public function cancelarEdit()
    {
        $this->reset();
    }

    public function confirmarEdit()
    {
        //Validamos los datos editados
        $validated = $this->validate([
            'edit_descripcion_unidad' => 'required|min:1|max:50',
            'edit_estado_unidad' => 'required'
        ]);

        $unidad = Unidad::find($this->edit_id_unidad);
        
        //Actualizamos los datos en la base de datos
        $unidad->update([
            'descripcion' => $validated['edit_descripcion_unidad'],
            'estado' => $validated['edit_estado_unidad']
        ]);
        
        try {
            $unidad->update($validated);
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

    public function render()
    {
        return view('livewire.almacen.unidades.principal', [
            'listaUnidades' => Unidad::where('descripcion', 'like', '%' . $this->search . '%')->orWhere('id', '=', $this->search)
            ->paginate(10)
        ]);
    }
}
