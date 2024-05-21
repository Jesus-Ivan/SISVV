<?php

namespace App\Livewire\Almacen\Recetas;

use App\Models\ICOProductos;
use Livewire\Component;

class RecetasEditar extends Component
{
    public ICOProductos $receta;

    public $categoria;
    public $nombre;
    public $descripcion;
    public $porcion;
    public $precio_venta;

    //Ingredientes registrados en la receta
    public $listaIngredientes = [];

    public function mount()
    {
        
    }

    public function editReceta(ICOProductos $receta)
    {
        $this->receta = $receta;
        //SE GUARDAN LOS DATOS EN LAS VARIABLES PARA PODER EDITAR
        $this->categoria = $receta->categoria;
        $this->nombre = $receta->nombre;
        $this->descripcion = $receta->descripcion;
        $this->porcion = $receta->porcion;
        $this->precio_venta = $receta->precio_venta;
        $this->listaIngredientes = $receta->ingredientes;
    }

    public function confirmarEdit()
    {
        
    }

    public function render()
    {
        return view('livewire.almacen.recetas.recetas-editar');
    }
}
