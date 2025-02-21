<?php

namespace App\Livewire\Almacen\Recetas;

use App\Models\ICOProductos;
use Livewire\Component;
use Livewire\WithPagination;

class Principal extends Component
{
    use WithPagination;
    public ICOProductos $cartaPlatillos;
    public $search;
    public $codigo;

    //ELIMINAMOS PLATILLO DE LA BASE DE DATOS
    public function delete(ICOProductos $codigo)
    {
        $this->dispatch('open-modal',  name: 'eliminarPlat'); //ABRIMOS EL MODAL PARA PODER ELIMINAR
        $this->cartaPlatillos = $codigo;
    }

    //CONFIRMAMOS LA ELIMINACION DEL PLATILLO
    public function confirmDelete()
    {
        $this->cartaPlatillos->estado = 0;
        $this->cartaPlatillos->save();
        $this->dispatch('close-modal');
        session()->flash('fail', "Platillo inactivado correctamente"); //MENSAJE DE ALERTA CUANDO SE ELIMINE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //REINGRESAMOS EL PLATILLO 
    public function reingresar(ICOProductos $codigo)
    {
        $this->dispatch('open-modal',  name: 'reingresarPlat'); //ABRIMOS EL MODAL PARA PODER REINGRESAR
        $this->cartaPlatillos = $codigo;
    }

    //CONFIRMAMOS EL REINGRESO DEL PLATILLO
    public function confirmReingreso()
    {
        $this->cartaPlatillos->estado = 1;
        $this->cartaPlatillos->save();
        $this->dispatch('close-modal');
        session()->flash('success', "Platillo reingresado con exito"); //MENSAJE DE ALERTA CUANDO SE REINGRESE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //UN HOOK PARA RESETEAR LA PAGINACION
    public function updated($search)
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.almacen.recetas.principal', [
            'listaPlatillos' => []
        ]);
    }
}
