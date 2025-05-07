<?php

namespace App\Http\Controllers;

use App\Models\PuntoVenta;
use App\Models\User;
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

    public function reportesIndex()
    {
        //Creamos una instacia de carbon con la fecha actual
        $hoy = now();
        //Duplicamos la fecha, para utilizarla como fecha limite predeterminada
        $limite = $hoy->copy();

        //Si el dia actual es mayor al dia 10 (dia de creacion de los recargos).
        if ($hoy->day > 10) {
            //Si la fecha actual, es un Martes 11.
            if ($hoy->day == 11  && $hoy->dayOfWeekIso == 2) {
                //Establecer fecha limite: Ultimo dia del mes anterior (Loa cargos del mes actual no han vencido)
                $limite->subMonth()->setDay($limite->daysInMonth);
            } else {
                //Establecer fecha limite: Ultimo dia del mes actual (cargos vencidos)
                $limite->setDay($limite->daysInMonth);
            }
        } else {
            //Establecer fecha limite: Ultimo dia del mes anterior (Loa cargos del mes actual no han vencido)
            $limite->subMonth()->setDay($limite->daysInMonth);
        }
        return view('recepcion.Reportes.principal', [
            'users' => User::all(),
            'limite' => $limite->toDateString(),
        ]);
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
