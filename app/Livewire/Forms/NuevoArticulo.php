<?php

namespace App\Livewire\Forms;

use App\Models\InventarioPrincipal;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NuevoArticulo extends Form
{
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

    #[Validate('required')]
    public $costo_unitario;

    #[Validate('required')]
    public $st_min;

    public function save()
    {
        //Validamos
        $validated = $this->validate();

        //Inicia la transacción
        DB::transaction(function () use ($validated) {
            //Creamos el registro
            InventarioPrincipal::create($validated);

            //Limpiamos la pagina
            $this->reset();
        });
        return true;
    }
}
