<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class VentaForm extends Form
{
    public $nombre_invitado;
    public $tipoVenta = "socio";

    public $socioSeleccionado;

}
