<?php

namespace App\Livewire\Forms;

use App\Models\GruposModificadores;
use App\Models\Insumo;
use App\Models\Modificador;
use App\Models\ModifProducto;
use App\Models\Producto;
use App\Models\Receta;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Form;

class ProductoForm extends Form
{
    //Atributos del producto GENERAL
    public $descripcion, $precio, $iva = 0, $costo_con_impuesto;
    public $id_grupo, $id_subgrupo, $estado = 1;
    //Atributos de producto RECETA
    public $receta_table = [];
    //Atributos de producto compuesto
    public $grupos_modif = [], $modif = [];
    //Producto original
    public ?Producto $original = null;

    /**
     * Establece las propiedades para editar. y Guarda temporalmente el modelo original
     */
    public function setValues(Producto $producto)
    {
        //Guardar original como propiedad
        $this->original = $producto;
        //Establecer valores (generales)
        $this->descripcion = $producto->descripcion;
        $this->precio = $producto->precio;
        $this->iva = $producto->iva;
        $this->costo_con_impuesto = $producto->precio_con_impuestos;
        $this->id_grupo = $producto->id_grupo;
        $this->id_subgrupo = $producto->id_subgrupo;
        $this->estado = $producto->estado;
        //Establecer valores (receta)
        $this->setReceta($producto);
        //Establecer valores (compuesto)
        $this->setCompuesto($producto);
    }

    public function setReceta(Producto $producto)
    {
        //Buscar los insumos de la receta
        $result = Receta::with('ingrediente')->where('clave_producto', $producto->clave)
            ->get();
        //Agregarlos al array de la tabla editable
        foreach ($result as $i => $insumo) {
            $this->receta_table[] = [
                'id' => $insumo->id,        //ID, del registo en la BD.
                'clave' => $insumo->clave_insumo,
                'descripcion' => $insumo->ingrediente->descripcion,
                'cantidad' => floatval($insumo->cantidad),
                'costo_con_impuesto' => floatval($insumo->ingrediente->costo_con_impuesto),
                'cantidad_con_merma' => floatval($insumo->cantidad_c_merma),
                'unidad' => ['descripcion' => $insumo->ingrediente->unidad->descripcion],
                'total' => $insumo->total,
            ];
        }
    }

    public function setCompuesto(Producto $producto)
    {
        //Buscar en la tabla 'grupo_modificador_producto'
        $grupos_modif = ModifProducto::with('grupoModif')
            ->where('clave_producto', $producto->clave)
            ->get();
        //Buscar en la tabla 'modificadores'
        $modif = Modificador::with('productoModif')
            ->where('clave_producto', $producto->clave)
            ->get(); //TODO: Verificar si es necesario buscar en la tabla 'modificadores'

        //agregar los grupos a la tabla editable
        foreach ($grupos_modif as $grupo) {
            $this->grupos_modif[] = [
                'id' => $grupo->id,     //ID, del registo en la BD.
                'id_grupo' => $grupo->id_grupo,
                'descripcion' => $grupo->grupoModif->descripcion,
                'incluidos' => $grupo->modif_incluidos,
                'maximos' => $grupo->modif_maximos,
                'forzar' => boolval($grupo->forzar_captura)
            ];
        }

        //Agregar los modificadores a la tabla 'modif'
        foreach ($modif as $modificador) {
            $this->modif[] = [
                'id' => $modificador->id, //ID, del registo en la BD.
                'clave' => $modificador->clave_modificador,
                'descripcion' => $modificador->productoModif->descripcion,
                'precio' => $modificador->precio,
                'id_grup_modif' => $modificador->id_grupo
            ];
        }
    }

    /**
     * Convierte el modelo en array y agrega las propiedades necesarias (cantidad y cantidad con merma)
     */
    public function agregarInsumoReceta(Insumo $insumo)
    {
        //Convertimos a array el insumo
        $insumoArray = $insumo->toArray();
        //Agregamos propiedades
        $insumoArray['cantidad'] = 0;
        $insumoArray['cantidad_con_merma'] = 0;
        $insumoArray['total'] = 0;
        //Agregarmos a la tabla
        $this->receta_table[] = $insumoArray;
    }

    /**
     * Remueve el item del array de la receta
     */
    public function eliminarInsumoReceta($index)
    {
        unset($this->receta_table[$index]);
    }

    /**
     * Convierte el modelo en array y agrega las propiedades necesarias (maximos, incluidos, forzar)
     */
    public function agregarGrupoModificador(GruposModificadores $grupo)
    {
        //Agregamos a la tabla el grupo, junto a las propiedades extra
        $this->grupos_modif[] = [
            'id_grupo' => $grupo->id,
            'descripcion' => $grupo->descripcion,
            'incluidos' => null,
            'maximos' => null,
            'forzar' => false
        ];
    }

