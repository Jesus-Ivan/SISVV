<?php

namespace App\Exports;

use App\Models\Socio;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;

class SociosExport implements FromArray
{

    /* public function collection()
    {
        $result = DB::table('socios_val_cuot')->get();
        return $result;
        return Socio::all();
    } */

    public function array(): array
    {
        //Obtenemos todos los socios validos
        $socios_membresias = DB::table('socios_validos')->get();
        //array auxiliar
        $data = [];
        foreach ($socios_membresias as $key => $socio) {
            $data[] = [
                'id' => $socio->id,
                'nombre' => $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m,
                'CUOTA MENSUAL' => '',
                'LOCKER' => '',
                'CARRITO' => '',
                'TIPO MEMBRESIA' => $socio->clave_membresia,
            ];
        }

        return $data;
    }
}
