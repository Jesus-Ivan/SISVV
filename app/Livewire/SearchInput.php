<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchInput extends Component
{
    public $search;
    public $tittle_bar;
    public $table_name, $table_columns, $primary_key;
    public $args;
    public $event;


    public function mount($params)
    {
        $this->tittle_bar = $params['tittle_bar'];
        $this->table_name = $params['table_name'];
        $this->table_columns = $params['table_columns'];
        $this->primary_key = $params['primary_key'];
        $this->event = $params['event'];
        $this->args = $params['args'];
    }

    #[Computed()]
    public function results()
    {
        if ($this->search != '') {
            return DB::table($this->table_name)
                ->whereAny($this->table_columns, 'like', '%' . $this->search . '%')
                ->where('tipo', 'like', '%' . $this->args . '%')
                ->where('estado', 1)
                ->take(40)
                ->get();
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
        return view('livewire.search-input');
    }
}
