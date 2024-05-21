<?php

namespace App\Livewire\Almacen\Recetas;

use App\Models\ICOInsumos;
use App\Models\InventarioPrincipal;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalIngredientes extends Component
{
    public $selectedIngrediente;
    public $selectedMedida;
    public $size;
    public $table;
    public $peso, $cantidad;


    #[On('on-selected-materia')]
    public function onSelectedMateria(InventarioPrincipal $data)
    {
        $this->selectedIngrediente = $data->toArray();
        $this->table = 'ipa_inventario_principal';
    }

    #[On('on-selected-insumo')]
    public function onSelectedInsumo(ICOInsumos $data)
    {
        $this->selectedIngrediente = $data->toArray();
        $this->table = 'ico_insumos';
    }

    public function onSelectedArticulo($codigo)
    {
        if ($this->selectedIngrediente === 'materia') {
            //Busca mediante la tabla de inventario principal
            $articulo = InventarioPrincipal::where('codigo', $codigo)->first();
        } else if ($this->selectedIngrediente === 'insumo') {
            //Busca mediante la tabla de insumos
            $articulo = ICOInsumos::where('codigo', $codigo)->first();
        }
    }

    public function agregarIngrediente()
    {
        $validated = $this->validate([
            'selectedIngrediente' => 'required',
            'selectedMedida' => 'required',
            'size' => 'required|numeric|min:0.001'
        ]);
        //dd($validated);

        // EMITIMOS EL EVENTO PARA AGREGAR EL INGREDIENTE
        $this->dispatch('añadirIngrediente', [
            'table' => $this->table,
            'codigo' => $this->selectedIngrediente['codigo'],
            'nombre' => $this->selectedIngrediente['nombre'],
            'medida' => $this->selectedMedida,
            'size' => $this->size
        ]);
        // EMITIMOS EL EVENTO PARA CERRAR EL MODAL
        $this->dispatch('close-modal');
        $this->reset();
        //dump($validated);
    }

    public function render()
    {
        return view('livewire.almacen.recetas.modal-ingredientes');
    }
}
