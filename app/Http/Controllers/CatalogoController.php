<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\Controller;
use App\Models\CatalogoVistaVerde;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    //
    public function editArticulo(CatalogoVistaVerde $articulo)
    {
        return view('almacen.Articulos.editar-articulo', ['articulo' => $articulo]);
    }
}
