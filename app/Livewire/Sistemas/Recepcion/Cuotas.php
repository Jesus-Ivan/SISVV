<?php

namespace App\Livewire\Sistemas\Recepcion;

use App\Models\Cuota;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Cuotas extends Component
{
    use WithPagination;
    public $search;

    #[Validate('required|min:3|max:50')]
    public $descripcion;
    #[Validate('required|min:12|max:14')]
    public $tipo;
    #[Validate('numeric')]
    public $monto;

    //PROPIEDADES PARA EDITAR UN REGISTRO
    public $editar_id;
    public $editar_descripcion;
    public $editar_tipo;
    public $editar_monto;

    public function editCuota(Cuota $cuota)
    {
        $this->editar_id = $cuota->id;
        $this->editar_descripcion = $cuota->descripcion;
        $this->editar_tipo = $cuota->tipo;
        $this->editar_monto = $cuota->monto;
    }

    public function cancelarEdit()
    {
        $this->reset();
    }

    public function confirmarEdit()
    {
        //Validamos los datos editados
        $validated = $this->validate([
            'editar_descripcion' => 'required|min:3|max:250',
            'editar_tipo' => 'required|min:3|max:20',
            'editar_monto' => 'numeric'
        ]);
        $cuota = Cuota::find($this->editar_id);

        $cuota->update([
            'descripcion' => $validated['editar_descripcion'],
            'tipo' => $validated['editar_tipo'],
            'monto' => $validated['editar_monto'],
        ]);

        try {
            $cuota->update($validated);
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

    #[Computed()]
    public function cuotas()
    {
        $query = Cuota::query()
            ->whereAny(['descripcion', 'id'], 'like', "%$this->search%");
        return $query->orderBy('id', 'ASC')->paginate(15);
    }

    public function render()
    {
        return view('livewire.sistemas.recepcion.cuotas');
    }
}