    /**
     * Remueve el item del array 'grupos_modif'
     */
    public function eliminarGrupo($index)
    {
        //Obtenemos el item a eliminar
        $item = $this->grupos_modif[$index];
        //Recorremos la tabla 'modif'
        for ($i = 0; $i < count($this->modif); $i++) {
            //Si 'id_grup_modif' del modificador es el mismo que 'id_grupo' del grupo a eliminar
            if ($this->modif[$i]['id_grup_modif'] == $item['id_grupo']) {
                //Asignar nuevo valor a 'null'
                $this->modif[$i]['id_grup_modif'] = null;
            }
        }
        unset($this->grupos_modif[$index]);
    }

    /**
     * Agrega el producto al array 'modif', y añade sus atributos restantes
     */
    public function agregarProducto(Producto $producto)
    {
        //Convertir en array el modelo
        $productoArray = $producto->toArray();
        //Agregamos propiedades
        $productoArray['id_grup_modif'] = null;
        //Agregar al array
        $this->modif[] = $productoArray;
    }

    /**
     * Remueve el item del array 'modif'
     */
    public function eliminarProducto($index)
    {
        unset($this->modif[$index]);
    }

    /**
     * Crea el producto en la tabla 'productos' y valida las propiedades
     */
    public function crearProducto(): Producto
    {
        //Validar las propiedades;
        $validated = $this->validarGenerales();
        //Crear el registro en la bd
        $result = Producto::create([
            'descripcion' => $validated['descripcion'],
            'precio' => $validated['precio'],
            'iva' => $validated['iva'],
            'precio_con_impuestos' => $validated['costo_con_impuesto'],
            'id_grupo' => $validated['id_grupo'],
            'id_subgrupo' => $this->id_subgrupo,
            'estado' => $this->estado
        ]);

        return $result;
    }

    /**
     * Crea las nuevas propiedades o actualiza los cambios.
     */
    public function actualizarProducto()
    {
        $validated = $this->validarGenerales();
        $this->validarReceta();
        /**
         * Validar propiedades compuestas
         */
        $this->validarGrupoModif(); //validar los campos de la tabla 'grupos_modif'
        $this->validarModif();      //Validamos los campos de la tabla 'modif'
        $this->verificarGrupos();   //Verificar relacion entre tablas '$grupos_modif y $modif'

        $this->actualizarGenerales($validated);
    }

    /**
     * Actualiza las propiedades generales el producto
     */
    public function actualizarGenerales($validated) {

    }

    /**
     * Crear los registros en la tabla 'recetas'
     */
    public function crearReceta(Producto $producto)
    {
        //Validar las propiedades
        $this->validarReceta();
        //Iterar el array actual
        foreach ($this->receta_table as $key => $insumo) {
            //Crear el registro en la bd
            $result = Receta::create([
                'clave_producto' => $producto->clave,
                'clave_insumo' => $insumo['clave'],
                'cantidad' => $insumo['cantidad'],
                'cantidad_c_merma' => $insumo['cantidad_con_merma'],
                'total' => $insumo['total'],
            ]);
        }
    }

    /**
     * Crea las propiedades compuestas del producto dado
     */
    public function crearCompuesto(Producto $producto)
    {
        //validar los campos de la tabla 'grupos_modif'
        $this->validarGrupoModif();

        //Validamos los campos de la tabla 'modif'
        $this->validarModif();

        //Verificar relacion entre tablas '$grupos_modif y $modif'
        $this->verificarGrupos();

        foreach ($this->grupos_modif as $key => $item) {
            //Crear el propiedades de la tabla  'grupo_modificador_producto'
            ModifProducto::create([
                'id_grupo' => $item['id_grupo'],
                'clave_producto' => $producto->clave,
                'modif_incluidos' => $item['incluidos'],
                'modif_maximos' => $item['maximos'],
                'forzar_captura' => $item['forzar'],
            ]);
        }
        foreach ($this->modif as $key => $item) {
            //Crear los modificadores (en la tabla 'modificadores')
            Modificador::create([
                'id_grupo' => $item['id_grup_modif'],
                'clave_producto' => $producto->clave,
                'clave_modificador' => $item['clave'],
                'precio' => $item['precio'],
            ]);
        }
    }

