<?php

namespace App\Livewire\Forms;

use App\Models\Presentacion;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PresentacionForm extends Form
{
    public $clave, $descripcion, $id_grupo, $id_proveedor, $ultimo_costo, $iva;
    public $estado, $insumo_base, $rendimiento;



    /**
     * Guarda la presentacion en la BD
     */
    public function guardarPresentacion()
    {
        $validated = $this->validate([
            'descripcion' => 'required',
            'ultimo_costo' => 'required',
            'rendimiento' => 'required',
            'insumo_base' => 'required',
            'id_proveedor' => 'required',
            'estado' => 'required',
        ]);
        Presentacion::create([
            'descripcion' => $validated['descripcion'],
            'id_grupo' => 99999,
            'costo' => $validated['ultimo_costo'],
            'iva' => $this->iva,
            'clave_insumo_base' => $this->insumo_base,
            'rendimiento' => $validated['rendimiento'],
            'id_proveedor' => $validated['id_proveedor'],
            'estado' => $validated['estado'],
        ]);
        $this->reset();
    }
}
