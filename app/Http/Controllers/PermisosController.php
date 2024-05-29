<?php

namespace App\Http\Controllers;

use App\Models\UserPermisos;
use Illuminate\Http\Request;

class PermisosController extends Controller
{
    public function index()
    {
        $result = UserPermisos::where('id_user', auth()->user()->id)
            ->where('clave_permiso', 'RECEP')
            ->take(1)->get();
        if (count($result) > 0) {
            return view('recepcion.index');
        }else{
            return redirect()->route('home');
        }
    }
}
