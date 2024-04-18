<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SociosController extends Controller
{
    //

    //Muestra el formulario para editar
    public function showEdit(Socio $socio)
    {
        return view('recepcion.Socios.editar-socio',['socio'=> $socio]);
    }
}
