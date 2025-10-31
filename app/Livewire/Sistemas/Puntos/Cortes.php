<?php

namespace App\Livewire\Sistemas\Puntos;

use App\Models\Caja;
use App\Models\PuntoVenta;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Cortes extends Component
{
    use WithPagination;
    public $fecha;
    public $fInicio, $fFin;
    public $id_usuario;

    public function updatedFInicio()
    {
        $this->resetPage();
    }

    public function updatedFFin()
    {
        $this->resetPage();
    }

    public function updatedIdUsuario()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function usuarios()
    {
        return User::all();
    }

    #[Computed()]
    public function cortesPuntos()
    {
        $query = Caja::query();
        $query->with(['users', 'puntoVenta']);

        if ($this->fInicio && $this->fFin) {
            $query->whereDate('fecha_apertura', '>=', $this->fInicio)
                ->whereDate('fecha_apertura', '<=', $this->fFin);
        }

        if ($this->id_usuario) {
            $query->where('id_usuario', $this->id_usuario);
        }

        $query->orderBy('fecha_apertura', 'desc');
        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.sistemas.puntos.cortes');
    }
}
