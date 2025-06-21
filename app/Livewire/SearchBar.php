<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchBar extends Component
{
    public $search;
    public $tittle_bar;
    public $table_name, $table_columns, $primary_key;
    public $dpto;
    public $event;
    public $conditions;


    public function mount($params)
    {
        $this->tittle_bar = $params['tittle_bar'];
        $this->table_name = $params['table_name'];
        $this->table_columns = $params['table_columns'];
        $this->primary_key = $params['primary_key'];
        $this->event = $params['event'];
        if (array_key_exists('conditions', $params)) {
            $this->conditions = $params['conditions'];
        }
    }

    #[Computed()]
    public function results()
    {
        if ($this->search != '') {
            //Construir la consulta
            $result = DB::table($this->table_name);
            //Si tiene condiciones extras
            if ($this->conditions)
                $result->where($this->conditions);  //Agregar las condiciones extras
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
