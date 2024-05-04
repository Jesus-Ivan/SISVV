<?php

namespace App\Livewire\Almacen;

use App\Models\Categoria;
use App\Models\Familia;
use App\Models\InventarioPrincipal;
use App\Models\Proveedor;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Articulos extends Component
{
    use WithPagination;
    public $search;
    public InventarioPrincipal $inventarioPrincipal;
    public $codigo;
    public $stock;

    #[Validate('required')]
    public $id_familia;

    #[Validate('required')]
    public $id_categoria;

    #[Validate('required')]
    public $id_proveedor;

    #[Validate('required|min:5|max:100')]
    public $nombre;

    #[Validate('required')]
    public $id_unidad;

    #[Validate('required')]
    public $punto_venta;

    #[Validate('required|numeric|min:1')]
    public $costo_unitario;

    #[Validate('required|numeric|min:1')]
    public $st_min;

    #[Validate('required|numeric|min:1')]
    public $st_max;

    #[Computed()]
    public function familias()
    {
        return Familia::all();
    }
    #[Computed()]
    public function categorias()
    {
        return Categoria::all();
    }
    #[Computed()]
    public function proveedores()
    {
        return Proveedor::all();
    }
    #[Computed()]
    public function unidades()
    {
        return Unidad::all();
    }

    //REGISTRAMOS UN ARTICULO EN LA TABLA
    public function register()
    {
        $validated = $this->validate();
        //dd($validated);
        InventarioPrincipal::create($validated);
        $this->reset(
            'id_familia',
            'id_categoria',
            'nombre',
            'id_unidad',
            'id_proveedor',
            'punto_venta',
            'st_min',
            'st_max',
            'costo_unitario'
        );
        $this->dispatch('close-modal');
        session()->flash('success', "Articulo registrado con exito"); //MENSAJE DE ALERTA CUANDO SE REGISTRE CORRECTAMENTE
        $this->dispatch('open-action-message');
        $this->resetPage();
    }

    //EDITAMOS ARTICULO DE LA BASE DE DATOS
    public function edit(InventarioPrincipal $articulo)
    {
        $this->dispatch('open-modal',  name: 'modificarAr'); //SE ABRE EL MODAL PARA PODER EDITAR
        $this->inventarioPrincipal = $articulo;
        //SE GUARDAN LOS DATOS EN LAS VARIABLES PARA PODER EDITAR
        $this->codigo = $articulo->codigo;
        $this->id_familia = $articulo->id_familia;
        $this->id_categoria = $articulo->id_categoria;
        $this->nombre = $articulo->nombre;
        $this->id_unidad = $articulo->id_unidad;
        $this->id_proveedor = $articulo->id_proveedor;
        $this->punto_venta = $articulo->punto_venta;
        $this->st_min = $articulo->st_min;
        $this->st_max = $articulo->st_max;
        $this->costo_unitario = $articulo->costo_unitario;
    }

    //ACTUALIZAMOS EL ARTICULO EN LA BASE DE DATOS
    public function updateAr()
    {
        $validated = $this->validate();
        $this->inventarioPrincipal->update($validated);
        $this->reset(
            'id_familia',
            'id_categoria',
            'nombre',
            'id_unidad',
            'id_proveedor',
            'punto_venta',
            'st_min',
            'st_max',
            'costo_unitario'
        );
        $this->dispatch('close-modal');
        session()->flash('success', "Cambios registrados con exito"); //MENSAJE DE ALERTA CUANDO SE EDITE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    public function cancelarEdit()
    {
        $this->reset(
            'id_familia',
            'id_categoria',
            'nombre',
            'id_unidad',
            'id_proveedor',
            'punto_venta',
            'st_min',
            'st_max',
            'costo_unitario'
        );
        $this->dispatch('close-modal');
    }

    //ELIMINAMOS ARTICULO DE LA BASE DE DATOS
    public function delete(InventarioPrincipal $codigo)
    {
        $this->dispatch('open-modal',  name: 'eliminarAr'); //ABRIMOS EL MODAL PARA PODER ELIMINAR
        $this->inventarioPrincipal = $codigo;
    }

    //CONFIRMAMOS LA ELIMINACION DEL ARTICULO
    public function confirmDelete()
    {
        $this->inventarioPrincipal->estado = 0;
        $this->inventarioPrincipal->save();
        $this->dispatch('close-modal');
        session()->flash('fail', "Artículo inactivado correctamente"); //MENSAJE DE ALERTA CUANDO SE ELIMINE CORRECTAMENTE
        $this->dispatch('open-action-message');
        //$this->dispatch('reload')->self();
    }

    //REINGRESAMOS EL ARTICULO 
    public function reingresar(InventarioPrincipal $codigo)
    {
        $this->dispatch('open-modal',  name: 'reingresarAr'); //ABRIMOS EL MODAL PARA PODER REINGRESAR
        $this->inventarioPrincipal = $codigo;
    }

    //CONFIRMAMOS EL REINGRESO DEL ARTICULO
    public function confirmReingreso()
    {
        $this->inventarioPrincipal->estado = 1;
        $this->inventarioPrincipal->save();
        $this->dispatch('close-modal');
        session()->flash('success', "Artículo reingresado con exito"); //MENSAJE DE ALERTA CUANDO SE REINGRESE CORRECTAMENTE
        $this->dispatch('open-action-message');
        //$this->dispatch('reload')->self();
    }

    //UN HOOK PARA RESETEAR LA PAGINACION
    public function updated($search)
    {
        $this->resetPage();
    }

    public function render()
    {
        $result = DB::table('ipa_inventario_principal')
            ->join('familias', 'ipa_inventario_principal.id_familia', '=', 'familias.id')
            ->join('categorias', 'ipa_inventario_principal.id_categoria', '=', 'categorias.id')
            ->join('unidades', 'ipa_inventario_principal.id_unidad', '=', 'unidades.id')
            ->join('proveedores', 'ipa_inventario_principal.id_proveedor', '=', 'proveedores.id')
            ->where('nombre', 'like', '%' . $this->search . '%')->orWhere('codigo', '=', $this->search)
            ->select([
                //COLUMNAS DE LA TABLA PRINCIPAL
                'ipa_inventario_principal.codigo',
                'ipa_inventario_principal.nombre',
                'ipa_inventario_principal.punto_venta',
                'ipa_inventario_principal.costo_unitario',
                'ipa_inventario_principal.stock',
                'ipa_inventario_principal.st_min',
                'ipa_inventario_principal.st_max',
                'ipa_inventario_principal.estado',
                //SOLO INCLUYE LAS COLUMNAS DESEADAS DE LAS TABLAS UNIDAS
                'familias.familia',
                'categorias.categoria',
                'unidades.unidad',
                'proveedores.proveedor',
            ])
            ->orderByRaw('ipa_inventario_principal.codigo')
            ->paginate(5);

        return view('livewire.almacen.articulos', [
            'listaArticulos' => $result
        ]);
    }
}
