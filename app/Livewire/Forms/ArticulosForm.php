<?php

namespace App\Livewire\Forms;

use App\Models\CatalogoVistaVerde;
use App\Models\Proveedor;
use App\Models\UnidadCatalogo;
use Illuminate\Auth\Events\Validated;
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
    public $clave_dpto;
    public $tipo;

    //INFORMACION DE LOS PRECIOS POR UNIDAD
    public $id_unidad;
    public $costo_unidad;

    public $unidades = [];
    public $articulos_BD;
    public ?CatalogoVistaVerde $articulo;

    protected $articulo_rules = [
        'nombre' => 'required|min:3|max:80',
        'familia' => 'max:50',
        'categoria' => 'max:50',
        'proveedor' => 'max:50',
        'costo_unitario' => 'min:0|max:50',
        'costo_empleado' => 'max:50',
        'clave_dpto' => 'required',
        'tipo' => 'max:50',
        'estado' => 'max:10'
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
        $this->clave_dpto = $articulo->clave_dpto;
        $this->tipo = $articulo->tipo;
        $this->estado = $articulo->estado;
    }

    public function setUnidades(CatalogoVistaVerde $articulo)
    {
        $this->unidades = UnidadCatalogo::with('unidad')->where('codigo_catalogo', $articulo->codigo)->get()->toArray();
    }

    //CREACION DE UN PRECIO POR UNIDAD EN EL ARTICULO
    public function crearUnidad()
    {
        //Validamos entradas
        $validated = $this->validate([
            'id_unidad' => "required",
            'costo_unidad' => 'required|numeric|min:0.01',
        ]);
        //Creamos una marca de tiempo
        $validated['temp'] = time();

        array_push($this->unidades, $validated);
        //Limpiamos los campos
        $this->reset('id_unidad', 'costo_unidad');
    }

    //ELIMINAR UN PRECIO DE UNIDAD EN EL MODO DE CREACION
    public function quitarUnidad($temp)
    {
        $this->unidades = array_filter($this->unidades, function ($unidad) use ($temp) {
            return $unidad['temp'] != $temp;
        });
    }

    public function eliminarUnidad($index_unidad)
    {
        $this->unidades[$index_unidad]['deleted'] = true;
    }

    //SE CREA UN NUEVO ARTICULO AL CATALOGO
    public function articuloNuevo()
    {
        $validated = $this->validate($this->articulo_rules);

        //Creamos el articulo
        DB::transaction(function () use ($validated) {
            $articulo = CatalogoVistaVerde::create([
                'nombre' => $validated['nombre'],
                'id_familia' => $validated['familia'],
                'id_categoria' => $validated['categoria'],
                'id_proveedor' => $validated['proveedor'],
                'costo_unitario' => $validated['costo_unitario'],
                'costo_empleado' => $validated['costo_empleado'],
                'clave_dpto' => $validated['clave_dpto'],
                'tipo' => $validated['tipo']
            ]);
            //Creamos los precios de unidad dependiendo de la cantidad ingresada
            foreach ($this->unidades as $unidad) {
                UnidadCatalogo::create([
                    'codigo_catalogo' => $articulo->codigo,
                    'id_unidad' => $unidad['id_unidad'],
                    'costo_unidad' => $unidad['costo_unidad'],
                ]);
            }
            //Limpiamos las propiedades
            $this->reset();
        });
    }

    /**
     * Actualiza unicamente la informacion general del articulo
     */
    public function guardarInfoGeneral($validated)
    {
        $this->articulo->update($validated);
        CatalogoVistaVerde::where('codigo', $this->articulo->codigo)->update([
            'id_familia' => $validated['familia'],
            'id_categoria' => $validated['categoria'],
            'id_proveedor' => $validated['proveedor'],
        ]);
    }

    /**
     * Actualiza unicamente la informacion de precio de compra por unidad en cada articulo
     */
    public function guardarInfoUnidades()
    {
        foreach ($this->unidades as $unidad) {
            if (array_key_exists('deleted', $unidad)) {
                //Eliminamos la unidad del articulo
                UnidadCatalogo::destroy($unidad['id']);
            } else {
                //Dependiendo la unidad si existe, actualizamos o creamos
                if (array_key_exists('id', $unidad)) {
                    //Actualizamos unicamente el precio de la unidad
                    $result = UnidadCatalogo::find($unidad['id']);
                    $result->costo_unidad = $unidad['costo_unidad'];
                    $result->save();
                } else {
                    //Creamos la unidad en modo edición
                    UnidadCatalogo::create([
                        'codigo_catalogo' => $this->articulo->codigo,
                        'id_unidad' => $unidad['id_unidad'],
                        'costo_unidad' => $unidad['costo_unidad']
                    ]);
                }
            }
        }
    }

    public function validar()
    {
        $this->articulo_rules['estado'] = 'required';
        $validated = $this->validate($this->articulo_rules);
        return $validated;
    }
}
