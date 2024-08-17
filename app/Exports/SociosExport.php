<?php

namespace App\Exports;

use App\Models\Cuota;
use App\Models\SocioCuota;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;

class SociosExport implements FromArray
{

    public function __construct()
    {
    }

    public function array(): array
    {
        //Array auxiliar de los socios
        $socios = DB::table('socios')
            ->join('socios_membresias', 'socios.id', '=', 'socios_membresias.id_socio')
            ->select(
                'socios.id',
                'socios.nombre',
                'socios.apellido_p',
                'socios.apellido_m',
                'socios_membresias.clave_membresia',
                'socios_membresias.estado',
            )
            ->get();
        //Agregamos el encabezado
        $final[] = [
            'id' => 'ID',
            'nombre' =>  'NOMBRE',
            'clave' => 'CLAVE MEMBRESIA',
            'estado' => 'ESTADO',
            'locker' => 'LOCKERS',
            'resguardo' => 'RESGUARDO CARRITO',
            'membresia' => 'MEMBRESIA',
        ];
        //Buscamos las cuotas que son de locker
        $cuotas_locker = Cuota::where('tipo', 'like', '%LOC%')->get()->toArray();
        //Buscamos las cuotas que son de resguardo-carrito
        $cuotas_resg_carr = Cuota::where('tipo', 'like', '%RES%')->get()->toArray();
        //Buscamos las cuotas de membresia
        $cuotas_membresias = Cuota::whereNotNull('clave_membresia')->get()->toArray();


        foreach ($socios as $socio) {
            $cuotas = SocioCuota::with('cuota')->where('id_socio', $socio->id)->get();

            $final[] = [
                'id' => $socio->id,
                'nombre' =>  $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m,
                'clave' => $socio->clave_membresia,
                'estado' => $socio->estado,
                'locker' => $this->sumar_cuotas(array_column($cuotas_locker, 'id'), $cuotas),
                'resguardo' => $this->sumar_cuotas(array_column($cuotas_resg_carr, 'id'), $cuotas),
                'membresia' => $this->sumar_cuotas(array_column($cuotas_membresias, 'id'), $cuotas),
            ];
        }
        //Retornamos el array listo
        return $final;
    }

    private function sumar_cuotas(array $id_cuotas_permitidas, $cuotas)
    {
        $suma = 0;
        $cuotas_filtradas = $cuotas->whereIn('id_cuota', $id_cuotas_permitidas);

        foreach ($cuotas_filtradas as $filtrada) {
            $suma += $filtrada->cuota->monto;
        }
        return $suma;
    }
}
