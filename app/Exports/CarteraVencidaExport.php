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
        $sociosTemp = Socio::with(['socioMembresia', 'cuotasMembresia.cuota'])->get()->toArray();


        //Si queremos la cartera con los socios cancelados
        if ($cancelados) {
            $this->socios = $sociosTemp;
        } else {
            //de lo contrario, excluir solo a los socios con TODAS sus membresias canceladas
            $this->socios = array_filter($sociosTemp, function ($socio) {
                return !$this->todasCanceladas($socio);
            });
        }
    }

    //Determina si el socio tiene todas sus membresias canceladas (principal CAN y sin adicionales activas)
    private function todasCanceladas($socio): bool
    {
        $principal = $socio['socio_membresia'] ?? null;
        $tieneAdicionales = !empty($socio['cuotas_membresia']);

        //Sin membresia principal: no esta cancelado por una membresia CAN
        if (is_null($principal)) {
            return false;
        }
        //Cancelado solo si la principal es CAN y no quedan membresias adicionales activas
        return $principal['estado'] === 'CAN' && !$tieneAdicionales;
    }


    public function array(): array
    {
        //Definimos los titulos de los encabezados a la tabla
        $encabezados = [
            'NO.SOCIO' => 'NO.SOCIO',
            'NOMBRE' => 'NOMBRE',
            'MEMBRESIAS' => 'MEMBRESIAS',
            'NOTAS VENTAS' => 'NOTAS VENTAS',
        ];
        //REFERENNCIA A LA VARIABLE ENCABEZADOS
        foreach ($this->cuotas as $cuota) {
            $encabezados[$cuota->id] = $cuota->descripcion;
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
                    'MEMBRESIAS' => $this->listarMembresias($socio),
                    'NOTAS VENTAS' => $total_deuda,
                ];
                foreach ($this->cuotas as $cuota) {
                    $totalCuota = $this->obtenerTotalCuota($estado_cuenta, $cuota->id);
                    $row_aux[$cuota->id] = $totalCuota;
                    $total_deuda += $totalCuota;
                }
                if ($total_deuda)
                    $data[] = $row_aux;
            }
        }

        return $data;
    }

    /**
     * Obtiene el acumulado de las notas pendientes segun la fecha limite.
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

    //Lista todas las membresias del socio (principal + adicionales) separadas por comas (RF 5)
    private function listarMembresias($socio): string
    {
        $claves = collect();
        //Membresia principal desde socios_membresias
        if (!empty($socio['socio_membresia']['clave_membresia'])) {
            $claves->push($socio['socio_membresia']['clave_membresia']);
        }
        //Membresias adicionales desde socios_cuotas
        foreach ($socio['cuotas_membresia'] ?? [] as $sc) {
            if (!empty($sc['cuota']['clave_membresia']) && $sc['cuota']['clave_membresia'] !== 'N/A') {
                $claves->push($sc['cuota']['clave_membresia']);
            }
        }
        return $claves->unique()->implode(', ');
    }
}
