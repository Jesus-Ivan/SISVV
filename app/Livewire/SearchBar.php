<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;


class SearchBar extends Component
{
    public $search = '';
    public $tittle_bar;
    public $table_name, $table_columns, $primary_key;
    public $event;
    #[Reactive]
    public $conditions;


    public function mount($tittle, $table, $columns, $primary, $event, $conditions = null)
    {
        $this->tittle_bar = $tittle;
        $this->table_name = $table;
        $this->table_columns = $columns;
        $this->primary_key = $primary;
        $this->event = $event;
        $this->conditions = $conditions;
    }

    #[Computed()]
    public function results()
    {
        if ($this->search != '') {
            //Construir la consulta
            $result = DB::table($this->table_name);
            //Si tiene condiciones extras
            if ($this->conditions){
                $result->where($this->conditions);  //Agregar las condiciones extras
            }
            //Agregar la condición de búsqueda principal
            $result->whereAny($this->table_columns, 'like', '%' . $this->search . '%')
                ->take(40);
            //Devolver resultados
            return $result->get();
        } else {
            return [];
        }
    }

    public function select($id)
    {
        $this->dispatch($this->event, $id);
        $this->reset('search');
    }
    public function render()
    {
        return view('livewire.search-bar');
    }
}
