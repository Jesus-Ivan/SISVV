<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Autocomplete extends Component
{

    public $params, $search, $event;

    #[Computed]
    public function results()
    {
        if ( $this->search != '') {
            if (count($this->params['columns']) == 2) {
                return DB::table($this->params['table_name'])
                    ->where($this->params['columns'][0], 'like', '%' . $this->search . '%')
                    ->orWhere($this->params['columns'][1], 'like', $this->search)
                    ->take(10)
                    ->get();
            } else {
                return DB::table($this->params->table_name)
                    ->where($this->params['columns'][0], 'like', '%' . $this->search . '%')
                    ->take(10)
                    ->get();
            }
        } else {
            return [];
        }
    }

    public function select(int $socioId)
    {
        $this->dispatch($this->event, $socioId);
        $this->reset('search');
    }
    public function render()
    {
        return view('livewire.autocomplete');
    }
}