    /**
     * Valida para cada item de la tabla '$grupos_modif' exista al menos 1 elemento correspondiente en la tabla '$modif'
     */
    public function verificarGrupos()
    {
        //Si la tabla de grupos de modificadores tiene algun elemento 
        if (count($this->grupos_modif) > 0) {
            //Revisar si cada grupo, tiene un modificador asignado.
            foreach ($this->grupos_modif as $i => $grupo) {
                //Filtrar los items de la tabla 'modificadores', que tengan asignado dicho grupo
                $result = array_filter($this->modif, function ($item_modif) use ($grupo) {
                    return $item_modif['id_grup_modif'] == $grupo['id_grupo'];
                });
                //Si el grupo de modificador, no tiene item asignado en la tabla 'modificadores'
                if (count($result) == 0)
                    //Lanzar excepcion
                    throw new Exception('Faltan modificadores para: ' . $grupo['descripcion'] . '. Revisar propiedades Compuestas');
            }
        }
    }

    /**
     * Multiplica toda la tabla de insumos (receta).
     *  total = cantidad * costo_con_impuesto
     */
    public function recalcularSubtotales()
    {
        //Funcion para multiplicar cada item del array
        $func = function (array $value): array {
            //Si 'cantidad' es un string vacio 
            if (strlen($value['cantidad']) == 0)
                $value['cantidad'] = '1';       //asignar un nuevo valor
            //Obtener el valor absoluto
            $value['cantidad'] = abs($value['cantidad']);
            //Calcular el total
            $value['total'] = round($value['cantidad'] * $value['costo_con_impuesto'], 2);
            return $value;
        };
        //Mapeo de la tabla
        $updatedTable = array_map($func, $this->receta_table);
        //Actualizar la tabla
        $this->receta_table = $updatedTable;
    }

    /**
     * Reestablece las propiedades a su valor original
     */
    public function limpiar()
    {
        $this->reset();
    }

    /**
     * Valida las propiedades generales del producto y retorna el array validado.
     */
    public function validarGenerales()
    {
        return $this->validate([
            'descripcion' => 'required',
            'precio' => 'required',
            'iva' => 'required',
            'costo_con_impuesto' => 'required',
            'id_grupo' => 'required',
        ]);
    }

    /**
     * Valida que la cantidad de la receta sea positiva
     */
    public function validarReceta()
    {
        foreach ($this->receta_table as $key => $insumo) {
            //Si cantidad es negativa
            if ($insumo['cantidad'] < 0) {
                //Lanzar excepcion
                throw new Exception('Cantidad debe ser positivo. Revisar receta: ' . $insumo['descripcion']);
            }
        }
    }

    /**
     * Contiene las reglas para validar las propiedades de los grupos de modificadores.
     * Y valida cada propiedad de la tabla 'grupos_modif'
     */
    public function validarGrupoModif()
    {
        foreach ($this->grupos_modif as $index => $value) {
            $this->validate([
                'grupos_modif.' . $index . '.incluidos' => 'required|numeric|min:0',
                'grupos_modif.' . $index . '.maximos'  => 'required|numeric|min:0'
            ], [
                'grupos_modif.*.incluidos.required' => 'Obligatorio',
                'grupos_modif.*.incluidos.min' => 'Mínimo: 0',
                'grupos_modif.*.maximos.required' => 'Obligatorio',
                'grupos_modif.*.maximos.min' => 'Mínimo: 0',
            ]);
        }
    }

    /**
     * Contiene las reglas para validar las propiedades de los modificadores.
     * Y valida cada propiedad de la tabla 'modif'
     */
    public function validarModif()
    {
        foreach ($this->modif as $index => $value) {
            $this->validate([
                'modif.' . $index . '.id_grup_modif' => 'required',
                'modif.' . $index . '.precio'  => 'required|numeric|min:0'
            ], [
                'modif.*.id_grup_modif.required' => 'Obligatorio',
                'modif.*.precio.required' => 'Obligatorio',
                'modif.*.precio.min' => 'Mínimo: 0',
            ]);
        }
    }

    public function calcularPrecioIva()
    {
        //Verificar el atributo $iva es un string vacio 
        if (strlen($this->iva) == 0)
            $this->iva = '0';
        //Verificar el atributo $precio es un string vacio 
        if (strlen($this->precio) == 0)
            $this->precio = '0';
        //Calcular costo con iva
        $costo_con_impuesto = $this->precio + ($this->precio * ($this->iva / 100));
        $this->costo_con_impuesto = round($costo_con_impuesto, 2);
    }
    public function calcularPrecioSinIva()
    {
        //Verificar el atributo $costo_con_impuesto es un string vacio 
        if (strlen($this->costo_con_impuesto) == 0)
            $this->costo_con_impuesto = '0';
        //Calcular Costo sin iva
        $costo_sin_iva = ($this->costo_con_impuesto * 100) / (100 + $this->iva);
        $this->precio = round($costo_sin_iva, 2);
    }
}
