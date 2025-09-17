<?php

namespace App\Livewire\Forms;

use App\Models\Insumo;
use App\Models\Presentacion;
use App\Models\Unidad;
use Exception;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PresentacionForm extends Form
{
    public $clave, $descripcion, $id_grupo, $id_proveedor, $ultimo_costo, $iva = 16, $costo_iva;
    public $estado = 1, $ultima_compra;
    public $insumo_base, $unidad_insumo, $rendimiento, $redondeo = true;
    public ?Presentacion $original = null;
    public $c_rendimiento = null, $c_rendimiento_imp = null;

    /**
     * Guarda los valores iniciales, para establecerlos dentro del formulario como array
     */
    public function setValues(Presentacion $presentacion)
    {
        //Guardar propiedades editables
        $this->clave = $presentacion->clave;        //Para efectos visuales en la UI. Pero no se modifica.
        $this->descripcion = $presentacion->descripcion;
        $this->id_grupo = $presentacion->id_grupo;
        $this->id_proveedor = $presentacion->id_proveedor;
        $this->ultimo_costo = $presentacion->costo;
        $this->iva = $presentacion->iva;
        $this->costo_iva = $presentacion->costo_con_impuesto;
        $this->estado = $presentacion->estado;
        $this->ultima_compra = $presentacion->ultima_compra;
        $this->rendimiento = $presentacion->rendimiento;
        $this->redondeo = boolval($presentacion->redondeo);
        $this->setInsumoBase($presentacion->clave_insumo_base);
        //Guardar el modelo original
        $this->original = $presentacion;
        $this->c_rendimiento = $presentacion->costo_rend;
        $this->c_rendimiento_imp = $presentacion->costo_rend_impuesto;
    }

    /**
     * Guarda el insumo base original, la unidad correspondiente y el grupo.
     */
    public function setInsumoBase($clave)
    {
        //Buscar insumo base
        $this->insumo_base = Insumo::with('grupo')->find($clave);
        //Si hay un insumo base
        if ($this->insumo_base) {
            //Buscar la unidad
            $this->unidad_insumo = Unidad::find($this->insumo_base->id_unidad);
            //Cambiar el grupo de la presentacion, por el grupo del insumo base.
            $this->id_grupo = $this->insumo_base->grupo->id;
        }
    }

    /**
     * Reinicia el Insumo base seleccionado, de la presentacion.
     */
    public function limpiarInsumoBase()
    {
        $this->reset('insumo_base', 'unidad_insumo');
    }

    public function calcularPrecioIva()
    {
        //Verificar el atributo $iva es un string vacio 
        if (strlen($this->iva) == 0)
            $this->iva = '0';
        //Verificar el atributo $costo es un string vacio 
        if (strlen($this->ultimo_costo) == 0)
            $this->ultimo_costo = '0';
        //Calcular costo con iva
        $costo_iva = $this->ultimo_costo + ($this->ultimo_costo * ($this->iva / 100));
        $this->costo_iva = round($costo_iva, 2);
    }
    public function calcularPrecioSinIva()
    {
        //Verificar el atributo $costo_iva es un string vacio 
        if (strlen($this->costo_iva) == 0)
            $this->costo_iva = '0';
        //Calcular Costo sin iva
        $costo_sin_iva = ($this->costo_iva * 100) / (100 + $this->iva);
        $this->ultimo_costo = round($costo_sin_iva, 2);
    }

    /**
     * Guarda la nueva presentacion en la BD
     */
    public function guardarPresentacion()
    {
        //Validar los campos
        $validated = $this->validate([
            'descripcion' => 'required',
            'id_grupo' => 'required',
            'costo_iva' => 'required',
            'rendimiento' => 'required',
            'redondeo' => 'required',
            'insumo_base' => 'required',
            'id_proveedor' => 'required',
            'estado' => 'required',
        ]);
        //Agregar el  ultimo_costo y el iva al array
        $validated['ultimo_costo'] = $this->ultimo_costo;
        $validated['iva'] = $this->iva;
        $validated['ultima_compra'] = $this->ultima_compra; //Agregar la fecha de ultima compra
        $validated['c_rendimiento'] = $this->c_rendimiento;
        $validated['c_rendimiento_imp'] = $this->c_rendimiento_imp;
        //Crear el registro en la BD
        Presentacion::create([
            'descripcion' => $validated['descripcion'],
            'id_grupo' => $validated['id_grupo'],
            'costo' => $validated['ultimo_costo'],
            'iva' => $validated['iva'],
            'costo_con_impuesto' => $validated['costo_iva'],
            'clave_insumo_base' => $validated['insumo_base']['clave'],
            'rendimiento' => $validated['rendimiento'],
            'redondeo' => $validated['redondeo'],
            'id_proveedor' => $validated['id_proveedor'],
            'costo_rend' => $validated['c_rendimiento'],
            'costo_rend_impuesto' => $validated['c_rendimiento_imp'],
            'estado' => $validated['estado'],
            'ultima_compra' => $validated['ultima_compra'],
        ]);
        $this->reset();
    }

    /**
     * Guarda los cambios hechos en la presentacion
     */
    public function guardarCambios()
    {
        $validated = $this->validate([
            'descripcion' => 'required',
            'id_grupo' => 'required',
            'costo_iva' => 'required',
            'rendimiento' => 'required',
            'redondeo' => 'required',
            'insumo_base' => 'required',
            'id_proveedor' => 'required',
            'estado' => 'required',
        ]);
        //Agregar el iva al array
        $validated['ultimo_costo'] = $this->ultimo_costo;
        $validated['iva'] = $this->iva;
        $validated['c_rendimiento'] = $this->c_rendimiento;
        $validated['c_rendimiento_imp'] = $this->c_rendimiento_imp;

        //Cambiar los atributos
        $this->original->descripcion = $validated['descripcion'];
        $this->original->id_grupo = $validated['id_grupo'];
        $this->original->costo = $validated['ultimo_costo'];
        $this->original->iva = $validated['iva'];
        $this->original->costo_con_impuesto = $validated['costo_iva'];
        $this->original->clave_insumo_base = $validated['insumo_base']['clave'];
        $this->original->rendimiento = $validated['rendimiento'];
        $this->original->redondeo = $validated['redondeo'];
        $this->original->costo_rend = $validated['c_rendimiento'];
        $this->original->costo_rend_impuesto = $validated['c_rendimiento_imp'];
        $this->original->id_proveedor = $validated['id_proveedor'];
        $this->original->estado = $validated['estado'];
        $this->original->save();    //Persistir los cambios en la BD
    }

    /**
     * Actuliza el costo rendimiento de la presentacion (sin impuesto)
     */
    public function costoRendimiento()
    {
        if ($this->rendimiento && $this->ultimo_costo) {
            $this->c_rendimiento = round($this->ultimo_costo / $this->rendimiento, 3);
        }
    }

    /**
     * Actuliza el costo rendimiento de la presentacion (CON impuesto)
     */
    public function costoRendimientoImp()
    {
        if ($this->costo_iva && $this->rendimiento) {
            $this->c_rendimiento_imp = round($this->costo_iva / $this->rendimiento, 3);
        }
    }
}
