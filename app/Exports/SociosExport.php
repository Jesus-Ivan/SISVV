<?php

namespace App\Exports;

use App\Models\Cuota;
use App\Models\IntegrantesSocio;
use App\Models\Socio;
use App\Models\SocioCuota;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;

class SociosExport implements FromArray
{

    public function __construct() {}

    public function array(): array
    {
        //Un socio por fila — sin JOIN para evitar duplicados con múltiples membresías
        $socios = Socio::with('socioMembresias')->whereHas('socioMembresia')->get();
        //Agregamos el encabezado
        $final[] = [
            'id' => 'ID',
            'nombre' =>  'NOMBRE',
            'estado' => 'ESTADO',
            'membresias' => 'MEMBRESIAS CONTRATADAS',
            'tarifa_especial' => 'TARIFA PERSONALIZADA',
            'locker' => 'LOCKERS',
            'resguardo' => 'RESGUARDO CARRITO',
            'membresia' => 'MEMBRESIA',
            'integrantes' => 'INTEGRANTES',
        ];
        //Buscamos las cuotas que son de locker
        $cuotas_locker = Cuota::where('tipo', 'like', '%LOC%')->get()->toArray();
        //Buscamos las cuotas que son de resguardo-carrito
        $cuotas_resg_carr = Cuota::where('tipo', 'like', '%RES%')->get()->toArray();
        //Buscamos las cuotas de membresia
        $cuotas_membresias = Cuota::whereNotNull('clave_membresia')->get()->toArray();

        DB::transaction(function () use ($cuotas_locker, $cuotas_resg_carr, $cuotas_membresias, $socios, &$final) {
            foreach ($socios as $socio) {
                $cuotas = SocioCuota::with('cuota')->where('id_socio', $socio->id)->get();
                $integrantes = IntegrantesSocio::where('id_socio', $socio->id)->get();

                $final[] = [
                    'id' => $socio->id,
                    'nombre' =>  $socio->nombre . ' ' . $socio->apellido_p . ' ' . $socio->apellido_m,
                    'estado' => $socio->socioMembresias->pluck('estado')->implode(', '),
                    'membresias' => $this->listarMembresias($socio),
                    'tarifa_especial' => $cuotas->whereNotNull('monto_personalizado')->isNotEmpty() ? 'SÍ' : 'NO',
                    'locker' => $this->sumar_cuotas(array_column($cuotas_locker, 'id'), $cuotas),
                    'resguardo' => $this->sumar_cuotas(array_column($cuotas_resg_carr, 'id'), $cuotas),
                    'membresia' => $this->sumar_cuotas(array_column($cuotas_membresias, 'id'), $cuotas),
                    'integrantes' => count($integrantes),
                ];
            }
        }, 2);
        //Retornamos el array listo
        return $final;
    }

    private function sumar_cuotas(array $id_cuotas_permitidas, $cuotas)
    {
        $suma = 0;
        $cuotas_filtradas = $cuotas->whereIn('id_cuota', $id_cuotas_permitidas);

        foreach ($cuotas_filtradas as $filtrada) {
            //Usar monto_a_cobrar para reflejar tarifas personalizadas (RF 4 / RF 5)
            $suma += $filtrada->monto_a_cobrar;
        }
        return $suma;
    }

    //Lista todas las membresías del socio desde socios_membresias separadas por comas (RF 5)
    private function listarMembresias(Socio $socio): string
    {
        return $socio->socioMembresias
            ->pluck('clave_membresia')
            ->unique()
            ->implode(', ');
    }
}
