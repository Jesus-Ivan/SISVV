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
    public $consumosMesFin;
    public $fecha_limite;

    public function __construct($consumosMesFin, $cancelados, $fecha_limite)
    {
        $this->cuotas = Cuota::all();
        $this->consumosMesFin = $consumosMesFin;
        $this->fecha_limite = $fecha_limite;
        $sociosTemp = Socio::with('socioMembresia')->get()->toArray();


        //Si queremos la cartera con los socios cancelados
        if ($cancelados) {
            $this->socios = $sociosTemp;
        } else {
            //de lo contrario, filtrar los socios cancelados
            $this->socios = array_filter($sociosTemp, function ($socio) {
                return $socio['socio_membresia']['estado'] != 'CAN';
            });
        }
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
        foreach ($this->socios as $socio) {
            //Buscar el estado de cuenta
            $estado_cuenta = EstadoCuenta::where('id_socio', $socio['id'])
                ->where('saldo', '>',  0)
                ->whereDate('fecha', '<=', $this->fecha_limite)
                ->get();

            //Si debe algun concepto
            if (count($estado_cuenta)) {
                $total_deuda = $this->obtenerNotas($estado_cuenta, "NOTA");

                $row_aux = [
                    'NO.SOCIO' => $socio['id'],
                    'NOMBRE' => $socio['nombre'] . ' ' . $socio['apellido_p'] . ' ' . $socio['apellido_m'],
                    'NOTAS VENTAS' => $total_deuda,
                ];
                foreach ($this->cuotas as $key => $cuota) {
                    $totalCuota = $this->obtenerTotalCuota($estado_cuenta, $key + 1);
                    $row_aux[$key] = $totalCuota;
                    $total_deuda += $totalCuota;
                }
                if ($total_deuda)
                    $data[] = $row_aux;
            }
        }

        return $data;
    }

    /**
     * Obtiene el acumulado de las notas pendientes.
     * Segun la fecha limite
     */
    public function obtenerNotas($estado_cuenta, $exp_reg)
    {
        $patron = "/$exp_reg/i";
        $acu = 0;
        $fecha_limite = Carbon::parse($this->fecha_limite);

        foreach ($estado_cuenta as $key => $row) {
            if (preg_match($patron, $row->concepto) &&  is_null($row->id_cuota)) {
                $fecha_concepto = Carbon::parse($row->fecha);
                //Si la opcion de incluir consumos esta activada
                if ($this->consumosMesFin) {
                    $acu += $row->saldo;
                } elseif ($fecha_concepto->year < $fecha_limite->year) {
                    $acu += $row->saldo;
                } elseif ($fecha_concepto->year == $fecha_limite->year && $fecha_concepto->month < $fecha_limite->month) {
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
