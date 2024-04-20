<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Autocomplete extends Component
{

    public $table, $search;

    #[Computed]
    public function results(){
        if(isset($this->search)){
            return DB::table($this->table)->where('nombre','like','%'.$this->search.'%')->take(10)->get();
        }else{
            return [];
        }
    }

    public function select(int $socioId){
        $this->dispatch('on-selected-result', $socioId);
        $this->reset('search');
    }
    public function render()
    {
        return view('livewire.autocomplete');
    }
}
