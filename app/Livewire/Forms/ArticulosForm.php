<?php

namespace App\Livewire\Forms;

use App\Models\CatalogoVistaVerde;
use App\Models\Proveedor;
use App\Models\UnidadCatalogo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ArticulosForm extends Form
{
    public $nombre;
    public $descripcion;
    public $familia;
    public $categoria;
    public $unidad;
    public $proveedor;
    public $costo_unitario;
    public $costo_empleado;
    public $estado;
    public $tipo;

    //INFORMACION DE LOS PRECIOS POR UNIDAD
    public $id_unidad;
    public $costo;

    public $unidades = [];
    public $articulos_BD;
    public ?CatalogoVistaVerde $articulo; 

    protected $articulo_rules = [
        'nombre' => 'required|min:3|max:80',
        'familia' => 'max:50',
        'categoria' => 'max:50',
        'proveedor' => 'max:50',
        'costo_unitario' => 'max:50',
        'costo_empleado' => 'max:50',
        'tipo' => 'max:50'
    ];

    //Valores para poder editar el articulo
    public function setArticulo(CatalogoVistaVerde $articulo)
    {
        $this->articulo = $articulo;

        $this->nombre = $articulo->nombre;
        $this->familia = $articulo->id_familia;
        $this->categoria = $articulo->id_categoria;
        $this->proveedor = $articulo->id_proveedor;
        $this->costo_unitario = $articulo->costo_unitario;
        $this->costo_empleado = $articulo->costo_empleado;
        $this->tipo = $articulo->tipo;
        $this->estado = $articulo->estado;
    }

    //CREACION DE UN PRECIO POR UNIDAD EN EL ARTICULO
    public function crearUnidad()
    {
        //Validamos entradas
        $validated = $this->validate([
            'id_unidad' => "required",
            'costo' => 'required|max:20',
        ]);
        //Creamos una marca de tiempo
        $validated['temp'] = time();

        array_push($this->unidades, $validated);
        //Limpiamos los campos
        $this->reset('id_unidad', 'costo');
    }

    //ELIMINAR UN PRECIO DE UNIDAD EN EL MODO DE CREACION
    public function quitarUnidad($temp)
    {
        $this->unidades = array_filter($this->unidades, function ($unidad) use ($temp) {
            return $unidad['temp'] != $temp;
        });
    }

    //SE CREA UN NUEVO ARTICULO AL CATALOGO
    public function articuloNuevo()
    {
        $validated = $this->validate($this->articulo_rules);

        //Creamos el articulo
        DB::transaction(function() use ($validated) {
            $articulo = CatalogoVistaVerde::create([
                'nombre' => $validated['nombre'],
                'id_familia' => $validated['familia'],
                'id_categoria' => $validated['categoria'],
                'id_proveedor' => $validated['proveedor'],
                'costo_unitario' => $validated['costo_unitario'],
                'costo_empleado' => $validated['costo_empleado'],
                'tipo' => $validated['tipo']
            ]);
            //Creamos los precios de unidad dependiendo de la cantidad ingresada
            foreach ($this->unidades as $unidad) {
                UnidadCatalogo::create([
                    'codigo_catalogo' => $articulo->codigo,
                    'id_unidad' => $unidad['id_unidad'],
                    'costo' => $unidad['costo'],
                ]);
            }
            //Limpiamos las propiedades
            $this->reset();
        });
    }
}