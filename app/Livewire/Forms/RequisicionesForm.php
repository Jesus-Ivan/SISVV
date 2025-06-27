<?php

namespace App\Livewire\Forms;

use App\Models\DetallesCompra;
use App\Models\OrdenCompra;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RequisicionesForm extends Form
{
    public $tipo_orden;         //Tipo de orden a registrar
    //Tabla de presentaciones para la requisicion
    public $presentaciones = [];

    /**
     * Agrega al array 'presentaciones' la presentacion validada
     */
    public function agregarPresentacion($presentacion)
    {
        $this->presentaciones[] = [
            'clave' => $presentacion['articulo_seleccionado']['clave'],
            'descripcion' => $presentacion['articulo_seleccionado']['descripcion'],
            'id_proveedor' => $presentacion['id_proveedor'],
            'cantidad' => 1,
            'costo_unitario' => $presentacion['costo_unitario'],
            'iva' => $presentacion['iva'],
            'costo_con_impuesto' => $presentacion['costo_con_impuesto'],
            'importe' => $presentacion['costo_con_impuesto'],   //Dado de que agrega con cantidad * 1
        ];
    }

    /**
     * Elimina del array 'presentaciones' segun el indice
     */
    public function eliminarPresentacion($index)
    {
        unset($this->presentaciones[$index]);
    }

    /**
     * Multiplica la cantidad de la presentacion * el costo con impuesto, segun el indice de la tabla 'presentaciones'
     */
    public function actualizarImporte($index)
    {
        //Verificar que cantidad no sea vacio
        if (strlen($this->presentaciones[$index]['cantidad']) == 0)
            $this->presentaciones[$index]['cantidad'] = 1;
        //Calcular el importe
        $this->presentaciones[$index]['importe'] =
            $this->presentaciones[$index]['cantidad'] * $this->presentaciones[$index]['costo_con_impuesto'];
    }

    /**
     * Calcula el precio con iva de la tabla 'presentaciones'
     */
    public function actualizarCostoIva($index)
    {
        //Verificar el atributo $iva es un string vacio 
        if (strlen($this->presentaciones[$index]['iva']) == 0)
            $this->presentaciones[$index]['iva'] = '0';
        //Verificar el atributo $costo_unitario es un string vacio 
        if (strlen($this->presentaciones[$index]['costo_unitario']) == 0)
            $this->presentaciones[$index]['costo_unitario'] = '0';

        $costo_unitario = $this->presentaciones[$index]['costo_unitario']; //variables axuliares
        $iva = $this->presentaciones[$index]['iva']; //variables axuliares

        //Calcular costo con iva
        $costo_iva = $costo_unitario + ($costo_unitario * ($iva / 100));
        $this->presentaciones[$index]['costo_con_impuesto'] = round($costo_iva, 2);
    }

    /**
     * Calcula el costo unitario sin iva de la tabla 'presentaciones', segun el indice dado
     */
    public function actualizarCostoSinIva($index)
    {
        //Verificar el atributo $costo_con_impuesto es un string vacio 
        if (strlen($this->presentaciones[$index]['costo_con_impuesto']) == 0)
            $this->presentaciones[$index]['costo_con_impuesto'] = '0';

        //Variables auximilares
        $costo_con_impuesto = $this->presentaciones[$index]['costo_con_impuesto'];
        $iva = $this->presentaciones[$index]['iva'];

        //Calcular Costo sin iva
        $costo_sin_iva = ($costo_con_impuesto * 100) / (100 + $iva);
        $this->presentaciones[$index]['costo_unitario'] = round($costo_sin_iva, 2);
    }

    /**
     * Crea en la BD, los registros correspondientes a la requisicion
     */
    public function crearRequisicion()
    {
        //Multiplicar los valores de la tabla, por si hubo error en una peticion de livewire
        $this->multiplicarTabla();
        
        $validated = $this->validate([
            'tipo_orden' => 'required',
            'presentaciones' => 'required|array|min:1',
        ]);
        $subtotal = array_sum(array_column($validated['presentaciones'], 'costo_unitario'));
        $iva = array_sum(array_column($validated['presentaciones'], 'costo_con_impuesto'));
        //creamos la orden
        $result_orden = OrdenCompra::create([
            'fecha' => now(),
            'tipo_orden' => $validated['tipo_orden'],
            'id_user' => auth()->user()->id,
            'cantidad' => count($validated['presentaciones']),
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $subtotal + $iva,
        ]);

        foreach ($validated['presentaciones'] as $key => $articulo) {
            //creamos los detalles de la orden de compra
            DetallesCompra::create([
                'folio_orden' => $result_orden->folio,
                'codigo_producto' => $articulo['clave'],
                'nombre' => $articulo['descripcion'],
                'id_proveedor' => $articulo['id_proveedor'],
                'cantidad' => $articulo['cantidad'],
                'costo_unitario' => $articulo['costo_unitario'],
                'iva' => $articulo['iva'],
                'costo_con_impuesto' => $articulo['costo_con_impuesto'],
                'importe' => $articulo['importe'],
            ]);
        }
        $this->reset();
    }

    public function multiplicarTabla()
    {
        for ($i = 0; $i < count($this->presentaciones); $i++) {
            $this->actualizarCostoSinIva($i);
            $this->actualizarImporte($i);
        }
    }
}
