<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SociosController extends Controller
{
    //

    //Muestra el formulario para editar
    public function showEdit(Request $request, Socio $socio)
    {
        $permisos = $request->get('permisos_sistemas'); //Obtenemos los permisos (opcionales) incrutados en la peticion
        return view('recepcion.Socios.editar-socio', ['socio' => $socio, 'permisos' => $permisos]);
    }
}
