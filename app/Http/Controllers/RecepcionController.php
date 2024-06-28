<?php

namespace App\Http\Controllers;

use App\Models\PuntoVenta;
use Illuminate\Http\Request;

class RecepcionController extends Controller
{
    public function ventasIndex()
    {
        //Obtenemos el punto de venta correspondiente a recepcion
        $punto_venta = $this->getPunto();
        //Devolvemos la vista de blade al usuario, anexando la informacion obtenida
        return view('recepcion.Ventas.ventas', ['codigopv' => $punto_venta]);
    }

    public function ventasNueva()
    {
        //Obtenemos el punto de venta correspondiente a recepcion
        $punto_venta = $this->getPunto();
        //Devolvemos la vista de blade al usuario, anexando la informacion obtenida
        return view('recepcion.Ventas.nueva-venta', ['codigopv' => $punto_venta]);
    }

    private function getPunto()
    {
        return PuntoVenta::where('nombre', 'LIKE', '%RECEP%')
            ->first();
    }
}
