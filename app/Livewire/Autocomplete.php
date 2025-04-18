<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Autocomplete extends Component
{

    public $params, $search = '', $event, $primaryKey;

    #[Computed]
    public function results()
    {
        //dd($this->params);
        if ($this->search != '') {
            return DB::table($this->params['table']['name'])
                ->whereAny($this->params['table']['columns'], 'like', '%' . $this->search . '%')
                ->take(30)
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
        return view('livewire.autocomplete');
    }
}
