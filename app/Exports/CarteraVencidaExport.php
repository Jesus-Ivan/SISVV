<?php

namespace App\Exports;

use App\Models\Cuota;
use App\Models\EstadoCuenta;
use App\Models\Socio;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;

class CarteraVencidaExport implements FromArray
{
    public $cuotas;
    public $socios;

    public function __construct()
    {
        $this->cuotas = Cuota::all();
        $sociosTemp = Socio::with('socioMembresia')->get()->toArray();
        $this->socios = array_filter($sociosTemp, function ($socio) {
            return $socio['socio_membresia']['estado'] != 'CAN';
        });
        //dd($this->socios);
    }


    public function array(): array
    {
        //Definimos los titulos de los encabezados a la tabla
        $encabezados = [
            'NO.SOCIO' => 'NO.SOCIO',
            'NOMBRE' => 'NOMBRE',
            'NOTAS VENTAS' => 'NOTAS VENTAS',
        ];
        //REFERENNCIA A LA VARIABLE ENCABEZADOS
        foreach ($this->cuotas as $key => $cuota) {
            $encabezados[$key] = $cuota->descripcion;
        }

        //array auxiliar
        $data = [];

        //Agregamos los encabezados al array
        $data[] = $encabezados;

        //Ingresamos los valores al array
        foreach ($this->socios as $key => $socio) {
            $estado_cuenta = EstadoCuenta::where('id_socio', $socio['id'])
                ->where('saldo', '>',  0)->get();
            //SI no hay ningun concepto que deba, omitir iteracion
            if (! count($estado_cuenta)) {
                continue;
            }
            $row_aux = [
                'NO.SOCIO' => $socio['id'],
                'NOMBRE' => $socio['nombre'] . ' ' . $socio['apellido_p'] . ' ' . $socio['apellido_m'],
                'NOTAS VENTAS' => $this->obtenerNotas($estado_cuenta, "NOTA"),
            ];

            foreach ($this->cuotas as $key => $cuota) {
                $row_aux[$key] = $this->obtenerTotalCuota($estado_cuenta, $key + 1);
            }


            $data[] = $row_aux;
        }

        //dd($data);
        return $data;
    }

    private function obtenerNotas($estado_cuenta, $exp_reg)
    {
        $patron = "/$exp_reg/i";
        $acu = 0;
        $hoy = now();
        foreach ($estado_cuenta as $key => $row) {
            if (preg_match($patron, $row->concepto)) {
                $fecha_concepto = Carbon::parse($row->fecha);
                if ($fecha_concepto->year <= $hoy->year && $fecha_concepto->month < $hoy->month) {
                    $acu += $row->saldo;
                }
            }
        }
        return $acu;
    }

    private function obtenerTotalCuota($estado_cuenta, $id_cuota)
    {
        $acu = 0;
        foreach ($estado_cuenta as $key => $row) {
            if ($row->id_cuota == $id_cuota) {
                $acu += $row->saldo;
            }
        }
        return $acu;
    }
}
