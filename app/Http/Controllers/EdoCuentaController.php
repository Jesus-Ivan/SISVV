<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EdoCuentaController extends Controller
{
    //Muestra el formulario para editar un estado de cuenta
    public function showEditEdoCuenta(Socio $socio)
    {
        return view('recepcion.Estado-cuenta.nuevo-cargo', ['socio' => $socio]);
    }
}
