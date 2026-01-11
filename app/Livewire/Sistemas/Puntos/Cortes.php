<?php

namespace App\Livewire\Sistemas\Puntos;

use App\Libraries\ProductosService;
use App\Models\Caja;
use App\Models\PuntoVenta;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

    public function generarExplosion(Caja $caja)
    {
        $productoServ = new ProductosService;
        try {
            DB::transaction(function () use ($caja, $productoServ) {
                //Obtener los productos vendidos en el corte de caja
                $productos = $productoServ->getTotalProductos($caja);
                //Descontar exitencias
                if (config('app.discount_stock'))
                    $productoServ->descontarStock($productos, $caja);
            });
            session()->flash("success", "Explosion generada correctamente");
        } catch (\Throwable $th) {
            session()->flash("fail", $th->getMessage());
        }
        $this->dispatch("explosion-insumo");
    }

    public function render()
    {
        return view('livewire.sistemas.puntos.cortes');
    }
}
