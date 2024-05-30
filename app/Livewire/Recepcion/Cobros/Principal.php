<?php

namespace App\Livewire\Recepcion\Cobros;

use App\Models\Recibo;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public $search;

    #[Computed()]
    public function recibos()
    {
        return Recibo::where('id_socio', '=', $this->search)
            ->orWhere('nombre', 'like', '%' . $this->search . '%')
            ->orderby('created_at', 'desc')
            ->paginate(10);
    }
    public function render()
    {
        return view('livewire.recepcion.cobros.principal');
    }
}
