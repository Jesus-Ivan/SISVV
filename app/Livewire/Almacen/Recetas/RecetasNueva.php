<?php

namespace App\Livewire\Almacen\Recetas;

use App\Models\ICOProductos;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RecetasNueva extends Component
{
    public $cantidad;
    public $peso;
    public $descripcion;

    #[Modelable]
    public $listaIngredientes = [];

    #[Validate('required')]
    public $nombre;

    #[Validate('required')]
    public $categoria;

    #[Validate('required')]
    public $porcion;

    #[Validate('required|numeric|min:1')]
    public $precio_venta;

    #[On('añadirIngrediente')]
    public function añadir($data)
    {
        /*switch ($data['table']) {
            case 'ipa_inventario_principal':
                $articulo = InventarioPrincipal::find($data['codigo']);
                break;
            case 'ico_insumos':
                $articulo = ICOInsumos::find($data['codigo']);
                break;
        }*/

        //dd($articulo);
        array_push($this->listaIngredientes, $data);
    }

    //Eliminar produto o insumo de la lista
    public function remove($index)
    {
        unset($this->listaIngredientes[$index]);
    }

    //Confirmar y guardar receta
    public function saveReceta()
    {
        $info = $this->validate();
        $info['recetas'] = $this->listaIngredientes;
        $info['descripcion'] = $this->descripcion;
        //dd($info);

        DB::transaction(function () use ($info) {
            $resultReceta = ICOProductos::create([
                'categoria' => $info['categoria'],
                'nombre' => $info['nombre'],
                'descripcion' => $info['descripcion'],
                'porcion' => $info['porcion'],
                'precio_venta' => $info['precio_venta']
            ]);
            //Registramos la receta en la tabla correspondiente
            foreach ($info['recetas'] as $key => $listaIngredientes) {
                DB::table('recetas')
                    ->insert([
                        'codigo_producto' => $resultReceta->codigo,
                        'codigo_insumo' => ($listaIngredientes['table'] == 'ico_insumos') ? $listaIngredientes['codigo']  : null,
                        'codigo_materia_prima' => ($listaIngredientes['table'] == 'ipa_inventario_principal') ? $listaIngredientes['codigo']  : null,
                        'cantidad_requerida' => ($listaIngredientes['medida'] == 'cantidad') ? $listaIngredientes['size']  : null ,
                        'peso_requerido' => ($listaIngredientes['medida'] == 'peso') ? $listaIngredientes['size']  : null 
                    ]);
            }
            //MENSAJE DE ALERTA
            session()->flash('success', "Receta creada con exito");
            $this->dispatch('open-action-message');
            //RESETEAMOS LOS VALORES
            $this->reset([
                'nombre', 
                'descripcion', 
                'categoria', 
                'porcion', 
                'listaIngredientes', 
                'precio_venta'
            ]);
        });
    }

    public function render()
    {
        return view('livewire.almacen.recetas.recetas-nueva');
    }
}
