<?php

namespace App\Livewire\Almacen;

use App\Models\CatalogoVistaVerde;
use App\Models\Categoria;
use App\Models\Familia;
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
    public CatalogoVistaVerde $catalogoVV;
    public $codigo;
    public $stock_amc;

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

    #[Validate('required|numeric|min:1')]
    public $costo_unitario;

    #[Validate('required|numeric|min:1')]
    public $st_min_amc;

    #[Validate('required|numeric|min:1')]
    public $st_max_amc;

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
        CatalogoVistaVerde::create($validated);
        $this->reset(
            'id_familia',
            'id_categoria',
            'nombre',
            'id_unidad',
            'id_proveedor',
            'st_min_amc',
            'st_max_amc',
            'costo_unitario'
        );
        $this->dispatch('close-modal');
        session()->flash('success', "Articulo registrado con exito"); //MENSAJE DE ALERTA CUANDO SE REGISTRE CORRECTAMENTE
        $this->dispatch('open-action-message');
        $this->resetPage();
    }

    //EDITAMOS ARTICULO DE LA BASE DE DATOS
    public function edit(CatalogoVistaVerde $articulo)
    {
        $this->dispatch('open-modal',  name: 'modificarAr'); //SE ABRE EL MODAL PARA PODER EDITAR
        $this->catalogoVV = $articulo;
        //SE GUARDAN LOS DATOS EN LAS VARIABLES PARA PODER EDITAR
        $this->codigo = $articulo->codigo;
        $this->id_familia = $articulo->id_familia;
        $this->id_categoria = $articulo->id_categoria;
        $this->nombre = $articulo->nombre;
        $this->id_unidad = $articulo->id_unidad;
        $this->id_proveedor = $articulo->id_proveedor;
        $this->st_min_amc = $articulo->st_min_amc;
        $this->st_max_amc = $articulo->st_max_amc;
        $this->costo_unitario = $articulo->costo_unitario;
    }

    //ACTUALIZAMOS EL ARTICULO EN LA BASE DE DATOS
    public function updateAr()
    {
        $validated = $this->validate();
        $this->catalogoVV->update($validated);
        $this->reset(
            'id_familia',
            'id_categoria',
            'nombre',
            'id_unidad',
            'id_proveedor',
            'st_min_amc',
            'st_max_amc',
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
            'st_min',
            'st_max',
            'costo_unitario'
        );
        $this->dispatch('close-modal');
    }

    //ELIMINAMOS ARTICULO DE LA BASE DE DATOS
    public function delete(CatalogoVistaVerde $codigo)
    {
        $this->dispatch('open-modal',  name: 'eliminarAr'); //ABRIMOS EL MODAL PARA PODER ELIMINAR
        $this->catalogoVV = $codigo;
    }

    //CONFIRMAMOS LA ELIMINACION DEL ARTICULO
    public function confirmDelete()
    {
        $this->catalogoVV->estado = 0;
        $this->catalogoVV->save();
        $this->dispatch('close-modal');
        session()->flash('fail', "Artículo inactivado correctamente"); //MENSAJE DE ALERTA CUANDO SE ELIMINE CORRECTAMENTE
        $this->dispatch('open-action-message');
    }

    //REINGRESAMOS EL ARTICULO 
    public function reingresar(CatalogoVistaVerde $codigo)
    {
        $this->dispatch('open-modal',  name: 'reingresarAr'); //ABRIMOS EL MODAL PARA PODER REINGRESAR
        $this->catalogoVV = $codigo;
    }

    //CONFIRMAMOS EL REINGRESO DEL ARTICULO
    public function confirmReingreso()
    {
        $this->catalogoVV->estado = 1;
        $this->catalogoVV->save();
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
        $result = DB::table('catalogo_vista_verde')
            ->join('familias', 'catalogo_vista_verde.id_familia', '=', 'familias.id')
            ->join('categorias', 'catalogo_vista_verde.id_categoria', '=', 'categorias.id')
            ->join('unidades', 'catalogo_vista_verde.id_unidad', '=', 'unidades.id')
            ->join('proveedores', 'catalogo_vista_verde.id_proveedor', '=', 'proveedores.id')
            ->where('nombre', 'like', '%' . $this->search . '%')->orWhere('codigo', '=', $this->search)
            ->select([
                //COLUMNAS DE LA TABLA PRINCIPAL
                'catalogo_vista_verde.codigo',
                'catalogo_vista_verde.nombre',
                'catalogo_vista_verde.costo_unitario',
                'catalogo_vista_verde.stock_amc',
                'catalogo_vista_verde.st_min_amc',
                'catalogo_vista_verde.st_max_amc',
                'catalogo_vista_verde.estado',
                //SOLO INCLUYE LAS COLUMNAS DESEADAS DE LAS TABLAS UNIDAS
                'familias.familia',
                'categorias.categoria',
                'unidades.unidad',
                'proveedores.proveedor',
            ])
            ->orderByRaw('catalogo_vista_verde.codigo')
            ->paginate(10);
        return view('livewire.almacen.articulos', [
            'listaArticulos' => $result
        ]);
    }
}
