<?php

namespace App\Livewire\Almacen\Articulos;

use App\Livewire\Forms\ArticulosForm;
use App\Models\CatalogoVistaVerde;
use App\Models\Clasificacion;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Component; 

class Principal extends Component
{
    use WithPagination;
    public ArticulosForm $form;
    public $search_input;
    public $radioButon;
    public $vista;

    //Iniciamos los valores por defecto
    public function mount()
    {
        //Nombre por default
        $this->search_input = '';
        //Radio button 'Mostrar Todos'                   
        $this->radioButon = 'T';
        //Select 'Mostrar Todos'           
        $this->vista = 'COM';
    }

    public function search()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function articulos()
    {
        $query = CatalogoVistaVerde::with('proveedor', 'familia', 'categoria');

        if ($this->search_input) {
            $query->where(function ($q){
                $q->where('nombre', 'like', '%' . $this->search_input . '%')
                ->orWhere('codigo', '=', $this->search_input);
            });
        }

        //Aplicamos la busqueda por radio button
        switch ($this->radioButon) {
            // Si el radio button es 'ALMACEN'
            case 'A':
                $query->where('clave_dpto', 'ALM');
                break;
            // Si el radio button es 'PUNTO DE VENTA'
            case 'PV':
                $query->where('clave_dpto', 'PV');
                break;
            // Si el radio button es 'TODOS'
            default:
                break;
        }

        //Aplicamos la busqueda por select
        switch ($this->vista) {
            //Muestra unicamente los articulos 'ACTIVOS'
            case 'ACT':
                $query->where('estado', 1);
                break;
            //Muestra unicamente los articulos 'INACTIVOS'
            case 'INA':
                $query->where('estado', 0);
                break;
            default:
                //Muestra los articulos 'COMPLETA'
                break;
        }
        
        return $query->orderByRaw('catalogo_vista_verde.codigo')
            ->whereNot('clave_dpto', 'RECEP')
            ->paginate(20);
    }



    public function render()
    {
        return view('livewire.almacen.articulos.principal');
    }
}
