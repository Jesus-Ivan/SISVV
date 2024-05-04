<?php

namespace App\Livewire\Almacen;

use App\Models\Proveedor;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Proveedores extends Component
{
    use WithPagination;
    public $search;
    public Proveedor $accionesProveedor;

    #[Validate('required|min:5|max:20')]
    public $proveedor;
    #[Validate('required|min:12|max:14')]
    public $rfc;
    #[Validate('required|numeric|min:1')]
    public $consumo;
    #[Validate('required|numeric|min:1')]
    public $credito_compra;

    //REGISTRAMOS EL PROVEEDOR EN LA BASE DE DATOS
    public function register()
    {
        $validated = $this->validate();
        Proveedor::create($validated);
        $this->reset('proveedor', 'rfc', 'consumo', 'credito_compra');
        $this->dispatch('close-modal');
        session()->flash('success', "Proveedor registrado con exito"); //MENSAJE DE ALERTA CUANDO SE REGISTRE CORRECTAMENTE
        $this->dispatch('open-action-message');
        $this->resetPage();
    }

    //EDITAMOS PROVEEDOR DE LA BASE DE DATOS
    public function edit(Proveedor $proveedor)
    {
        $this->dispatch('open-modal',  name: 'modificarPr'); //SE ABRE EL MODAL PARA PODER EDITAR
        $this->accionesProveedor = $proveedor;
        $this->proveedor = $proveedor->proveedor;
        $this->rfc = $proveedor->rfc;
        $this->consumo = $proveedor->consumo;
        $this->credito_compra = $proveedor->credito_compra;
    }

    //ACTUALIZAMOS LA PROVEEDOR EN LA BASE DE DATOS
    public function updatePr()
    {
        $validated = $this->validate();
        $this->accionesProveedor->update($validated);
        $this->reset('proveedor', 'rfc', 'consumo', 'credito_compra');
        $this->dispatch('close-modal');
        session()->flash('success', "Cambios registrados con exito"); //MENSAJE DE ALERTA CUANDO SE EDITE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    public function cancelarEdit()
    {
        $this->reset('proveedor', 'rfc', 'consumo', 'credito_compra');
        $this->dispatch('close-modal');
    }

    //ELIMINAMOS PROVEEDOR DE LA BASE DE DATOS
    public function delete(Proveedor $proveedor)
    {
        $this->dispatch('open-modal',  name: 'eliminarPr'); //ABRIMOS EL MODAL PARA PODER ELIMINAR
        $this->accionesProveedor = $proveedor;
    }

    //CONFIRMAMOS LA ELIMINACION DEL PROVEEDOR
    public function confirmDelete()
    {
        $this->accionesProveedor->estado = 0;
        $this->accionesProveedor->save();
        $this->dispatch('close-modal');
        session()->flash('fail', "Proveedor inactivado correctamente"); //MENSAJE DE ALERTA CUANDO SE ELIMINE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //REINGRESAMOS EL PROVEEDOR 
    public function reingresar(Proveedor $proveedor)
    {
        $this->dispatch('open-modal',  name: 'reingresarPr'); //ABRIMOS EL MODAL PARA PODER REINGRESAR
        $this->accionesProveedor = $proveedor;
    }

    //CONFIRMAMOS EL REINGRESO DEL PROVEEDOR
    public function confirmReingreso()
    {
        $this->accionesProveedor->estado = 1;
        $this->accionesProveedor->save();
        $this->dispatch('close-modal');
        session()->flash('success', "Proveedor reingresada con exito"); //MENSAJE DE ALERTA CUANDO SE REINGRESE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //UN HOOK PARA RESETEAR LA PAGINACION
    public function updated($search)
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.almacen.proveedores', [
            'listaProveedores' => Proveedor::where('proveedor', 'like', '%' . $this->search . '%')->orWhere('id', '=', $this->search)
                ->paginate(5)
        ]);
    }
}
