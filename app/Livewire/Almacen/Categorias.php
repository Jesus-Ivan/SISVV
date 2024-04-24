<?php

namespace App\Livewire\Almacen;

use App\Models\Categoria;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Categorias extends Component
{
    use WithPagination;
    public $search;
    public Categoria $accionesCategoria;

    #[Validate('required|min:5|max:20')]
    public $categoria;

    //REGISTRAMOS UNA NUEVA CATEGORIA EN LA BASE DE DATOS
    public function register()
    {
        $validated = $this->validate();
        Categoria::create($validated);
        $this->reset('categoria');
        $this->dispatch('close-modal');
        session()->flash('success', "Categoría registrada con exito");//MENSAJE DE ALERTA CUANDO SE REGISTRE CORRECTAMENTE
        $this->dispatch('open-action-message');
        $this->resetPage();
    }

    //EDITAMOS CATEGORIA DE LA BASE DE DATOS
    public function edit(Categoria $categoria)
    {
        $this->dispatch ('open-modal',  name: 'modificarCt'); //SE ABRE EL MODAL PARA PODER EDITAR
        $this->accionesCategoria = $categoria;
        $this->categoria = $categoria->categoria;
    }

    //ACTUALIZAMOS LA CATEGORIA EN LA BASE DE DATOS
    public function updateCat()
    {
        $validated = $this->validate();
        $this->accionesCategoria->update($validated);
        $this->reset('categoria');
        $this->dispatch('close-modal');
        session()->flash('success', "Cambios registrados con exito");//MENSAJE DE ALERTA CUANDO SE EDITE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    public function cancelarEdit()
    {
        $this->reset('categoria');
        $this->dispatch('close-modal');
    }

    //ELIMINAMOS CATEGORIA DE LA BASE DE DATOS
    public function delete(Categoria $categoria)
    {
        $this->dispatch('open-modal',  name: 'eliminarCt');//ABRIMOS EL MODAL PARA PODER ELIMINAR
        $this->accionesCategoria = $categoria;
    }

    //CONFIRMAMOS LA ELIMINACION DE LA CATEGORIA
    public function confirmDelete()
    {
        $this->accionesCategoria->estado = 0;
        $this->accionesCategoria->save();
        $this->dispatch('close-modal');
        session()->flash('fail', "Categoría inactivada correctamente");//MENSAJE DE ALERTA CUANDO SE ELIMINE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //REINGRESAMOS LA CATEGORIA 
    public function reingresar(Categoria $categoria)
    {
        $this->dispatch ('open-modal',  name: 'reingresarCt');//ABRIMOS EL MODAL PARA PODER REINGRESAR
        $this->accionesCategoria = $categoria;
    }

    //CONFIRMAMOS EL REINGRESO DE LA CATEGORIA
    public function confirmReingreso()
    {
        $this->accionesCategoria->estado = 1;
        $this->accionesCategoria->save();
        $this->dispatch('close-modal');
        session()->flash('success', "Categoría reingresada con exito");//MENSAJE DE ALERTA CUANDO SE REINGRESE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //UN HOOK PARA RESETEAR LA PAGINACION
    public function updated($search)
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.almacen.categorias', [
            'listaCategorias' => Categoria::where('categoria', 'like', '%' . $this->search . '%')->orWhere('id', '=', $this->search)
                ->paginate(5)
        ]);
    }
}
