<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LockersController extends Controller
{
    /**
     * Muestra la vista inicial del departamento del sistema
     */
    public function index(Request $request)
    {

        return view('lockers.index');
    }

    /**
     * Muestra la vista para revisar la lista de lockers desocupados/ocupados
     */
    public function consultarCasilleros(Request $request)
    {
        return view('lockers.Casilleros.casilleros');
    }


    /**
     * Prepara el panel para editar el locker seleccionado
     */
    public function editarCasillero(Request $request)
    {
        return view('lockers.Casilleros.editar', [
            'id_locker' => $request->segment(4)  //'id_locker' está en el 5to segmento de la ruta
        ]);
    }

    /**
     * Muestra la vista con todos los movimientos realizados durante el dia
     */
    public function consultarMovimientos(Request $request)
    {
        return view('lockers.Movimientos.movimientos');
    }

    /**
     * Muestra la vista para dar de alta lockers en el sistema
     */
    public function altaCasillero(Request $request)
    {
        return view('lockers.Movimientos.alta');
    }


    /**
     * Prepara la vista para dar de baja un locker
     */
    public function bajaCasillero(Request $request) {}

    /**
     * Prepara la vista para cambiar un numero de locker ya asignado
     */
    public function transferirCasillero(Request $request)
    {
        return view('lockers.Movimientos.transferir');
    }
}
