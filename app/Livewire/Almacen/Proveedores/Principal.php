<?php

namespace App\Livewire\Almacen\Proveedores;

use App\Livewire\Almacen\Proveedores;
use App\Models\Proveedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search;
    public $estado;

    #[Validate('required|min:3|max:50')]
    public $nombre;
    #[Validate('required|min:12|max:14')]
    public $rfc;
    #[Validate('numeric')]
    public $consumo;
    #[Validate('numeric')]
    public $credito_compra;

    /*public $proveedor_rules = [
        'nombre' => 'required|min:5|max:20',
        'rfc' => 'required|min:12|max:14',
        'consumo' => 'numeric|min:1',
        'credito_compra' => 'numeric|min:1',
    ];*/

    //PROPIEDADES PARA EDITAR UN REGISTRO
    public $editar_id;
    public $editar_nombre;
    public $editar_rfc;
    public $editar_consumo;
    public $editar_credito_compra;
    public $editar_estado;

    //REGISTRAR NUEVO PROVEEDOR EN LA BASE DE DATOS
    public function register()
    {
        try {
            $validated = $this->validate();
            Proveedor::create($validated);
            session()->flash('success', 'Proveedor registrado con éxito');
            $this->reset('nombre', 'rfc', 'consumo', 'credito_compra');
            $this->dispatch('close-modal');
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

    public function editProveedor(Proveedor $proveedor)
    {
        $this->editar_id = $proveedor->id;
        $this->editar_nombre = $proveedor->nombre;
        $this->editar_rfc = $proveedor->rfc;
        $this->editar_consumo = $proveedor->consumo;
        $this->editar_credito_compra = $proveedor->credito_compra;
        $this->editar_estado = $proveedor->estado;
    }

    public function cancelarEdit()
    {
        $this->reset();
    }

    public function confirmarEdit()
    {
        //Validamos los datos editados
        $validated = $this->validate([
            'editar_nombre' => 'required|min:3|max:50',
            'editar_rfc' => 'required|min:12|max:14',
            'editar_consumo' => 'numeric',
            'editar_credito_compra' => 'numeric',
            'editar_estado' => 'required'
        ]);
        $proveedor = Proveedor::find($this->editar_id);
        
        //Actualizamos los datos del proveedor
        $proveedor->update([
            'nombre' => $validated['editar_nombre'],
            'rfc' => $validated['editar_rfc'],
            'consumo' => $validated['editar_consumo'],
            'credito_compra' => $validated['editar_credito_compra'],
            'estado' => $validated['editar_estado']
        ]);

        try {
            $proveedor->update($validated);
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
        return view('livewire.almacen.proveedores.principal', [
            'listaProveedores' => Proveedor::where('nombre', 'like', '%' . $this->search . '%')->orWhere('id', '=', $this->search)
                ->paginate(20)
        ]);
    }
}
