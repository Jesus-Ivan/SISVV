<?php

namespace App\Livewire\Almacen\Clasificacion;

use App\Models\Clasificacion;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search;
    public $estado, $similarClasificaciones, $newClasificacion;

    //PROPIEDADES PARA PODER EDITAR UN REGISTRO 
    public $edit_id_clasificacion;
    public $edit_nombre_clasificacion;
    public $edit_tipo_clasificacion;
    public $edit_estado_clasificacion;

    #[Validate('required')]
    public $tipo;

    #[Validate('required|min:1|max:50')]
    public $nombre;

    //REGISTRAR NUEVA CLASIFICAION
    public function register()
    {
        try {
            $validated = $this->validate();

            //BUSCA COINCIDENCIAS CON SECUENCIA DE CARACTERES
            $regex = '/^' . preg_quote($validated['nombre'], '/') . '.*$/i';

            //BUSCAMOS NOMBRES SIMILARES
            $similarClasificacion = Clasificacion::where(function ($query) use ($validated, $regex) {
                $query->where('nombre', 'like', "%{$validated['nombre']}%")
                    ->orWhere('nombre', 'regexp', $regex);
            })->get();

            //SI SE ENCUENTRAN COINCIDENCIAS, MOSTRAMOS EL MODAL
            if (count($similarClasificacion) > 0) {
                $this->similarClasificaciones = $similarClasificacion;
                $this->newClasificacion = $validated;
                $this->dispatch('open-modal', name: 'alert');
            } else {
                //SI NO HAY COINCIDENCIAS, REGISTRAMOS LA NUEVA CLASIFICACION
                Clasificacion::create($validated);
                //MENSAJE DE ALERTA
                session()->flash('success', "Registro guardado con éxito");
                $this->reset('tipo', 'nombre');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar nuevo registro' . $e->getMessage());
        }
        $this->dispatch('open-action-message');
    }

    //GUARDAMOS EL POSIBLE REGISTRO DUPLICADO SI LO CONFIRMA DESDE EL MODAL
    public function confirmSave()
    {
        // GUARDAR EL REGISTRO
        Clasificacion::create($this->newClasificacion);
        session()->flash('success', "Registro guardado con éxito");
        $this->dispatch('open-action-message');
        $this->reset('tipo', 'nombre');
        $this->dispatch('close-modal');
    }


    public function editClasificacion(Clasificacion $clasificacion)
    {
        //EDITAR LOS ATRIBUTOS
        $this->edit_id_clasificacion = $clasificacion->id;
        $this->edit_nombre_clasificacion = $clasificacion->nombre;
        $this->edit_tipo_clasificacion = $clasificacion->tipo;
        $this->edit_estado_clasificacion = $clasificacion->estado;
    }

    public function confirmarEdit(Clasificacion $clasificacion)
    {
        //dd($clasificacion);
        $validated = $this->validate([
            'edit_nombre_clasificacion' => 'required|min:1',
            'edit_tipo_clasificacion' => 'required',
            'edit_estado_clasificacion' => 'required',
        ]);

        $clasificacion = Clasificacion::find($this->edit_id_clasificacion);

        $clasificacion->update([
            'nombre' => $validated['edit_nombre_clasificacion'],
            'tipo' => $validated['edit_tipo_clasificacion'],
            'estado' => $validated['edit_estado_clasificacion'],
        ]);

        try {
            $clasificacion->update($validated);
            session()->flash('success', "Registro Modificado correctamente");
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el registro' . $e->getMessage());
        }
        $this->dispatch('open-action-message');
        $this->reset();
    }

    public function cancelarEdit()
    {
        $this->reset();
    }

    public function render()
    {
        $result = DB::table('clasificacion_productos')
            ->where('nombre', 'like', '%' . $this->search . '%')->orWhere('id', '=', $this->search)
            ->select([
                'clasificacion_productos.id',
                'clasificacion_productos.nombre',
                'clasificacion_productos.tipo',
                'clasificacion_productos.estado',
            ])
            ->orderByRaw('clasificacion_productos.id')
            ->paginate(20);
        return view('livewire.almacen.clasificacion.principal', [
            'listaClasificacion' => $result
        ]);
    }
}
