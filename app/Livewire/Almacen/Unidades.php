<?php

namespace App\Livewire\Almacen;

use App\Models\Unidad;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Unidades extends Component
{
    use WithPagination;
    public $search;
    public Unidad $accionesUnidad;

    #[Validate('required:min:5|max:20')]
    public $unidad;

    //REGISTRAMOS UNA NUEVA UNIDAD EN LA BASE DE DATOS
    public function register()
    {
        $validated = $this->validate();
        Unidad::create($validated);
        $this->reset('unidad');
        $this->dispatch('close-modal');
        session()->flash('success', "Unidad registrada con exito");//MENSAJE DE ALERTA CUANDO SE REGISTRE CORRECTAMENTE
        $this->dispatch('open-action-message');
        $this->resetPage();
    }

    //EDITAMOS UNIDAD DE LA BASE DE DATOS
    public function edit(Unidad $unidad)
    {
        $this->dispatch ('open-modal',  name: 'modificarUd'); //SE ABRE EL MODAL PARA PODER EDITAR
        $this->accionesUnidad = $unidad;
        $this->unidad = $unidad->unidad;
    }

    //ACTUALIZAMOS LA UNIDAD EN LA BASE DE DATOS
    public function updateUd()
    {
        $validated = $this->validate();
        $this->accionesUnidad->update($validated);
        $this->reset('unidad');
        $this->dispatch('close-modal');
        session()->flash('success', "Cambios registrados con exito");//MENSAJE DE ALERTA CUANDO SE EDITE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    public function cancelarEdit()
    {
        $this->reset('unidad');
        $this->dispatch('close-modal');
    }

    //ELIMINAMOS UNIDAD DE LA BASE DE DATOS
    public function delete(Unidad $unidad)
    {
        $this->dispatch('open-modal',  name: 'eliminarUd');//ABRIMOS EL MODAL PARA PODER ELIMINAR
        $this->accionesUnidad = $unidad;
    }

    //CONFIRMAMOS LA ELIMINACION DE LA CATEGORIA
    public function confirmDelete()
    {
        $this->accionesUnidad->estado = 0;
        $this->accionesUnidad->save();
        $this->dispatch('close-modal');
        session()->flash('fail', "Unidad inactivada correctamente");//MENSAJE DE ALERTA CUANDO SE ELIMINE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //REINGRESAMOS LA UNIDAD 
    public function reingresar(Unidad $unidad)
    {
        $this->dispatch ('open-modal',  name: 'reingresarUd');//ABRIMOS EL MODAL PARA PODER REINGRESAR
        $this->accionesUnidad = $unidad;
    }

    //CONFIRMAMOS EL REINGRESO DE LA UNIDAD
    public function confirmReingreso()
    {
        $this->accionesUnidad->estado = 1;
        $this->accionesUnidad->save();
        $this->dispatch('close-modal');
        session()->flash('success', "Unidad reingresada con exito");//MENSAJE DE ALERTA CUANDO SE REINGRESE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //UN HOOK PARA RESETEAR LA PAGINACION
    public function updated($search)
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.almacen.unidades', [
            'listaUnidades' => Unidad::where('unidad', 'like', '%' . $this->search . '%')->orWhere('id', '=', $this->search)
                ->paginate(5)
        ]);
    }
}
