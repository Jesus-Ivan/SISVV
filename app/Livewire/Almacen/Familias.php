<?php

namespace App\Livewire\Almacen;

use App\Models\Familia;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Familias extends Component
{
    use WithPagination;
    public $search;
    public Familia $accionesFamilia;

    #[Validate('required|min:4|max:30')]
    public $familia;

    //REGISTRAMOS LA FAMILIA EN LA BASE DE DATOS
    public function register()
    {
        $validated = $this->validate();
        Familia::create($validated);
        $this->reset('familia');
        $this->dispatch('close-modal');
        session()->flash('success', "Familia registrada con exito"); //MENSAJE DE ALERTA CUANDO SE REGISTRE CORRECTAMENTE
        $this->dispatch('open-action-message');
        $this->resetPage();
    }

    //EDITAMOS FAMILIA DE LA BASE DE DATOS
    public function edit(Familia $familia)
    {
        $this->dispatch('open-modal',  name: 'modificarFm'); //SE ABRE EL MODAL PARA PODER EDITAR
        $this->accionesFamilia = $familia;
        $this->familia = $familia->familia;
    }

    //ACTUALIZAMOS LA FAMILIA EN LA BASE DE DATOS
    public function updateFam()
    {
        $validated = $this->validate();
        $this->accionesFamilia->update($validated);
        $this->reset('familia');
        $this->dispatch('close-modal');
        session()->flash('success', "Cambios registrados con exito"); //MENSAJE DE ALERTA CUANDO SE EDITE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    public function cancelarEdit()
    {
        $this->reset('familia');
        $this->dispatch('close-modal');
    }

    //ELIMINAMOS FAMILIA DE LA BASE DE DATOS
    public function delete(Familia $familia)
    {
        $this->dispatch('open-modal',  name: 'eliminarFm'); //ABRIMOS EL MODAL PARA PODER ELIMINAR
        $this->accionesFamilia = $familia;
    }

    //CONFIRMAMOS LA ELIMINACION DE LA FAMILIA
    public function confirmDelete()
    {
        $this->accionesFamilia->estado = 0;
        $this->accionesFamilia->save();
        $this->dispatch('close-modal');
        session()->flash('fail', "Familia inactivada correctamente"); //MENSAJE DE ALERTA CUANDO SE ELIMINE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //REINGRESAMOS LA FAMILIA 
    public function reingresar(Familia $familia)
    {
        $this->dispatch('open-modal',  name: 'reingresarFm'); //ABRIMOS EL MODAL PARA PODER REINGRESAR
        $this->accionesFamilia = $familia;
    }

    //CONFIRMAMOS EL REINGRESO DE LA CATEGORIA
    public function confirmReingreso()
    {
        $this->accionesFamilia->estado = 1;
        $this->accionesFamilia->save();
        $this->dispatch('close-modal');
        session()->flash('success', "Familia reingresada con exito"); //MENSAJE DE ALERTA CUANDO SE REINGRESE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //UN HOOK PARA RESETEAR LA PAGINACION
    public function updated($search)
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.almacen.familias', [
            'listaFamilias' => Familia::where('familia', 'like', '%' . $this->search . '%')->orWhere('id', '=', $this->search)
                ->paginate(5)
        ]);
    }
}
